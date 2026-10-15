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
}
