<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
}
