<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameItem extends Model
{
    protected $fillable = ['name', 'description', 'embedding'];

    /**
     * Laravel 13 Native Vector Cast
     */
    protected function casts(): array
    {
        return [
            'embedding' => 'vector', // Ini akan mengotomatisasi konversi array ke format Vector MariaDB/MySQL
        ];
    }
}
