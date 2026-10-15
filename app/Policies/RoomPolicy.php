<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'teacher';
    }

    public function view(User $user, Room $room): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'teacher') {
            return $user->id === $room->user_id;
        }

        // Student can view if they are in the room, handled in RoomController mostly, but let's allow if they are attached
        return $room->students()->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'teacher';
    }

    public function update(User $user, Room $room): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'teacher' && $user->id === $room->user_id;
    }

    public function delete(User $user, Room $room): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'teacher' && $user->id === $room->user_id;
    }

    public function restore(User $user, Room $room): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Room $room): bool
    {
        return $user->role === 'admin';
    }
}
