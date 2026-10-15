@extends('layouts.neo')

@section('title', $room->game->title)
@section('page-title', $room->game->title)
@section('page-subtitle', 'Room: ' . $room->room_code . ' • Oleh: ' . $room->teacher->name)

@section('content')
<div class="flex flex-col gap-6">
    {{-- Room Info Banner --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl bg-[#2563EB] border-4 border-black shadow-[6px_6px_0px_#000000]">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-[#FFE600] border-3 border-black flex items-center justify-center text-3xl shrink-0 shadow-[3px_3px_0px_#000000]">
                🎮
            </div>
            <div>
                <h2 class="font-display font-black text-white text-xl leading-tight">{{ $room->game->title }}</h2>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    <span class="neo-tag px-2 py-0.5 rounded bg-[#FFE600] text-black text-[10px] font-mono font-black">
                        PIN: {{ $room->room_code }}
                    </span>
                    <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-black">
                        +{{ $room->game->xp_reward }} XP
                    </span>
                    <span class="text-white/70 text-[10px] font-mono">oleh {{ $room->teacher->name }}</span>
                </div>
            </div>
        </div>
        <span class="neo-tag px-3 py-1.5 rounded-lg bg-[#4ADE80] text-black font-mono text-xs font-black uppercase shrink-0">
            🟢 LIVE
        </span>
    </div>

    {{-- Game Iframe / Play Area --}}
    <div class="neo-box rounded-2xl overflow-hidden bg-black">
        <div class="flex items-center justify-between px-5 py-3 bg-[#FFFDF5] dark:bg-slate-900 border-b-3 border-black">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-[#FB7185] border border-black"></span>
                <span class="w-3 h-3 rounded-full bg-[#FFE600] border border-black"></span>
                <span class="w-3 h-3 rounded-full bg-[#4ADE80] border border-black"></span>
            </div>
            <span class="font-mono font-bold text-xs text-stone-500 dark:text-slate-400 truncate mx-4">{{ $room->game->game_url }}</span>
            <a href="{{ $room->game->game_url }}" target="_blank" class="neo-btn px-3 py-1.5 rounded-lg bg-[#FFE600] text-black font-mono font-black text-[10px] uppercase flex items-center gap-1 shrink-0">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                Full Screen
            </a>
        </div>
        <div class="relative w-full" style="padding-bottom: 56.25%; min-height: 400px;">
            @if($room->game->game_url && $room->game->game_url !== '#')
                <iframe
                    src="{{ $room->game->game_url }}"
                    class="absolute inset-0 w-full h-full"
                    frameborder="0"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    title="{{ $room->game->title }}"
                ></iframe>
            @else
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-[#1a1a1a]">
                    <div class="text-7xl">🎮</div>
                    <p class="font-display font-black text-white text-xl">Game URL belum diset</p>
                    <p class="font-mono text-stone-400 text-sm">Admin perlu mengatur URL game ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Game Description --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-6">
        <h3 class="font-display font-black text-base text-black dark:text-white mb-2 uppercase">Tentang Game Ini</h3>
        <p class="font-body text-stone-600 dark:text-slate-300 text-sm leading-relaxed">{{ $room->game->description }}</p>
    </div>

    {{-- Back Button --}}
    <div>
        <a href="{{ route('student.join') }}" class="neo-back-btn inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-base">dialpad</span>
            Masuk Room Lain
        </a>
    </div>
</div>
@endsection
