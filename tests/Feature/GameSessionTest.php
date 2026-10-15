<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Room;
use App\Models\RoomSession;
use App\Models\StudentAnswer;
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

    #[Test]
    public function game_advances_immediately_when_all_students_have_answered(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);

        $game = $this->makeGameWithQuestions(2);
        $firstQuestion = $game->questions()->orderBy('order')->first();
        $option = $firstQuestion->options()->first();

        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach([$student1->id, $student2->id]);

        $session = RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        // Siswa 1 menjawab -> masih menunggu siswa 2
        $res1 = $this->actingAs($student1)->postJson(route('student.room.answer', $room), [
            'question_id' => $firstQuestion->id,
            'option_id' => $option->id,
        ]);
        $res1->assertOk()->assertJsonPath('auto_advanced', false);

        $session->refresh();
        $this->assertEquals(0, $session->current_question_index);

        // Siswa 2 menjawab -> semua siswa aktif sudah menjawab, harus langsung pindah!
        $res2 = $this->actingAs($student2)->postJson(route('student.room.answer', $room), [
            'question_id' => $firstQuestion->id,
            'option_id' => $option->id,
        ]);
        $res2->assertOk()->assertJsonPath('auto_advanced', true);

        $session->refresh();
        $this->assertEquals(1, $session->current_question_index);
    }

    #[Test]
    public function survival_mode_auto_advances_when_all_remaining_active_students_have_answered(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $eliminatedStudent = User::factory()->create(['role' => 'student']);
        $activeStudent = User::factory()->create(['role' => 'student']);

        // 4 soal: soal 0, 1, 2 dijawab salah oleh eliminatedStudent, soal 3 yang sedang aktif
        $game = $this->makeGameWithQuestions(4);
        $questions = $game->questions()->orderBy('order')->get();
        $q3 = $questions[3];
        $option = $q3->options()->first();

        $room = Room::factory()->create([
            'game_id' => $game->id,
            'user_id' => $teacher->id,
            'game_mode' => 'survival',
        ]);
        $room->students()->attach([$eliminatedStudent->id, $activeStudent->id]);

        $session = RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 3,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        // Simulasikan eliminatedStudent sudah salah 3x di soal 0, 1, 2
        for ($i = 0; $i < 3; $i++) {
            StudentAnswer::create([
                'room_session_id' => $session->id,
                'user_id' => $eliminatedStudent->id,
                'question_id' => $questions[$i]->id,
                'question_option_id' => $questions[$i]->options()->where('is_correct', false)->first()->id,
                'answered_at' => Carbon::now(),
                'is_correct' => false,
                'score_earned' => 0,
            ]);
        }

        // Sekarang hanya activeStudent yang masih hidup. Begitu activeStudent menjawab soal 3, game harus langsung finish!
        $res = $this->actingAs($activeStudent)->postJson(route('student.room.answer', $room), [
            'question_id' => $q3->id,
            'option_id' => $option->id,
        ]);
        $res->assertOk()->assertJsonPath('auto_advanced', true);

        $session->refresh();
        $this->assertEquals('finished', $session->status);
    }

    #[Test]
    public function game_finishes_immediately_when_all_students_answer_last_question(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $game = $this->makeGameWithQuestions(1);
        $lastQuestion = $game->questions()->first();
        $option = $lastQuestion->options()->first();

        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        $session = RoomSession::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_question_index' => 0,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        $res = $this->actingAs($student)->postJson(route('student.room.answer', $room), [
            'question_id' => $lastQuestion->id,
            'option_id' => $option->id,
        ]);

        $res->assertOk()->assertJsonPath('auto_advanced', true);

        $session->refresh();
        $this->assertEquals('finished', $session->status);
    }

    #[Test]
    public function student_can_view_answer_history_and_review_on_result_page(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $game = $this->makeGameWithQuestions(2);
        $questions = $game->questions()->orderBy('order')->get();
        $q0 = $questions[0];
        $q1 = $questions[1];

        $q0Correct = $q0->options()->where('is_correct', true)->first();
        $q1Wrong = $q1->options()->where('is_correct', false)->first();

        $room = Room::factory()->create(['game_id' => $game->id, 'user_id' => $teacher->id]);
        $room->students()->attach($student->id);

        $session = RoomSession::create([
            'room_id' => $room->id,
            'status' => 'finished',
            'current_question_index' => 1,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => 30,
        ]);

        // Jawab soal 0 dengan benar
        StudentAnswer::create([
            'room_session_id' => $session->id,
            'user_id' => $student->id,
            'question_id' => $q0->id,
            'question_option_id' => $q0Correct->id,
            'answered_at' => Carbon::now(),
            'is_correct' => true,
            'score_earned' => 100,
        ]);

        // Jawab soal 1 dengan salah
        StudentAnswer::create([
            'room_session_id' => $session->id,
            'user_id' => $student->id,
            'question_id' => $q1->id,
            'question_option_id' => $q1Wrong->id,
            'answered_at' => Carbon::now(),
            'is_correct' => false,
            'score_earned' => 0,
        ]);

        $response = $this->actingAs($student)->get(route('student.result', $room));

        $response->assertOk()
            ->assertSeeText('Riwayat & Pembahasan Soal')
            ->assertSee('1 Benar')
            ->assertSee('1 Salah')
            ->assertViewHas('reviewItems')
            ->assertViewHas('summary', function ($summary) {
                return $summary['total'] === 2
                    && $summary['correct'] === 1
                    && $summary['wrong'] === 1
                    && $summary['unanswered'] === 0
                    && $summary['accuracy'] === 50;
            });
    }
}
