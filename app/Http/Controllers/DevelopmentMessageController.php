<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentMessage;
use Illuminate\Http\Request;

class DevelopmentMessageController extends Controller
{
    /**
     * Tampilkan formulir input pesan pengembangan dan riwayat pesan pengguna
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $myMessages = collect();

        if ($user) {
            $myMessages = DevelopmentMessage::where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();
        }

        return view('feedback.create', compact('myMessages', 'user'));
    }

    /**
     * Simpan pesan pengembangan baru dari guru atau siswa
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'in:feature_request,bug_report,ui_ux,question,other'],
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
        ], [
            'subject.required' => 'Judul pesan tidak boleh kosong.',
            'message.required' => 'Isi pesan pengembangan tidak boleh kosong.',
            'category.required' => 'Pilih salah satu kategori pesan.',
        ]);

        $user = $request->user();

        DevelopmentMessage::create([
            'user_id' => $user?->id,
            'name' => $user?->name ?? $request->input('name', 'Tamu Pengguna'),
            'email' => $user?->email ?? $request->input('email', 'guest@goxsgame.com'),
            'role' => $user?->role ?? 'guest',
            'category' => $request->input('category'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'status' => 'pending',
        ]);

        return redirect()->route('feedback.create')
            ->with('success', '🚀 Pesan pengembangan Anda berhasil dikirim! Tim Administrator GOXSGAME akan segera meninjau masukan Anda.');
    }
}
