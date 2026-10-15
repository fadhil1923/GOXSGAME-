<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = ['ip_address', 'user_agent', 'url', 'user_id', 'role'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
