<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'teacher';
    }

    public function view(User $user, Game $game): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'teacher') {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'teacher';
    }

    public function update(User $user, Game $game): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'teacher' && $user->id === $game->user_id;
    }

    public function delete(User $user, Game $game): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'teacher' && $user->id === $game->user_id;
    }

    public function restore(User $user, Game $game): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Game $game): bool
    {
        return $user->role === 'admin';
    }
}
