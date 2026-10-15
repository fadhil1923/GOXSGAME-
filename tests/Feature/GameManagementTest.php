<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function creating_game_redirects_directly_to_question_management(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->post(route('manage.games.store'), [
            'title' => 'Kuis Matematika Seru',
            'description' => 'Belajar penjumlahan dan pengurangan cepat',
            'xp_reward' => 100,
            'difficulty' => 'Easy',
            'grade_level' => 'Kelas 1',
        ]);

        $game = Game::where('title', 'Kuis Matematika Seru')->first();
        $this->assertNotNull($game);

        $response->assertRedirect(route('manage.games.questions.index', $game));
        $response->assertSessionHas('success', 'Game berhasil disimpan! Silakan tambahkan soal untuk game ini.');
    }

    #[Test]
    public function accessing_game_show_redirects_to_question_management(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create(['user_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->get(route('manage.games.show', $game));

        $response->assertRedirect(route('manage.games.questions.index', $game));
    }

    #[Test]
    public function teacher_can_generate_global_music_questions_by_topic(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create(['user_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('manage.games.questions.ai_generate.store', $game), [
            'keyword' => 'Tebak Lagu Taylor Swift',
            'count' => 3,
            'question_type' => 'audio',
        ]);

        $response->assertRedirect(route('manage.games.questions.index', $game));

        $questions = $game->questions()->where('type', 'audio')->get();
        $this->assertCount(3, $questions);

        foreach ($questions as $question) {
            $this->assertEquals('audio', $question->type);
            $this->assertNotEmpty($question->media_url);
            $this->assertCount(4, $question->options);
            $this->assertEquals(1, $question->options()->where('is_correct', true)->count());
        }
    }

    #[Test]
    public function teacher_can_generate_global_image_questions_by_topic(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create(['user_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('manage.games.questions.ai_generate.store', $game), [
            'keyword' => 'Taylor Swift',
            'count' => 3,
            'question_type' => 'image',
        ]);

        $response->assertRedirect(route('manage.games.questions.index', $game));

        $questions = $game->questions()->where('type', 'image')->get();
        $this->assertCount(3, $questions);

        foreach ($questions as $question) {
            $this->assertEquals('image', $question->type);
            $this->assertNotEmpty($question->media_url);
            $this->assertCount(4, $question->options);
            $this->assertEquals(1, $question->options()->where('is_correct', true)->count());
        }
    }

    #[Test]
    public function teacher_can_generate_global_mixed_questions_by_topic(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create(['user_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('manage.games.questions.ai_generate.store', $game), [
            'keyword' => 'Taylor Swift',
            'count' => 4,
            'question_type' => 'mixed',
        ]);

        $response->assertRedirect(route('manage.games.questions.index', $game));

        $questions = $game->questions()->get();
        $this->assertCount(4, $questions);

        $types = $questions->pluck('type')->unique();
        // Should contain multiple diverse types
        $this->assertTrue($types->count() >= 2);
    }
}
