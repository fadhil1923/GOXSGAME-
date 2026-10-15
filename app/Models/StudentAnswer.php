<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $fillable = [
        'room_session_id',
        'user_id',
        'question_id',
        'question_option_id',
        'answered_at',
        'is_correct',
        'score_earned',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
            'is_correct' => 'boolean',
        ];
    }

    public function session()
    {
        return $this->belongsTo(RoomSession::class, 'room_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
