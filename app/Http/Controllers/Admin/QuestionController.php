<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    public function index(Game $game)
    {
        $questions = $game->questions()->with('options')->get();

        return view('manage.games.questions.index', compact('game', 'questions'));
    }

    public function create(Game $game)
    {
        Gate::authorize('update', $game);

        return view('manage.games.questions.create', compact('game'));
    }

    public function store(Request $request, Game $game)
    {
        Gate::authorize('update', $game);

        $type = $request->input('type', 'multiple_choice');

        $rules = [
            'type' => ['required', 'string', 'in:multiple_choice,true_false,image,audio'],
            'question_text' => ['required', 'string'],
            'time_limit_seconds' => ['required', 'integer', 'min:5', 'max:300'],
            'points' => ['required', 'integer', 'min:0'],
            'media_file' => ['nullable', 'file', 'max:10240'], // max 10MB
            'media_url' => ['nullable', 'string', 'max:500'],
        ];

        if ($type === 'true_false') {
            $rules['tf_correct'] = ['required', 'in:true,false'];
        } else {
            $rules['options'] = ['required', 'array', 'min:2', 'max:4'];
            $rules['options.*.text'] = ['required', 'string', 'max:255'];
            $rules['correct_option'] = ['required', 'integer', 'min:0'];
        }

        $validated = $request->validate($rules);

        // Handle upload media file
        $mediaUrl = $validated['media_url'] ?? null;
        if ($request->hasFile('media_file')) {
            $folder = $type === 'audio' ? 'audio' : 'images';
            $path = $request->file('media_file')->store("questions/{$folder}", 'public');
            $mediaUrl = "/storage/{$path}";
        }

        $question = $game->questions()->create([
            'question_text' => $validated['question_text'],
            'type' => $type,
            'media_url' => $mediaUrl,
            'time_limit_seconds' => $validated['time_limit_seconds'],
            'points' => $validated['points'],
            'order' => $game->questions()->count(),
        ]);

        if ($type === 'true_false') {
            $isTrueCorrect = $validated['tf_correct'] === 'true';
            $question->options()->create(['option_text' => 'BENAR', 'is_correct' => $isTrueCorrect]);
            $question->options()->create(['option_text' => 'SALAH', 'is_correct' => ! $isTrueCorrect]);
        } else {
            foreach ($validated['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => $index === (int) $validated['correct_option'],
                ]);
            }
        }

        return redirect()->route('manage.games.questions.index', $game)
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit(Game $game, Question $question)
    {
        $question->load('options');

        return view('manage.games.questions.edit', compact('game', 'question'));
    }

    public function update(Request $request, Game $game, Question $question)
    {
        Gate::authorize('update', $game);

        $type = $request->input('type', $question->type ?? 'multiple_choice');

        $rules = [
            'type' => ['required', 'string', 'in:multiple_choice,true_false,image,audio'],
            'question_text' => ['required', 'string'],
            'time_limit_seconds' => ['required', 'integer', 'min:5', 'max:300'],
            'points' => ['required', 'integer', 'min:0'],
            'media_file' => ['nullable', 'file', 'max:10240'],
            'media_url' => ['nullable', 'string', 'max:500'],
        ];

        if ($type === 'true_false') {
            $rules['tf_correct'] = ['required', 'in:true,false'];
        } else {
            $rules['options'] = ['required', 'array', 'min:2', 'max:4'];
            $rules['options.*.text'] = ['required', 'string', 'max:255'];
            $rules['correct_option'] = ['required', 'integer', 'min:0'];
        }

        $validated = $request->validate($rules);

        $mediaUrl = $validated['media_url'] ?? $question->media_url;
        if ($request->hasFile('media_file')) {
            $folder = $type === 'audio' ? 'audio' : 'images';
            $path = $request->file('media_file')->store("questions/{$folder}", 'public');
            $mediaUrl = "/storage/{$path}";
        }

        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $type,
            'media_url' => $mediaUrl,
            'time_limit_seconds' => $validated['time_limit_seconds'],
            'points' => $validated['points'],
        ]);

        // Rebuild all options
        $question->options()->delete();
        if ($type === 'true_false') {
            $isTrueCorrect = $validated['tf_correct'] === 'true';
            $question->options()->create(['option_text' => 'BENAR', 'is_correct' => $isTrueCorrect]);
            $question->options()->create(['option_text' => 'SALAH', 'is_correct' => ! $isTrueCorrect]);
        } else {
            foreach ($validated['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => $index === (int) $validated['correct_option'],
                ]);
            }
        }

        return redirect()->route('manage.games.questions.index', $game)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy(Game $game, Question $question)
    {
        Gate::authorize('update', $game);
        $question->delete();

        return redirect()->route('manage.games.questions.index', $game)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
