@extends('layouts.neo')

@section('title', 'Pesan Pengembangan — Admin')
@section('page-title', 'Pesan Pengembangan')
@section('page-subtitle', 'Daftar masukan, ide fitur, dan laporan bug dari Guru dan Siswa')

@section('content')
<div class="w-full flex flex-col gap-6">

    {{-- Stats Cards Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Card 1: Total Pesan --}}
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 border-3 border-black shadow-[4px_4px_0px_#000] flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-[11px] sm:text-xs font-mono font-bold text-stone-500 dark:text-slate-400 uppercase">Total Pesan</span>
                <span class="font-display font-black text-2xl sm:text-3xl text-black dark:text-white mt-1">{{ $totalCount }}</span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-[#2563EB] text-white border-2 border-black shadow-[2px_2px_0px_#000] flex items-center justify-center text-xl sm:text-2xl shrink-0">
                📬
            </div>
        </div>

        {{-- Card 2: Belum Dibaca --}}
        <div class="neo-box rounded-2xl bg-[#FFE600] p-4 sm:p-5 border-3 border-black shadow-[4px_4px_0px_#000] flex items-center justify-between text-black">
            <div class="flex flex-col">
                <span class="text-[11px] sm:text-xs font-mono font-bold text-black uppercase">Perlu Review</span>
                <span class="font-display font-black text-2xl sm:text-3xl text-black mt-1">{{ $pendingCount }}</span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-black text-[#FFE600] border-2 border-black shadow-[2px_2px_0px_#000] flex items-center justify-center text-xl sm:text-2xl shrink-0">
                ⏳
            </div>
        </div>

        {{-- Card 3: Dari Guru --}}
        <div class="neo-box rounded-2xl bg-[#4ADE80] p-4 sm:p-5 border-3 border-black shadow-[4px_4px_0px_#000] flex items-center justify-between text-black">
            <div class="flex flex-col">
                <span class="text-[11px] sm:text-xs font-mono font-bold text-black uppercase">Dari Guru</span>
                <span class="font-display font-black text-2xl sm:text-3xl text-black mt-1">{{ $teacherCount }}</span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-black text-[#4ADE80] border-2 border-black shadow-[2px_2px_0px_#000] flex items-center justify-center text-xl sm:text-2xl shrink-0">
                👨‍🏫
            </div>
        </div>

        {{-- Card 4: Dari Siswa --}}
        <div class="neo-box rounded-2xl bg-[#06B6D4] p-4 sm:p-5 border-3 border-black shadow-[4px_4px_0px_#000] flex items-center justify-between text-black">
            <div class="flex flex-col">
                <span class="text-[11px] sm:text-xs font-mono font-bold text-black uppercase">Dari Siswa</span>
                <span class="font-display font-black text-2xl sm:text-3xl text-black mt-1">{{ $studentCount }}</span>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-black text-[#06B6D4] border-2 border-black shadow-[2px_2px_0px_#000] flex items-center justify-center text-xl sm:text-2xl shrink-0">
                🎓
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 border-3 border-black shadow-[4px_4px_0px_#000]">
        <form method="GET" action="{{ route('manage.feedback.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            {{-- Search Input --}}
            <div class="flex flex-col gap-1 lg:col-span-2">
                <label for="search" class="font-display font-black text-xs uppercase text-black dark:text-white">Cari Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Cari subjek, pesan, atau nama..."
                        class="neo-input w-full px-4 py-2.5 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm">
                </div>
            </div>

            {{-- Filter Peran --}}
            <div class="flex flex-col gap-1">
                <label for="role" class="font-display font-black text-xs uppercase text-black dark:text-white">Pengirim</label>
                <select name="role" id="role" class="neo-input w-full px-3 py-2.5 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm">
                    <option value="">Semua Pengirim</option>
                    <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>👨‍🏫 Guru</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>🎓 Siswa</option>
                    <option value="guest" {{ request('role') === 'guest' ? 'selected' : '' }}>👤 Tamu / Guest</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="flex flex-col gap-1">
                <label for="status" class="font-display font-black text-xs uppercase text-black dark:text-white">Status</label>
                <select name="status" id="status" class="neo-input w-full px-3 py-2.5 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Menunggu Review</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>👀 Sudah Dibaca</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>🛠️ Sedang Diproses</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>✅ Selesai Ditanggapi</option>
                </select>
            </div>

            {{-- Filter Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="neo-btn flex-1 py-2.5 rounded-xl bg-[#2563EB] hover:bg-blue-600 text-white font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000] flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-base">filter_list</span>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'role', 'status', 'category']))
                    <a href="{{ route('manage.feedback.index') }}" class="neo-btn p-2.5 rounded-xl bg-stone-200 dark:bg-slate-700 text-black dark:text-white font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000] flex items-center justify-center" title="Reset Filter">
                        <span class="material-symbols-outlined text-base">restart_alt</span>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Messages List Card --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-6 border-3 border-black shadow-[5px_5px_0px_#000] flex flex-col gap-4">
        
        <div class="flex items-center justify-between border-b-2 sm:border-b-3 border-black/10 dark:border-white/10 pb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-2xl text-black dark:text-white">inbox</span>
                <h2 class="font-display font-black text-lg sm:text-xl text-black dark:text-white uppercase tracking-tight">
                    Kotak Masuk Pesan ({{ $messages->total() }})
                </h2>
            </div>
            <span class="text-xs font-mono text-stone-500 dark:text-slate-400">
                Halaman {{ $messages->currentPage() }} dari {{ $messages->lastPage() }}
            </span>
        </div>

        @if($messages->isEmpty())
            <div class="py-12 text-center flex flex-col items-center justify-center gap-3">
                <span class="text-5xl opacity-40">📭</span>
                <h3 class="font-display font-black text-base text-stone-500 dark:text-slate-400">
                    Tidak ada pesan pengembangan yang cocok.
                </h3>
                <p class="text-xs font-mono text-stone-400 dark:text-slate-500">
                    Belum ada pesan yang masuk dengan kriteria filter saat ini.
                </p>
            </div>
        @else
            <div class="flex flex-col gap-4">
                @foreach($messages as $msg)
                    <div class="p-4 sm:p-5 rounded-2xl border-3 border-black bg-[#FEF9EF] dark:bg-slate-900 shadow-[3px_3px_0px_#000] flex flex-col gap-3">
                        
                        {{-- Top Header Row: Sender & Meta --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-black/10 dark:border-white/10 pb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-black text-[#FFE600] font-display font-black text-base flex items-center justify-center border-2 border-black shrink-0">
                                    {{ strtoupper(substr($msg->name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col leading-tight">
                                    <div class="flex items-center gap-2">
                                        <span class="font-display font-black text-sm text-black dark:text-white">{{ $msg->name }}</span>
                                        <span class="neo-tag text-[9px] font-mono font-black px-1.5 py-0.5 rounded {{ $msg->role_badge_class }}">
                                            {{ strtoupper($msg->role) }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400">{{ $msg->email }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-start sm:self-auto">
                                <span class="neo-tag text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white dark:bg-slate-800 text-black dark:text-white border border-black shadow-[1px_1px_0px_#000]">
                                    {{ $msg->category_label }}
                                </span>
                                <span class="neo-tag text-[10px] font-mono font-bold px-2 py-0.5 rounded {{ $msg->status_badge_class }}">
                                    {{ $msg->status_label }}
                                </span>
                                <span class="text-[11px] font-mono text-stone-400 dark:text-slate-500 ml-1">
                                    {{ $msg->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Subject & Message Content --}}
                        <div class="flex flex-col gap-1.5">
                            <h4 class="font-display font-black text-sm sm:text-base text-black dark:text-white">
                                {{ $msg->subject }}
                            </h4>
                            <p class="text-xs sm:text-sm font-mono text-stone-700 dark:text-slate-300 whitespace-pre-line leading-relaxed bg-white/60 dark:bg-slate-800/60 p-3 sm:p-4 rounded-xl border border-black/20">
                                {{ $msg->message }}
                            </p>
                        </div>

                        {{-- Admin Action Bar (Update Status & Notes) --}}
                        <div class="mt-1 pt-3 border-t border-black/10 dark:border-white/10 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                            
                            {{-- Form Update Status & Note --}}
                            <form method="POST" action="{{ route('manage.feedback.update', $msg) }}" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                @csrf
                                @method('PATCH')

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-xs font-mono font-bold text-stone-500 dark:text-slate-400">Status:</span>
                                    <select name="status" class="neo-input px-2.5 py-1.5 bg-white dark:bg-slate-800 text-black dark:text-white text-xs border-2 border-black font-mono font-bold rounded-lg">
                                        <option value="pending" {{ $msg->status === 'pending' ? 'selected' : '' }}>⏳ Menunggu Review</option>
                                        <option value="read" {{ $msg->status === 'read' ? 'selected' : '' }}>👀 Sudah Dibaca</option>
                                        <option value="in_progress" {{ $msg->status === 'in_progress' ? 'selected' : '' }}>🛠️ Sedang Diproses</option>
                                        <option value="resolved" {{ $msg->status === 'resolved' ? 'selected' : '' }}>✅ Selesai Ditanggapi</option>
                                    </select>
                                </div>

                                <input type="text" name="admin_notes" value="{{ $msg->admin_notes }}"
                                    placeholder="Tulis catatan / tanggapan untuk pengirim..."
                                    class="neo-input flex-1 px-3 py-1.5 bg-white dark:bg-slate-800 text-black dark:text-white text-xs border-2 border-black rounded-lg placeholder:text-stone-400">

                                <button type="submit" class="neo-btn px-3 py-1.5 rounded-lg bg-[#FFE600] text-black font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000] shrink-0" title="Simpan perubahan status">
                                    Simpan
                                </button>
                            </form>

                            {{-- Delete Form --}}
                            <form method="POST" action="{{ route('manage.feedback.destroy', $msg) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')" class="shrink-0 self-end sm:self-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="neo-btn px-3 py-1.5 rounded-lg bg-[#FB7185] hover:bg-rose-500 text-black font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000] flex items-center gap-1" title="Hapus pesan">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    <span>Hapus</span>
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $messages->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
