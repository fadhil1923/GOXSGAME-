<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function dashboard(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $myScore = StudentAnswer::where('user_id', $user->id)->sum('score_earned');
        $myGamesCount = DB::table('student_answers')
            ->where('user_id', $user->id)
            ->distinct('room_session_id')
            ->count('room_session_id');

        $globalLeaderboard = StudentAnswer::with('user')
            ->selectRaw('user_id, SUM(score_earned) as total_score')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit(10)
            ->get();

        return view('student.dashboard', compact('user', 'myScore', 'myGamesCount', 'globalLeaderboard'));
    }

    public function join()
    {
        return view('student.join');
    }

    public function enter(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string', 'size:6'],
            'name' => ['required', 'string', 'max:50'],
            'avatar' => ['required', 'string'],
        ]);

        $room = Room::where('room_code', strtoupper($request->pin))
            ->where('status', 'active')
            ->first();

        if (! $room) {
            return back()->withErrors(['pin' => 'PIN tidak valid atau room sudah tidak aktif.'])->withInput();
        }

        // Cek apakah user sudah login, jika belum buat user sementara (anonymous)
        if (auth()->check()) {
            $user = auth()->user();
            $user->update([
                'name' => $request->name,
                'avatar' => $request->avatar,
            ]);
        } else {
            // Buat akun sementara untuk siswa ini
            $user = User::create([
                'name' => $request->name,
                'email' => 'guest_'.uniqid().'@goxsgrow.local',
                'password' => bcrypt(uniqid()),
                'role' => 'student',
                'avatar' => $request->avatar,
            ]);
            // Langsung login-kan
            auth()->login($user);
        }

        if (! $room->students()->where('user_id', $user->id)->exists()) {
            if ($room->game_mode === 'team') {
                $teams = ['Tim Merah', 'Tim Biru', 'Tim Hijau', 'Tim Kuning'];
                $teamCounts = DB::table('room_user')
                    ->where('room_id', $room->id)
                    ->select('team', DB::raw('count(*) as total'))
                    ->groupBy('team')
                    ->pluck('total', 'team')
                    ->toArray();

                $leastTeam = 'Tim Merah';
                $minMembers = PHP_INT_MAX;
                foreach ($teams as $t) {
                    $count = $teamCounts[$t] ?? 0;
                    if ($count < $minMembers) {
                        $minMembers = $count;
                        $leastTeam = $t;
                    }
                }
                $room->students()->attach($user->id, ['team' => $leastTeam]);
            } else {
                $room->students()->attach($user->id);
            }
        }

        return redirect()->route('student.room', $room)
            ->with('success', 'Berhasil bergabung ke room!');
    }

    public function show(Room $room)
    {
        /** @var User|null $user */
        $user = request()->user();

        if (! $user) {
            return redirect()->route('student.join')
                ->withErrors(['pin' => 'Silakan bergabung ke room terlebih dahulu.']);
        }

        if (! $room->students()->where('room_user.user_id', $user->id)->exists()) {
            return redirect()->route('student.join')
                ->withErrors(['pin' => 'Anda belum bergabung ke room ini.']);
        }

        $room->load('game', 'teacher', 'session');

        // Kalau game sudah selesai → redirect ke hasil
        if ($room->session?->isFinished()) {
            return redirect()->route('student.result', $room);
        }

        // Kalau game belum dimulai → halaman waiting
        if (! $room->session || $room->session->status === 'waiting') {
            return response()
                ->view('student.waiting', compact('room'))
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        }

        // Game sedang berjalan → halaman game dengan data state awal (0 delay tanpa stuck "Memuat soal...")
        $initialState = $this->getRoomState($room, $user);

        return response()
            ->view('student.game', compact('room', 'initialState'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Hitung state lengkap game room untuk siswa aktif.
     */
    public function getRoomState(Room $room, User $user): array
    {
        $room->load('game.questions.options', 'session');
        $session = $room->session;

        if (! $session || $session->status === 'waiting') {
            return ['status' => 'waiting'];
        }

        if ($session->isFinished()) {
            return ['status' => 'finished'];
        }

        // Otomatis berpindah ke soal berikutnya jika waktu soal sudah habis
        if ($session->checkAndAutoAdvance()) {
            $room->load('session');
            $session = $room->session;
            if ($session->isFinished()) {
                return ['status' => 'finished'];
            }
        }

        $questions = $room->game->questions()->orderBy('order')->get()->values();
        $currentIndex = (int) ($session->current_question_index ?? 0);
        $currentQuestion = $questions->get($currentIndex);

        if (! $currentQuestion) {
            return ['status' => 'finished'];
        }

        // Apakah siswa sudah menjawab soal ini?
        $myAnswer = StudentAnswer::where('room_session_id', $session->id)
            ->where('user_id', $user->id)
            ->where('question_id', $currentQuestion->id)
            ->first();

        $lives = 3;
        if ($room->game_mode === 'survival') {
            $wrongAnswers = StudentAnswer::where('room_session_id', $session->id)
                ->where('user_id', $user->id)
                ->where('is_correct', false)
                ->count();
            $lives = max(0, 3 - $wrongAnswers);
        }

        if ($room->game_mode === 'survival' && $lives <= 0) {
            return ['status' => 'eliminated'];
        }

        $myPivot = $room->students()->where('room_user.user_id', $user->id)->first()?->pivot;
        $myTeam = $myPivot ? $myPivot->team : null;
        $hasUsed5050 = $myPivot ? (bool) $myPivot->has_used_5050 : false;

        $timeLimit = $session->current_time_limit ?: $currentQuestion->time_limit_seconds;

        return [
            'status' => 'playing',
            'game_mode' => $room->game_mode,
            'lives' => $lives,
            'team' => $myTeam,
            'has_used_5050' => $hasUsed5050,
            'question_index' => $currentIndex,
            'total_questions' => $questions->count(),
            'time_limit' => $timeLimit,
            'time_remaining' => $session->timeRemaining(),
            'question' => [
                'id' => $currentQuestion->id,
                'text' => $currentQuestion->question_text,
                'type' => $currentQuestion->type ?? 'multiple_choice',
                'media_url' => $currentQuestion->formatted_media_url,
                'options' => $currentQuestion->options->map(fn ($o) => [
                    'id' => $o->id,
                    'text' => $o->option_text,
                ])->values(),
            ],
            'my_answer' => $myAnswer ? [
                'option_id' => $myAnswer->question_option_id,
                'is_correct' => $myAnswer->is_correct,
                'score_earned' => $myAnswer->score_earned,
            ] : null,
        ];
    }

    /**
     * Polling endpoint — siswa poll ini setiap 1.5 detik untuk sinkronisasi state.
     */
    public function state(Room $room, Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (! $room->students()->where('room_user.user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $state = $this->getRoomState($room, $user);

        return response()->json($state)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * POST — siswa submit jawaban untuk soal aktif.
     */
    public function answer(Room $room, Request $request)
    {
        $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'option_id' => ['required', 'exists:question_options,id'],
        ]);

        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $room->load('session');
        $session = $room->session;

        if (! $session || ! $session->isPlaying()) {
            return response()->json(['error' => 'Game belum berjalan atau sudah selesai.'], 422);
        }

        if ($session->timeRemaining() === 0) {
            return response()->json(['error' => 'Waktu habis!'], 422);
        }

        // Pastikan option milik question yang aktif
        $questions = $room->game->questions()->orderBy('order')->get()->values();
        $currentQuestion = $questions->get($session->current_question_index);

        if (! $currentQuestion || $currentQuestion->id !== (int) $request->question_id) {
            return response()->json(['error' => 'Soal tidak sesuai.'], 422);
        }

        if ($room->game_mode === 'survival') {
            $wrongAnswers = StudentAnswer::where('room_session_id', $session->id)
                ->where('user_id', $user->id)
                ->where('is_correct', false)
                ->count();
            if ($wrongAnswers >= 3) {
                return response()->json(['error' => 'Anda sudah tereliminasi.'], 422);
            }
        }

        // Cegah jawab dua kali
        $alreadyAnswered = StudentAnswer::where('room_session_id', $session->id)
            ->where('user_id', $user->id)
            ->where('question_id', $currentQuestion->id)
            ->exists();

        if ($alreadyAnswered) {
            return response()->json(['error' => 'Sudah dijawab.'], 422);
        }

        $selectedOption = $currentQuestion->options()->findOrFail($request->option_id);
        $isCorrect = $selectedOption->is_correct;

        // Skor bonus: lebih cepat jawab = lebih banyak poin
        $timeRemaining = $session->timeRemaining();
        $timeLimit = $session->current_time_limit ?: $currentQuestion->time_limit_seconds;
        $speedBonus = $timeLimit > 0 ? (int) (($timeRemaining / $timeLimit) * $currentQuestion->points * 0.5) : 0;
        $scoreEarned = $isCorrect ? ($currentQuestion->points + $speedBonus) : 0;

        StudentAnswer::create([
            'room_session_id' => $session->id,
            'user_id' => $user->id,
            'question_id' => $currentQuestion->id,
            'question_option_id' => $selectedOption->id,
            'answered_at' => Carbon::now(),
            'is_correct' => $isCorrect,
            'score_earned' => $scoreEarned,
        ]);

        return response()->json([
            'is_correct' => $isCorrect,
            'score_earned' => $scoreEarned,
            'correct_option_id' => $currentQuestion->correctOption?->id,
        ]);
    }

    public function usePowerup(Room $room, Request $request)
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $room->load('session');
        $session = $room->session;

        if (! $session || ! $session->isPlaying()) {
            return response()->json(['error' => 'Game belum berjalan atau sudah selesai.'], 422);
        }

        $myPivot = $room->students()->where('room_user.user_id', $user->id)->first()?->pivot;
        if (! $myPivot) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if ($myPivot->has_used_5050) {
            return response()->json(['error' => 'Power-up sudah digunakan.'], 422);
        }

        // Get current question
        $questions = $room->game->questions()->orderBy('order')->get()->values();
        $currentQuestion = $questions->get($session->current_question_index);
        if (! $currentQuestion) {
            return response()->json(['error' => 'Soal tidak ditemukan.'], 422);
        }

        // Get wrong options
        $wrongOptions = $currentQuestion->options()->where('is_correct', false)->get();

        // Pick 2 wrong options to remove
        $removedOptions = $wrongOptions->random(min(2, $wrongOptions->count()))->pluck('id');

        // Mark as used
        $room->students()->updateExistingPivot($user->id, ['has_used_5050' => true]);

        return response()->json([
            'removed_options' => $removedOptions,
        ]);
    }

    public function result(Room $room)
    {
        /** @var User|null $user */
        $user = request()->user();

        if (! $user) {
            return redirect()->route('student.join');
        }

        if (! $room->students()->where('room_user.user_id', $user->id)->exists()) {
            return redirect()->route('student.join');
        }

        $room->load('session', 'game', 'teacher');

        $session = $room->session;

        $leaderboard = $session
            ? StudentAnswer::where('room_session_id', $session->id)
                ->with('user')
                ->selectRaw('user_id, SUM(score_earned) as total_score, SUM(is_correct) as correct')
                ->groupBy('user_id')
                ->orderByDesc('total_score')
                ->get()
            : collect();

        $myScore = $leaderboard->firstWhere('user_id', $user->id);
        $myRank = $leaderboard->search(fn ($r) => $r->user_id === $user->id) + 1;

        $teamLeaderboard = collect();
        $myTeam = null;

        if ($room->game_mode === 'team') {
            $studentTeams = DB::table('room_user')
                ->where('room_id', $room->id)
                ->pluck('team', 'user_id');

            $myTeam = $studentTeams[$user->id] ?? null;

            $teamScores = [];
            foreach ($leaderboard as $l) {
                $team = $studentTeams[$l->user_id] ?? 'Tanpa Tim';
                if (! isset($teamScores[$team])) {
                    $teamScores[$team] = 0;
                }
                $teamScores[$team] += $l->total_score;
            }

            arsort($teamScores);
            $rank = 1;
            foreach ($teamScores as $team => $score) {
                $teamLeaderboard->push((object) [
                    'team' => $team,
                    'total_score' => $score,
                    'rank' => $rank++,
                ]);
            }
        }

        return view('student.result', compact('room', 'leaderboard', 'myScore', 'myRank', 'teamLeaderboard', 'myTeam'));
    }
}
