<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
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
            'user_id' => User::factory()->create(['role' => 'teacher'])->id,
            'room_code' => strtoupper(Str::random(6)),
            'status' => 'active',
        ];
    }
}
