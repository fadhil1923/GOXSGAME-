<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Room;
use App\Models\RoomSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameSessionTest extends TestCase
{
    use RefreshDatabase;

    private function makeGameWithQuestions(int $count = 3): Game
    {
        $game = Game::factory()->create();

        for ($i = 0; $i < $count; $i++) {
            $question = Question::factory()->create([
                'game_id' => $game->id,
                'order' => $i,
                'time_limit_seconds' => 30,
                'points' => 100,
            ]);
            QuestionOption::create(['question_id' => $question->id, 'option_text' => 'Benar', 'is_correct' => true]);
            QuestionOption::create(['question_id' => $question->id, 'option_text' => 'Salah', 'is_correct' => false]);
        }

        return $game->fresh();
    }

    #[Test]
    public function teacher_can_start_game(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = $this->makeGameWithQuestions();
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('teacher.rooms.play', $room));

        $response->assertRedirect();
        $this->assertDatabaseHas('room_sessions', [
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
        ]);
    }

    #[Test]
    public function teacher_can_advance_to_next_question(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = $this->makeGameWithQuestions(3);
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);

        $this->actingAs($teacher)->post(route('teacher.rooms.play', $room));

        $this->actingAs($teacher)->post(route('teacher.rooms.next', $room));

        $this->assertDatabaseHas('room_sessions', [
            'room_id' => $room->id,
            'current_question_index' => 1,
        ]);
    }

    #[Test]
    public function teacher_rooms_next_is_exempt_from_csrf_verification(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = $this->makeGameWithQuestions(3);
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);

        $this->actingAs($teacher)->post(route('teacher.rooms.play', $room));

        // Panggil POST tanpa token CSRF dengan middleware aktif
        $response = $this->actingAs($teacher)
            ->withMiddleware()
            ->post(route('teacher.rooms.next', $room));

        $response->assertStatus(302);
        $response->assertRedirect(route('teacher.rooms.lobby', $room));
        $this->assertDatabaseHas('room_sessions', [
            'room_id' => $room->id,
            'current_question_index' => 1,
        ]);
    }

    #[Test]
    public function game_finishes_after_last_question(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = $this->makeGameWithQuestions(1);
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);

        $this->actingAs($teacher)->post(route('teacher.rooms.play', $room));

        // Satu soal → next langsung finish
        $this->actingAs($teacher)->post(route('teacher.rooms.next', $room));

        $this->assertDatabaseHas('room_sessions', [
            'room_id' => $room->id,
            'status' => 'finished',
        ]);
    }

    #[Test]
    public function student_state_returns_playing_when_game_is_active(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $game = $this->makeGameWithQuestions();
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        // Buat session aktif
        RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        $response = $this->actingAs($student)
            ->getJson(route('student.room.state', $room));

        $response->assertOk()
            ->assertJsonPath('status', 'playing')
            ->assertJsonStructure(['question' => ['id', 'text', 'options']]);
    }

    #[Test]
    public function student_can_submit_correct_answer(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $game = $this->makeGameWithQuestions();
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        $session = RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        $question = $game->questions()->orderBy('order')->first();
        $correctOption = $question->options()->where('is_correct', true)->first();

        $response = $this->actingAs($student)->postJson(route('student.room.answer', $room), [
            'question_id' => $question->id,
            'option_id' => $correctOption->id,
        ]);

        $response->assertOk()->assertJsonPath('is_correct', true);
        $this->assertDatabaseHas('student_answers', [
            'room_session_id' => $session->id,
            'user_id' => $student->id,
            'is_correct' => true,
        ]);
    }

    #[Test]
    public function student_cannot_answer_twice(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $game = $this->makeGameWithQuestions();
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        $question = $game->questions()->orderBy('order')->first();
        $option = $question->options()->first();

        $this->actingAs($student)->postJson(route('student.room.answer', $room), [
            'question_id' => $question->id,
            'option_id' => $option->id,
        ]);

        $response = $this->actingAs($student)->postJson(route('student.room.answer', $room), [
            'question_id' => $question->id,
            'option_id' => $option->id,
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function teacher_cannot_start_game_with_no_questions(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create(); // no questions
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('teacher.rooms.play', $room));

        $response->assertStatus(422);
        $this->assertDatabaseMissing('room_sessions', ['room_id' => $room->id]);
    }

    #[Test]
    public function question_automatically_advances_when_time_expires(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $game = $this->makeGameWithQuestions(2);
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now()->subSeconds(35),
            'current_time_limit' => 30,
        ]);

        $response = $this->actingAs($student)
            ->getJson(route('student.room.state', $room));

        $response->assertOk()
            ->assertJsonPath('status', 'playing')
            ->assertJsonPath('question_index', 1);

        $this->assertDatabaseHas('room_sessions', [
            'room_id' => $room->id,
            'current_question_index' => 1,
            'status' => 'playing',
        ]);
    }

    #[Test]
    public function game_automatically_finishes_when_time_expires_on_last_question(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $game = $this->makeGameWithQuestions(1);
        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now()->subSeconds(35),
            'current_time_limit' => 30,
        ]);

        $response = $this->actingAs($student)
            ->getJson(route('student.room.state', $room));

        $response->assertOk()
            ->assertJsonPath('status', 'finished');

        $this->assertDatabaseHas('room_sessions', [
            'room_id' => $room->id,
            'status' => 'finished',
        ]);
    }
}
