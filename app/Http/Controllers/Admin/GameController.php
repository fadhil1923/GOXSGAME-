<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Game::class);
        $games = Game::with('subject')->latest()->paginate(12);

        return view('manage.games.index', compact('games'));
    }

    public function create()
    {
        Gate::authorize('create', Game::class);
        $subjects = Subject::orderBy('name')->get();

        return view('manage.games.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'game_url' => ['nullable', 'url', 'max:2048'], // Changed to nullable since we might use internal logic
            'xp_reward' => ['required', 'integer', 'min:0'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'difficulty' => ['nullable', 'string', 'max:50'],
        ]);

        Gate::authorize('create', Game::class);

        $validated['user_id'] = $request->user()->id;

        // Otomatis tentukan thumbnail sesuai mata pelajaran jika tidak diisi manual
        if (empty($validated['thumbnail_url']) && ! empty($validated['subject_id'])) {
            $subject = Subject::find($validated['subject_id']);
            $catalog = Game::defaultSubjectThumbnails();
            if ($subject && isset($catalog[$subject->name])) {
                $validated['thumbnail_url'] = $catalog[$subject->name];
            } else {
                $validated['thumbnail_url'] = $catalog['Default'];
            }
        }

        $game = Game::create($validated);

        return redirect()->route('manage.games.questions.index', $game)
            ->with('success', 'Game berhasil disimpan! Silakan tambahkan soal untuk game ini.');
    }

    public function show(Game $game)
    {
        Gate::authorize('view', $game);

        return redirect()->route('manage.games.questions.index', $game);
    }

    public function edit(Game $game)
    {
        Gate::authorize('update', $game);
        $subjects = Subject::orderBy('name')->get();

        return view('manage.games.edit', compact('game', 'subjects'));
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'game_url' => ['nullable', 'url', 'max:2048'],
            'xp_reward' => ['required', 'integer', 'min:0'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'difficulty' => ['nullable', 'string', 'max:50'],
        ]);

        Gate::authorize('update', $game);

        if (empty($validated['thumbnail_url']) && ! empty($validated['subject_id'])) {
            $subject = Subject::find($validated['subject_id']);
            $catalog = Game::defaultSubjectThumbnails();
            if ($subject && isset($catalog[$subject->name])) {
                $validated['thumbnail_url'] = $catalog[$subject->name];
            }
        }

        $game->update($validated);

        return redirect()->route('manage.games.index')
            ->with('success', 'Game berhasil diperbarui!');
    }

    public function destroy(Game $game)
    {
        Gate::authorize('delete', $game);
        $game->delete();

        return redirect()->route('manage.games.index')
            ->with('success', 'Game berhasil dihapus.');
    }
}
