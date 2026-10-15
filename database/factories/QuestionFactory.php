<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'question_text' => fake()->sentence().'?',
            'type' => 'multiple_choice',
            'time_limit_seconds' => fake()->randomElement([15, 20, 30, 45]),
            'points' => fake()->randomElement([50, 100, 150, 200]),
            'order' => 0,
        ];
    }
}
