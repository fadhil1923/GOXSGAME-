<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'user_id', 'room_code', 'status', 'custom_time_per_question', 'game_mode'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id')
            ->withPivot('team', 'has_used_5050')
            ->withTimestamps();
    }

    public function session()
    {
        return $this->hasOne(RoomSession::class);
    }
}
