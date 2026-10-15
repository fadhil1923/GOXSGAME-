<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Room::class);
        $rooms = $request->user()->rooms()->with('game', 'students')->latest()->get();

        return view('teacher.rooms.index', compact('rooms'));
    }

    public function create()
    {
        Gate::authorize('create', Room::class);
        $games = Game::orderBy('title')->get();

        return view('teacher.rooms.create', compact('games'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id' => ['required', 'exists:games,id'],
            'game_mode' => ['required', 'in:classic,survival,team'],
        ]);

        Gate::authorize('create', Room::class);

        $room = Room::create([
            'game_id' => $request->game_id,
            'user_id' => $request->user()->id,
            'room_code' => strtoupper(Str::random(6)),
            'status' => 'active',
            'game_mode' => $request->game_mode,
        ]);

        return redirect()->route('teacher.rooms.show', $room)
            ->with('success', 'Room berhasil dibuat! Bagikan PIN kepada siswa Anda.');
    }

    public function show(Room $room)
    {
        Gate::authorize('view', $room);

        $room->load('game', 'students');

        return view('teacher.rooms.show', compact('room'));
    }

    public function destroy(Room $room)
    {
        Gate::authorize('delete', $room);

        $room->delete();

        return redirect()->route('teacher.rooms.index')
            ->with('success', 'Room berhasil dihapus.');
    }
}
