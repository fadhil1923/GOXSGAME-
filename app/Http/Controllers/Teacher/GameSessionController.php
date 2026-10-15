<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomSession;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\ComponentAttributeBag;

class GameSessionController extends Controller
{
    /**
     * Lobby guru: lihat siswa yang join + tombol START.
     */
    public function lobby(Room $room)
    {
        Gate::authorize('update', $room);

        $room->load('game.questions.options', 'students', 'session');

        return view('teacher.rooms.lobby', compact('room'));
    }

    /**
     * POST: Mulai game — buat RoomSession, set soal pertama.
     */
    public function play(Request $request, Room $room)
    {
        Gate::authorize('update', $room);
        abort_if($room->game->questions->isEmpty(), 422, 'Game ini belum memiliki soal.');

        $room->load('game.questions', 'session');

        $firstQuestion = $room->game->questions->first();
        $timeLimit = $request->filled('custom_time')
            ? (int) $request->input('custom_time')
            : ($room->custom_time_per_question ?? $firstQuestion->time_limit_seconds);

        if ($room->session) {
            // Reset sesi yang sudah ada
            $room->session->update([
                'status' => 'playing',
                'current_question_index' => 0,
                'question_started_at' => Carbon::now(),
                'current_time_limit' => $timeLimit,
            ]);
            // Hapus jawaban sesi lama jika game di-restart
            StudentAnswer::where('room_session_id', $room->session->id)->delete();
            // Reset powerup 50:50 di room_user
            DB::table('room_user')->where('room_id', $room->id)->update(['has_used_5050' => false]);
        } else {
            RoomSession::create([
                'room_id' => $room->id,
                'status' => 'playing',
                'current_question_index' => 0,
                'question_started_at' => Carbon::now(),
                'current_time_limit' => $timeLimit,
            ]);
        }

        return redirect()->route('teacher.rooms.lobby', $room)
            ->with('success', 'Game dimulai!');
    }

    /**
     * POST: Pindah ke soal berikutnya.
     */
    public function next(Request $request, Room $room)
    {
        Gate::authorize('update', $room);

        $room->load('game.questions', 'session');
        $session = $room->session;
        abort_if(! $session || ! $session->isPlaying(), 422, 'Game belum berjalan.');

        $totalQuestions = $room->game->questions->count();
        $nextIndex = $session->current_question_index + 1;

        if ($nextIndex >= $totalQuestions) {
            // Tidak ada soal berikutnya → selesaikan game
            $session->update(['status' => 'finished']);

            return redirect()->route('teacher.rooms.lobby', $room)
                ->with('success', 'Game selesai! Leaderboard siap.');
        }

        $nextQuestion = $room->game->questions->get($nextIndex);
        $timeLimit = $room->custom_time_per_question ?? $nextQuestion->time_limit_seconds;

        $session->update([
            'current_question_index' => $nextIndex,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => $timeLimit,
        ]);

        return redirect()->route('teacher.rooms.lobby', $room);
    }

    /**
     * POST: Akhiri game secara paksa.
     */
    public function end(Request $request, Room $room)
    {
        Gate::authorize('update', $room);

        $room->session?->update(['status' => 'finished']);

        return redirect()->route('teacher.rooms.lobby', $room)
            ->with('success', 'Game dihentikan.');
    }

    /**
     * GET: Leaderboard hasil akhir per room session.
     */
    public function results(Room $room)
    {
        Gate::authorize('update', $room);

        $room->load('session', 'game');
        abort_if(! $room->session, 404);

        $leaderboard = StudentAnswer::where('room_session_id', $room->session->id)
            ->with('user')
            ->selectRaw('user_id, SUM(score_earned) as total_score, COUNT(*) as answered, SUM(is_correct) as correct')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->get();

        return view('teacher.rooms.results', compact('room', 'leaderboard'));
    }

    /**
     * GET JSON — daftar siswa yang sudah join room (untuk polling di lobby guru).
     */
    public function students(Room $room)
    {
        Gate::authorize('update', $room);

        $students = $room->students()
            ->orderBy('room_user.created_at')
            ->get(['users.id', 'users.name', 'users.email', 'users.avatar', 'room_user.team']);

        return response()->json([
            'count' => $students->count(),
            'students' => $students->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'email' => $s->email,
                'team' => $s->pivot?->team,
                'avatar_html' => view('components.avatar', [
                    'code' => $s->avatar,
                    'size' => 'sm',
                    'attributes' => new ComponentAttributeBag([]),
                ])->render(),
                'initial' => strtoupper(substr($s->name, 0, 1)),
            ]),
        ]);
    }

    /**
     * GET JSON — Realtime leaderboard data for the current game session.
     */
    public function leaderboardData(Room $room)
    {
        Gate::authorize('update', $room);

        $room->load('session');
        if (! $room->session) {
            return response()->json(['leaderboard' => []]);
        }

        $leaderboard = StudentAnswer::where('room_session_id', $room->session->id)
            ->with('user')
            ->selectRaw('user_id, SUM(score_earned) as total_score, COUNT(*) as answered, SUM(is_correct) as correct')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->get();

        return response()->json([
            'leaderboard' => $leaderboard->map(fn ($row) => [
                'id' => $row->user->id ?? 0,
                'name' => $row->user->name ?? 'Unknown',
                'score' => (int) $row->total_score,
                'correct' => (int) $row->correct,
                'answered' => (int) $row->answered,
            ]),
        ]);
    }
}
