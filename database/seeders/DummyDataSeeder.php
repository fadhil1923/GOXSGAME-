<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Room;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cegah duplikasi data jika seeder dijalankan lebih dari satu kali
        if (User::where('email', 'admin@goxsgame.com')->exists()) {
            return;
        }

        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@goxsgame.com',
            'role' => 'admin',
        ]);

        // Teacher
        $teacher = User::factory()->create([
            'name' => 'Pak Fadhil',
            'email' => 'teacher@goxsgame.com',
            'role' => 'teacher',
        ]);

        // Students
        $student = User::factory()->create([
            'name' => 'Reyhan Pratama',
            'email' => 'student@goxsgame.com',
            'role' => 'student',
        ]);

        $student2 = User::factory()->create([
            'name' => 'Arya Nugroho',
            'email' => 'student2@goxsgame.com',
            'role' => 'student',
        ]);

        // Subjects
        $subjects = [
            ['name' => 'Matematika', 'icon' => 'calculate', 'color' => '#3B82F6'], // blue
            ['name' => 'IPA', 'icon' => 'science', 'color' => '#10B981'], // green
            ['name' => 'IPS', 'icon' => 'public', 'color' => '#F59E0B'], // yellow
            ['name' => 'Bahasa Indonesia', 'icon' => 'menu_book', 'color' => '#EF4444'], // red
            ['name' => 'Bahasa Inggris', 'icon' => 'language', 'color' => '#8B5CF6'], // purple
            ['name' => 'Coding', 'icon' => 'code', 'color' => '#14B8A6'], // teal
        ];

        $subjectIds = [];
        foreach ($subjects as $s) {
            $created = Subject::create($s);
            $subjectIds[$s['name']] = $created->id;
        }

        // ===================== GAME 1 =====================
        $game = Game::create([
            'title' => 'Aljabar Obby: Escape Fraction Temple',
            'description' => 'Lompati balok pecahan magma, selesaikan teka-teki persamaan linear real-time bersama seluruh kelas.',
            'thumbnail_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBXedawVQrvKE5L_n3eHOueARUTeUGFAOlZFiJSgRthR0fcnFSsS-MdjMg5nWMFD8E8J9iw26SIiBfjdaMO0BCh-W3ilNThlzNnS0NxgWI_sp6fTxmfSCFjVhtGsB_-uxsRIl6AKkCjTvQen85QQAEcAURvN110CXKZ6fiXHwFFwXfeS4eNRxjgZplFJDodyFBelaGiED95xmP6jWlfUG8lC0i9zEDz6HUXxQHCPbTd4CijMTUIA0M6',
            'game_url' => '#',
            'xp_reward' => 500,
            'user_id' => $teacher->id,
            'subject_id' => $subjectIds['Matematika'],
            'grade_level' => 'Kelas 5',
            'difficulty' => 'Medium',
        ]);

        // Soal-soal untuk game 1
        $questions = [
            [
                'text' => 'Berapakah hasil dari 2/3 + 1/6?',
                'time' => 30,
                'points' => 100,
                'options' => ['1/2', '5/6', '3/9', '1'],
                'correct' => 1,
            ],
            [
                'text' => 'Selesaikan: 3x = 15. Nilai x adalah?',
                'time' => 20,
                'points' => 100,
                'options' => ['3', '12', '5', '45'],
                'correct' => 2,
            ],
            [
                'text' => 'Berapa 1/4 dari 100?',
                'time' => 15,
                'points' => 150,
                'options' => ['40', '20', '50', '25'],
                'correct' => 3,
            ],
            [
                'text' => 'Pecahan mana yang nilainya sama dengan 0.5?',
                'time' => 20,
                'points' => 100,
                'options' => ['1/4', '3/6', '2/5', '1/3'],
                'correct' => 1,
            ],
            [
                'text' => 'Jika x - 4 = 10, maka x = ?',
                'time' => 25,
                'points' => 100,
                'options' => ['6', '14', '40', '4'],
                'correct' => 1,
            ],
        ];

        foreach ($questions as $i => $q) {
            $question = $game->questions()->create([
                'question_text' => $q['text'],
                'time_limit_seconds' => $q['time'],
                'points' => $q['points'],
                'order' => $i,
            ]);

            foreach ($q['options'] as $j => $optText) {
                $question->options()->create([
                    'option_text' => $optText,
                    'is_correct' => $j === $q['correct'],
                ]);
            }
        }

        // ===================== GAME 2 =====================
        Game::create([
            'title' => 'Fraction Runner 3D',
            'description' => 'Lari cepat melintasi portal desimal & ubah pecahan sebelum jebakan lantai roboh!',
            'thumbnail_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuALOE4cad0pGvlVg93QvbAezTkEZ8Ny9F7Md2XKrscMMqztdeFUEmuOjfdgsV2RC6kqhYYgTeIh1RasSL5BuP0f2gGl3BZzwGOaBn-CXYJEClqQC3tKg0R7Xmv_60Uw5zWTVfVPZJNhZePMnEaHmRKfdD0-nwC6XwWpD0EW-zE1l28eLFO7g9jwsPcsHG_Q3Dy7DxtciHf_5YWUhh25o4bKQJtXl9pBzc0cdkJZwIjIh2RBd7mvJ-qH',
            'game_url' => '#',
            'xp_reward' => 150,
            'user_id' => $teacher->id,
            'subject_id' => $subjectIds['Matematika'],
            'grade_level' => 'Kelas 6',
            'difficulty' => 'Hard',
        ]);

        // ===================== ROOM =====================
        $room = Room::create([
            'game_id' => $game->id,
            'user_id' => $teacher->id,
            'room_code' => '729481',
            'status' => 'active',
        ]);

        $room->students()->attach([$student->id, $student2->id]);
    }
}
