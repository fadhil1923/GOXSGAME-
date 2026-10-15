<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoomSession extends Model
{
    protected $fillable = [
        'room_id',
        'status',
        'current_question_index',
        'question_started_at',
        'current_time_limit',
    ];

    protected function casts(): array
    {
        return [
            'question_started_at' => 'datetime',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Return seconds remaining for current question (0 if expired).
     */
    public function timeRemaining(): int
    {
        if (! $this->question_started_at || ! $this->current_time_limit) {
            return 0;
        }

        $elapsed = (int) Carbon::now()->diffInSeconds($this->question_started_at, false) * -1;
        $remaining = $this->current_time_limit - $elapsed;

        return max(0, $remaining);
    }

    public function isPlaying(): bool
    {
        return $this->status === 'playing';
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    /**
     * Pindah ke soal berikutnya atau selesaikan game jika semua soal telah selesai.
     */
    public function advanceQuestion(): bool
    {
        if (! $this->isPlaying()) {
            return false;
        }

        $room = $this->room;
        if (! $room || ! $room->game) {
            return false;
        }

        $questions = $room->game->questions()->orderBy('order')->get()->values();
        $totalQuestions = $questions->count();
        $nextIndex = (int) $this->current_question_index + 1;

        if ($nextIndex >= $totalQuestions || ! $questions->has($nextIndex)) {
            $this->update([
                'status' => 'finished',
            ]);

            return true;
        }

        $nextQuestion = $questions->get($nextIndex);
        $timeLimit = $room->custom_time_per_question ?? ($nextQuestion?->time_limit_seconds ?? 30);

        $this->update([
            'current_question_index' => $nextIndex,
            'question_started_at' => Carbon::now(),
            'current_time_limit' => $timeLimit,
        ]);

        return true;
    }

    /**
     * Cek apakah seluruh siswa aktif yang terdaftar di room ini sudah menjawab soal saat ini.
     */
    public function allActiveStudentsHaveAnswered(): bool
    {
        if (! $this->isPlaying() || ! $this->room_id) {
            return false;
        }

        $room = $this->room;
        if (! $room || ! $room->game) {
            return false;
        }

        $studentIds = DB::table('room_user')
            ->where('room_id', $this->room_id)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($studentIds)) {
            return false;
        }

        // Pada mode survival, jangan tunggu siswa yang sudah tereliminasi (>= 3 salah)
        if ($room->game_mode === 'survival') {
            $eliminatedIds = StudentAnswer::where('room_session_id', $this->id)
                ->where('is_correct', false)
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) >= 3')
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $activeStudentIds = array_values(array_diff($studentIds, $eliminatedIds));
        } else {
            $activeStudentIds = $studentIds;
        }

        if (empty($activeStudentIds)) {
            return false;
        }

        $questions = $room->game->questions()->orderBy('order')->get()->values();
        $currentQuestion = $questions->get((int) $this->current_question_index);

        if (! $currentQuestion) {
            return false;
        }

        $answeredCount = StudentAnswer::where('room_session_id', $this->id)
            ->where('question_id', $currentQuestion->id)
            ->whereIn('user_id', $activeStudentIds)
            ->distinct('user_id')
            ->count('user_id');

        return $answeredCount >= count($activeStudentIds);
    }

    /**
     * Cek apakah waktu soal saat ini sudah habis atau seluruh siswa aktif telah menjawab.
     * Jika ya, otomatis pindah ke soal berikutnya secara aman (concurrency-safe).
     */
    public function checkAndAutoAdvance(): bool
    {
        if (! $this->isPlaying() || ! $this->question_started_at) {
            return false;
        }

        if ($this->timeRemaining() <= 0 || $this->allActiveStudentsHaveAnswered()) {
            return DB::transaction(function () {
                /** @var self|null $session */
                $session = self::where('id', $this->id)->lockForUpdate()->first();
                if ($session && $session->isPlaying() && ($session->timeRemaining() <= 0 || $session->allActiveStudentsHaveAnswered())) {
                    $advanced = $session->advanceQuestion();
                    $this->refresh();

                    return $advanced;
                }
                $this->refresh();

                return false;
            });
        }

        return false;
    }
}
