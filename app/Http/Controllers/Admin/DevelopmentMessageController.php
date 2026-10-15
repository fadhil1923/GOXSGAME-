<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentMessage;
use Illuminate\Http\Request;

class DevelopmentMessageController extends Controller
{
    /**
     * Tampilkan seluruh pesan pengembangan yang masuk dari guru & siswa
     */
    public function index(Request $request)
    {
        $query = DevelopmentMessage::with('user');

        // Filter berdasarkan peran pengirim
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan kategori pesan
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status pesan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian berdasarkan kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(12)->withQueryString();

        // Statistik ringkas untuk admin dashboard cards
        $totalCount = DevelopmentMessage::count();
        $pendingCount = DevelopmentMessage::where('status', 'pending')->count();
        $teacherCount = DevelopmentMessage::where('role', 'teacher')->count();
        $studentCount = DevelopmentMessage::where('role', 'student')->count();

        return view('admin.feedback.index', compact(
            'messages',
            'totalCount',
            'pendingCount',
            'teacherCount',
            'studentCount'
        ));
    }

    /**
     * Update status dan catatan admin untuk suatu pesan
     */
    public function update(Request $request, DevelopmentMessage $feedback)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,read,in_progress,resolved'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Status dan catatan pesan berhasil diperbarui!');
    }

    /**
     * Hapus pesan pengembangan
     */
    public function destroy(DevelopmentMessage $feedback)
    {
        $feedback->delete();

        return back()->with('success', 'Pesan pengembangan berhasil dihapus.');
    }
}
