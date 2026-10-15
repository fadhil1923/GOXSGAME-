<?php

namespace Tests\Feature;

use App\Models\DevelopmentMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DevelopmentMessageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_feedback_form(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get(route('feedback.create'));

        $response->assertStatus(200);
        $response->assertSee('Pesan Pengembangan');
    }

    #[Test]
    public function teacher_can_submit_feedback_message(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Budi Guru']);

        $response = $this->actingAs($teacher)->post(route('feedback.store'), [
            'category' => 'feature_request',
            'subject' => 'Tambah Mode Battle Royale Soal',
            'message' => 'Mohon tambahkan mode eliminasi live saat kuis berlangsung.',
        ]);

        $response->assertRedirect(route('feedback.create'));
        $this->assertDatabaseHas('development_messages', [
            'user_id' => $teacher->id,
            'name' => 'Budi Guru',
            'role' => 'teacher',
            'category' => 'feature_request',
            'subject' => 'Tambah Mode Battle Royale Soal',
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function student_cannot_access_admin_feedback_page(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('manage.feedback.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function teacher_cannot_access_admin_feedback_page(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get(route('manage.feedback.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_and_manage_feedback_messages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = DevelopmentMessage::create([
            'name' => 'Siti Siswa',
            'email' => 'siti@example.com',
            'role' => 'student',
            'category' => 'bug_report',
            'subject' => 'Tombol powerup tidak merespon di iOS',
            'message' => 'Ketika menekan powerup 50:50 tidak ada efeknya.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('manage.feedback.index'));

        $response->assertStatus(200);
        $response->assertSee('Tombol powerup tidak merespon di iOS');

        // Admin updates status and response note
        $updateResponse = $this->actingAs($admin)->patch(route('manage.feedback.update', $message), [
            'status' => 'in_progress',
            'admin_notes' => 'Sedang diperbaiki pada update patch versi 2.4.1.',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('development_messages', [
            'id' => $message->id,
            'status' => 'in_progress',
            'admin_notes' => 'Sedang diperbaiki pada update patch versi 2.4.1.',
        ]);
    }

    #[Test]
    public function admin_can_delete_feedback_message(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $message = DevelopmentMessage::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'teacher',
            'category' => 'other',
            'subject' => 'Spam message',
            'message' => 'Hapus pesan ini',
            'status' => 'pending',
        ]);

        $deleteResponse = $this->actingAs($admin)->delete(route('manage.feedback.destroy', $message));

        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('development_messages', [
            'id' => $message->id,
        ]);
    }
}
