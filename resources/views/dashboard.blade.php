@extends('layouts.neo')

@section('title', 'Dashboard — GOXSGAME')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . '! 👋')

@section('content')

@php
    $user = auth()->user();
    $role = $user->role;
@endphp

{{-- ===== ADMIN DASHBOARD ===== --}}
@if($role === 'admin')
<div class="flex flex-col gap-6">

    {{-- Welcome Banner --}}
    <div class="neo-box rounded-2xl bg-[#FFE600] p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 text-[8rem] opacity-10 select-none pointer-events-none">🛡️</div>
        <div>
            <h2 class="font-display font-black text-2xl text-black uppercase">Hai, {{ $user->name }}!</h2>
            <p class="font-mono font-bold text-sm text-black/80 mt-1">Panel Kendali Administrator — Pantau trafik pengunjung, pengguna, mata pelajaran, dan game.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('manage.settings.index') }}" class="neo-btn px-4 py-2.5 rounded-xl bg-white text-black font-display font-black text-xs uppercase flex items-center gap-1 shadow-[2px_2px_0px_#000]">
                <span class="material-symbols-outlined text-base">settings</span>
                Settings
            </a>
            <a href="{{ route('manage.games.index') }}"
               class="neo-btn px-5 py-2.5 rounded-xl bg-black !text-[#FFE600] font-display font-black text-xs uppercase tracking-wider shrink-0 shadow-[2px_2px_0px_#000]">
                Kelola Game →
            </a>
        </div>
    </div>

    {{-- Statistik Counter (Pengunjung, User, Game, Master) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Pengunjung --}}
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="font-display font-black text-xs uppercase text-stone-700 dark:text-slate-300">Trafik Pengunjung</span>
                <span class="w-8 h-8 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm border-2 border-black">👀</span>
            </div>
            <div class="mt-3">
                <span class="font-mono font-black text-2xl sm:text-3xl text-black dark:text-white">{{ number_format($totalVisitors ?? 0) }}</span>
                <span class="text-[11px] font-mono text-stone-600 dark:text-slate-400 block mt-0.5">
                    Hari ini: <strong class="text-green-600 dark:text-green-400">+{{ $todayVisitors ?? 0 }} kunjungan</strong>
                </span>
            </div>
        </div>

        {{-- Total User --}}
        <a href="{{ route('manage.users.index') }}" class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 flex flex-col justify-between hover:bg-stone-50 dark:hover:bg-slate-700/50 transition">
            <div class="flex items-center justify-between">
                <span class="font-display font-black text-xs uppercase text-stone-700 dark:text-slate-300">Total Pengguna</span>
                <span class="w-8 h-8 rounded-xl bg-[#4ADE80] text-black flex items-center justify-center text-sm border-2 border-black">👥</span>
            </div>
            <div class="mt-3">
                <span class="font-mono font-black text-2xl sm:text-3xl text-black dark:text-white">{{ number_format($totalUsers ?? 0) }}</span>
                <span class="text-[11px] font-mono text-stone-600 dark:text-slate-400 block mt-0.5">
                    {{ $totalTeachers ?? 0 }} Guru terdaftar ➔
                </span>
            </div>
        </a>

        {{-- Game & Soal --}}
        <a href="{{ route('manage.games.index') }}" class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 flex flex-col justify-between hover:bg-stone-50 dark:hover:bg-slate-700/50 transition">
            <div class="flex items-center justify-between">
                <span class="font-display font-black text-xs uppercase text-stone-700 dark:text-slate-300">Game Edukasi</span>
                <span class="w-8 h-8 rounded-xl bg-[#FFE600] text-black flex items-center justify-center text-sm border-2 border-black">🎮</span>
            </div>
            <div class="mt-3">
                <span class="font-mono font-black text-2xl sm:text-3xl text-black dark:text-white">{{ number_format($totalGames ?? 0) }}</span>
                <span class="text-[11px] font-mono text-stone-600 dark:text-slate-400 block mt-0.5">
                    {{ $totalRooms ?? 0 }} Room telah dibuka ➔
                </span>
            </div>
        </a>

        {{-- Master Mapel & Kelas --}}
        <a href="{{ route('manage.subjects.index') }}" class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 flex flex-col justify-between hover:bg-stone-50 dark:hover:bg-slate-700/50 transition">
            <div class="flex items-center justify-between">
                <span class="font-display font-black text-xs uppercase text-stone-700 dark:text-slate-300">Master Kurikulum</span>
                <span class="w-8 h-8 rounded-xl bg-[#A855F7] text-white flex items-center justify-center text-sm border-2 border-black">📚</span>
            </div>
            <div class="mt-3">
                <span class="font-mono font-black text-2xl sm:text-3xl text-black dark:text-white">{{ $totalSubjects ?? 0 }} Mapel</span>
                <span class="text-[11px] font-mono text-stone-600 dark:text-slate-400 block mt-0.5">
                    {{ $totalClasses ?? 0 }} Tingkat Kelas ➔
                </span>
            </div>
        </a>
    </div>

    {{-- Grid 2 Kolom: Aktivitas Pengunjung Terbaru & Navigasi Cepat --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Tabel Pengunjung Terbaru (2 Kolom) --}}
        <div class="lg:col-span-2 neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-6 flex flex-col gap-4">
            <div class="flex items-center justify-between border-b-2 border-black dark:border-white/20 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🌐</span>
                    <h3 class="font-display font-black text-base sm:text-lg text-black dark:text-white uppercase">Telah Mengunjungi (Log Trafik)</h3>
                </div>
                <span class="neo-tag px-2.5 py-0.5 bg-[#4ADE80] text-black text-[10px] font-mono font-bold">LIVE VISITOR</span>
            </div>

            @if(empty($recentVisitors) || $recentVisitors->isEmpty())
                <div class="py-8 text-center text-stone-400 dark:text-slate-500 font-mono text-xs">
                    Belum ada data kunjungan yang tercatat.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-body text-xs">
                        <thead>
                            <tr class="border-b-2 border-stone-200 dark:border-slate-700 text-stone-500 dark:text-slate-400 font-mono text-[11px] uppercase">
                                <th class="pb-2">Waktu</th>
                                <th class="pb-2">Pengguna / Role</th>
                                <th class="pb-2">IP & Halaman</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 dark:divide-slate-700/60">
                            @foreach($recentVisitors as $log)
                            <tr class="hover:bg-stone-50 dark:hover:bg-slate-700/50 transition">
                                <td class="py-2.5 font-mono text-stone-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                                <td class="py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-md border border-black flex items-center justify-center font-bold text-[10px] {{ $log->role === 'admin' ? 'bg-[#FB7185] text-black' : ($log->role === 'teacher' ? 'bg-[#4ADE80] text-black' : 'bg-stone-200 text-stone-700') }}">
                                            {{ strtoupper(substr($log->role ?? 'G', 0, 1)) }}
                                        </span>
                                        <span class="font-bold text-black dark:text-white">{{ $log->user?->name ?? 'Pengunjung Tamu' }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 max-w-[220px] truncate">
                                    <span class="font-mono text-stone-700 dark:text-slate-300 block truncate" title="{{ $log->url }}">{{ parse_url($log->url, PHP_URL_PATH) ?? '/' }}</span>
                                    <span class="font-mono text-[10px] text-stone-400 dark:text-slate-500">{{ $log->ip_address }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Navigasi Menu Cepat Admin --}}
        <div class="neo-box rounded-2xl bg-[#FEF9EF] dark:bg-slate-800 p-5 sm:p-6 flex flex-col gap-4">
            <div class="flex items-center gap-2 border-b-2 border-black dark:border-white/20 pb-3">
                <span class="text-xl">⚡</span>
                <h3 class="font-display font-black text-base uppercase text-black dark:text-white">Menu Master & Sistem</h3>
            </div>

            <div class="flex flex-col gap-2.5">
                <a href="{{ route('manage.users.index') }}" class="neo-btn p-3 rounded-xl bg-white dark:bg-slate-700 hover:bg-[#FFE600] dark:hover:bg-[#FFE600] flex items-center justify-between text-black dark:text-white dark:hover:text-black transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-xl">manage_accounts</span>
                        <span class="font-display font-black text-xs uppercase">Kelola Pengguna</span>
                    </div>
                    <span class="font-mono text-xs">➔</span>
                </a>

                <a href="{{ route('manage.subjects.index') }}" class="neo-btn p-3 rounded-xl bg-white dark:bg-slate-700 hover:bg-[#4ADE80] dark:hover:bg-[#4ADE80] flex items-center justify-between text-black dark:text-white dark:hover:text-black transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-xl">menu_book</span>
                        <span class="font-display font-black text-xs uppercase">Master Mata Pelajaran</span>
                    </div>
                    <span class="font-mono text-xs">➔</span>
                </a>

                <a href="{{ route('manage.classes.index') }}" class="neo-btn p-3 rounded-xl bg-white dark:bg-slate-700 hover:bg-[#06B6D4] dark:hover:bg-[#06B6D4] flex items-center justify-between text-black dark:text-white dark:hover:text-black transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-xl">school</span>
                        <span class="font-display font-black text-xs uppercase">Master Kelas & Tingkat</span>
                    </div>
                    <span class="font-mono text-xs">➔</span>
                </a>

                <a href="{{ route('manage.settings.index') }}" class="neo-btn p-3 rounded-xl bg-white dark:bg-slate-700 hover:bg-[#A855F7] dark:hover:bg-[#A855F7] hover:text-white dark:hover:text-white flex items-center justify-between text-black dark:text-white transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-xl">tune</span>
                        <span class="font-display font-black text-xs uppercase">Pengaturan Suara & Tema</span>
                    </div>
                    <span class="font-mono text-xs">➔</span>
                </a>
            </div>

            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border-2 border-black text-[11px] font-mono text-stone-600 dark:text-slate-300 mt-auto">
                💡 Anda dapat mengatur mode gelap/terang dan suara background melalui menu Settings.
            </div>
        </div>
    </div>
</div>

{{-- ===== TEACHER DASHBOARD ===== --}}
@elseif($role === 'teacher')
<div class="flex flex-col gap-6">

    {{-- Welcome Banner --}}
    <div class="neo-box rounded-2xl bg-[#4ADE80] p-6 flex items-center justify-between gap-4 relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 text-[8rem] opacity-10 select-none">🎓</div>
        <div>
            <h2 class="font-display font-black text-2xl text-black uppercase">Hai, {{ $user->name }}!</h2>
            <p class="font-mono font-bold text-sm text-black/70 mt-1">Buat room, mulai game, dan pantau siswa kamu secara live.</p>
        </div>
        <a href="{{ route('teacher.rooms.create') }}"
           class="neo-btn px-6 py-3 rounded-xl bg-black text-[#4ADE80] font-display font-black text-sm uppercase tracking-wider shrink-0">
            + Buat Room →
        </a>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('teacher.rooms.index') }}"
           class="neo-box rounded-2xl bg-[#2563EB] p-6 flex items-center gap-4 text-white hover:translate-y-[-4px] transition">
            <span class="material-symbols-outlined text-4xl">meeting_room</span>
            <div>
                <div class="font-display font-black text-base uppercase">Room Saya</div>
                <div class="font-mono text-xs opacity-80 mt-0.5">Lihat & kelola semua room aktif</div>
            </div>
        </a>
        <a href="{{ route('teacher.rooms.create') }}"
           class="neo-box rounded-2xl bg-[#FFE600] p-6 flex items-center gap-4 text-black hover:translate-y-[-4px] transition">
            <span class="material-symbols-outlined text-4xl">add_box</span>
            <div>
                <div class="font-display font-black text-base uppercase">Buat Room Baru</div>
                <div class="font-mono text-xs opacity-70 mt-0.5">Pilih game & atur PIN</div>
            </div>
        </a>
    </div>

    {{-- Tips --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 flex gap-4 items-start">
        <span class="text-3xl">💡</span>
        <div>
            <div class="font-display font-black text-sm uppercase text-black dark:text-white mb-1">Cara Memulai Game</div>
            <p class="font-mono text-xs text-stone-600 dark:text-slate-300 leading-relaxed">
                <strong>1.</strong> Buat room & pilih game → <strong>2.</strong> Bagikan <span class="bg-[#FFE600] text-black px-1 border border-black font-black">kode PIN</span> ke siswa →
                <strong>3.</strong> Tunggu siswa join di halaman <strong>Lobby</strong> → <strong>4.</strong> Klik <strong>START</strong> untuk mulai game!
            </p>
        </div>
    </div>
</div>

{{-- ===== STUDENT / GUEST DASHBOARD ===== --}}
@else
<div class="flex flex-col gap-6">
    <div class="neo-box rounded-2xl bg-[#2563EB] p-6 flex items-center justify-between gap-4 relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 text-[8rem] opacity-10 select-none">🎮</div>
        <div>
            <h2 class="font-display font-black text-2xl text-white uppercase">Hai, {{ $user->name }}!</h2>
            <p class="font-mono font-bold text-sm text-white/70 mt-1">Masukkan PIN dari gurumu untuk mulai game sekarang.</p>
        </div>
        <a href="{{ route('student.join') }}"
           class="neo-btn px-6 py-3 rounded-xl bg-[#FFE600] text-black font-display font-black text-sm uppercase tracking-wider shrink-0">
            Join Game 🚀
        </a>
    </div>
    {{-- Auto redirect --}}
    <script>setTimeout(() => { window.location.href = "{{ route('student.join') }}"; }, 500);</script>
</div>
@endif

@endsection