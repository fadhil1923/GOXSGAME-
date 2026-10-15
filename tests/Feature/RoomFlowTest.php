<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoomFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function teacher_can_create_a_room(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create();

        $response = $this->actingAs($teacher)->post(route('teacher.rooms.store'), [
            'game_id' => $game->id,
        ]);

        $this->assertDatabaseCount('rooms', 1);

        $room = Room::first();
        $response->assertRedirect(route('teacher.rooms.show', $room));
        $this->assertEquals($teacher->id, $room->user_id);
        $this->assertEquals($game->id, $room->game_id);
        $this->assertEquals('active', $room->status);
        $this->assertNotEmpty($room->room_code);
    }

    #[Test]
    public function student_can_join_room_with_correct_pin(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $game = Game::factory()->create();

        $room = Room::factory()->create([
            'game_id' => $game->id,
            'user_id' => $teacher->id,
            'room_code' => 'ABC123',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student)->post(route('student.enter'), [
            'pin' => 'ABC123',
            'name' => 'Luffy',
            'avatar' => 'https://example.com/luffy.png',
        ]);

        $response->assertRedirect(route('student.room', $room));
        $this->assertTrue($room->students()->where('user_id', $student->id)->exists());
    }

    #[Test]
    public function student_cannot_join_with_wrong_pin(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->post(route('student.enter'), [
            'pin' => 'WRONG1',
            'name' => 'Luffy',
            'avatar' => 'https://example.com/luffy.png',
        ]);

        $response->assertSessionHasErrors('pin');
    }

    #[Test]
    public function student_joining_twice_is_not_duplicated(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $room = Room::factory()->create(['room_code' => 'XXXXXX', 'status' => 'active']);

        $room->students()->attach($student->id);

        $this->actingAs($student)->post(route('student.enter'), [
            'pin' => 'XXXXXX',
            'name' => 'Luffy',
            'avatar' => 'https://example.com/luffy.png',
        ]);

        $this->assertCount(1, $room->students()->where('user_id', $student->id)->get());
    }

    #[Test]
    public function teacher_cannot_access_admin_routes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('admin.games.index'))
            ->assertForbidden();
    }

    #[Test]
    public function student_cannot_access_teacher_routes(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('teacher.rooms.index'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_cannot_access_teacher_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('teacher.rooms.index'))
            ->assertForbidden();
    }
}
