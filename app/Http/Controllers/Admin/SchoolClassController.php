<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::orderBy('name')->get();

        return view('manage.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_classes,name'],
            'level' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        SchoolClass::create($validated);

        return redirect()->route('manage.classes.index')->with('success', 'Data Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_classes,name,'.$class->id],
            'level' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $class->update($validated);

        return redirect()->route('manage.classes.index')->with('success', 'Data Kelas berhasil diperbarui!');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('manage.classes.index')->with('success', 'Data Kelas berhasil dihapus!');
    }
}
