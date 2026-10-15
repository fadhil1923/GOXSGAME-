@extends('layouts.neo')

@section('title', 'Kelola Pengguna — GOXSGAME')
@section('page-title', 'Kelola Pengguna')
@section('page-subtitle', 'Manajemen akun Admin, Guru, dan Siswa')

@section('content')
<div class="flex flex-col gap-5 sm:gap-6 w-full">

    {{-- Header Action & Filter --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="neo-tag px-3 py-1.5 rounded-xl bg-[#4ADE80] text-black font-mono text-xs font-black">
                {{ $users->total() }} TOTAL USER
            </span>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('manage.users.index') }}" class="flex items-center gap-2">
                <select name="role" onchange="this.form.submit()" class="neo-input py-2 px-4 text-xs font-body bg-white dark:bg-slate-800 text-black dark:text-white rounded-xl">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Guru</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Siswa</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..." 
                        class="neo-input py-2 pl-3 pr-8 text-xs font-body bg-white dark:bg-slate-800 text-black dark:text-white rounded-xl w-36 sm:w-48">
                    @if(request('search'))
                        <a href="{{ route('manage.users.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-black dark:hover:text-white text-xs font-bold">✕</a>
                    @endif
                </div>
            </form>

            <a href="{{ route('manage.users.create') }}" class="neo-btn px-4 py-2.5 rounded-xl bg-[#FFE600] text-black font-display font-black text-xs uppercase flex items-center justify-center gap-1.5 shadow-[2px_2px_0px_#000]">
                <span class="material-symbols-outlined text-base">person_add</span>
                <span>Tambah User</span>
            </a>
        </div>
    </div>

    {{-- Users Table Neo-Box --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden p-4 sm:p-6 flex flex-col gap-4">
        @if($users->isEmpty())
            <div class="py-12 text-center flex flex-col items-center justify-center gap-2">
                <span class="text-5xl">👤</span>
                <p class="font-display font-black text-base text-black dark:text-white">Tidak ada pengguna ditemukan.</p>
                <p class="text-xs font-mono text-stone-500 dark:text-slate-400">Coba ubah kata kunci pencarian atau filter role.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-body text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b-3 border-black font-display font-black text-xs uppercase text-stone-600 dark:text-slate-300">
                            <th class="pb-3 px-2">Pengguna</th>
                            <th class="pb-3 px-2">Role</th>
                            <th class="pb-3 px-2 hidden sm:table-cell">Terdaftar</th>
                            <th class="pb-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-black/10 dark:divide-white/10">
                        @foreach($users as $u)
                        <tr class="hover:bg-[#FEF9EF] dark:hover:bg-slate-700/50 transition">
                            <td class="py-3.5 px-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl border-2 border-black flex items-center justify-center font-display font-black text-sm shrink-0 shadow-[2px_2px_0px_#000] {{ $u->role === 'admin' ? 'bg-[#FB7185] text-black' : ($u->role === 'teacher' ? 'bg-[#4ADE80] text-black' : 'bg-[#2563EB] text-white') }}">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-display font-black text-sm text-black dark:text-white truncate">{{ $u->name }}</span>
                                        <span class="font-mono text-xs text-stone-500 dark:text-slate-400 truncate">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-2">
                                @if($u->role === 'admin')
                                    <span class="neo-tag px-2.5 py-0.5 rounded-lg bg-[#FB7185] text-black text-[10px] font-mono font-bold">
                                        👑 ADMIN
                                    </span>
                                @elseif($u->role === 'teacher')
                                    <span class="neo-tag px-2.5 py-0.5 rounded-lg bg-[#4ADE80] text-black text-[10px] font-mono font-bold">
                                        👨‍🏫 GURU
                                    </span>
                                @else
                                    <span class="neo-tag px-2.5 py-0.5 rounded-lg bg-[#2563EB] text-white text-[10px] font-mono font-bold">
                                        🎒 SISWA
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-2 font-mono text-xs text-stone-500 dark:text-slate-400 hidden sm:table-cell">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-2 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('manage.users.edit', $u) }}" class="neo-btn px-2.5 py-1.5 rounded-lg bg-[#FFE600] text-black font-display font-black text-xs uppercase flex items-center gap-1 shadow-[2px_2px_0px_#000]" title="Edit Pengguna">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span class="hidden sm:inline">Edit</span>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('manage.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="neo-btn p-1.5 rounded-lg bg-[#FB7185] text-black font-display font-black text-xs uppercase flex items-center justify-center shadow-[2px_2px_0px_#000]" title="Hapus User">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
