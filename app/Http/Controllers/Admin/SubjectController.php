<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount(['topics'])->latest()->get();

        return view('manage.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        Subject::create($validated);

        return redirect()->route('manage.subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name,'.$subject->id],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        $subject->update($validated);

        return redirect()->route('manage.subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('manage.subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}
