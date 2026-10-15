<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'question_text',
        'media_url',
        'type',
        'time_limit_seconds',
        'points',
        'order',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('id');
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function getFormattedMediaUrlAttribute(): ?string
    {
        if (empty($this->media_url)) {
            return null;
        }
        if (str_starts_with($this->media_url, 'http://') || str_starts_with($this->media_url, 'https://')) {
            return $this->media_url;
        }

        return asset(ltrim($this->media_url, '/'));
    }
}
