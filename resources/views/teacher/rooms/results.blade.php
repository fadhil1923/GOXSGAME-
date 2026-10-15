@extends('layouts.neo')

@section('title', 'Hasil Game — ' . $room->game->title)
@section('page-title', 'Leaderboard Akhir')
@section('page-subtitle', $room->game->title)

@section('content')
<div class="flex items-center justify-between mb-4">
    <a href="{{ route('teacher.rooms.lobby', $room) }}" class="neo-back-btn inline-flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Control Panel
    </a>
    <a href="{{ route('teacher.rooms.index') }}" class="neo-btn px-4 py-2 rounded-xl bg-[#FFE600] text-black font-display font-black text-xs uppercase">
        Daftar Room
    </a>
</div>

<div class="w-full neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
    <div class="px-6 py-4 border-b-3 border-black bg-[#FFE600] flex items-center gap-3">
        <span class="text-3xl">🏆</span>
        <h2 class="font-display font-black text-xl text-black uppercase">Final Leaderboard</h2>
        <span class="neo-tag px-2 py-0.5 rounded bg-black text-[#FFE600] text-xs font-mono font-black ml-auto">
            {{ $leaderboard->count() }} Siswa
        </span>
    </div>

    @if($leaderboard->isEmpty())
        <div class="py-16 text-center">
            <p class="text-4xl mb-3">📊</p>
            <p class="font-display font-black text-stone-400 dark:text-slate-500">Belum ada siswa yang menjawab.</p>
        </div>
    @else
        @php
            $top3 = $leaderboard->take(3);
            $others = $leaderboard->skip(3);
            $first = $top3->get(0);
            $second = $top3->get(1);
            $third = $top3->get(2);
        @endphp

        {{-- Podium 3 Besar --}}
        <div class="pt-12 pb-4 bg-[#FEF9EF] dark:bg-slate-900 flex items-end justify-center gap-4 px-6 border-b-3 border-black">
            {{-- Juara 2 --}}
            @if($second)
            <div class="flex flex-col items-center flex-1 max-w-[120px]">
                <div class="w-12 h-12 rounded-full border-2 border-black bg-stone-200 dark:bg-slate-700 flex items-center justify-center font-display font-black text-xl mb-2 shadow-[2px_2px_0px_#000]">
                    <x-avatar :code="$second->user->avatar ?? 'cow|none'" size="sm" />
                </div>
                <span class="font-display font-black text-sm text-black dark:text-white truncate w-full text-center">{{ $second->user->name }}</span>
                <span class="font-mono font-bold text-xs text-stone-500 dark:text-slate-400 mb-2">{{ number_format($second->total_score) }}</span>
                <div class="w-full bg-stone-300 dark:bg-slate-600 h-24 border-3 border-black rounded-t-xl flex justify-center pt-2 shadow-[4px_4px_0px_#000]">
                    <span class="font-display font-black text-2xl text-black dark:text-white">2</span>
                </div>
            </div>
            @endif

            {{-- Juara 1 --}}
            @if($first)
            <div class="flex flex-col items-center flex-1 max-w-[140px] z-10">
                <span class="text-4xl mb-1 animate-bounce">👑</span>
                <div class="w-16 h-16 rounded-full border-3 border-black bg-[#FFE600] flex items-center justify-center font-display font-black text-2xl mb-2 shadow-[4px_4px_0px_#000]">
                    <x-avatar :code="$first->user->avatar ?? 'cow|none'" size="md" />
                </div>
                <span class="font-display font-black text-base text-black dark:text-white truncate w-full text-center">{{ $first->user->name }}</span>
                <span class="font-mono font-black text-sm text-[#2563EB] dark:text-blue-400 mb-2">{{ number_format($first->total_score) }} pts</span>
                <div class="w-full bg-[#FFE600] h-32 border-3 border-black rounded-t-xl flex justify-center pt-2 shadow-[4px_4px_0px_#000]">
                    <span class="font-display font-black text-4xl text-black">1</span>
                </div>
            </div>
            @endif

            {{-- Juara 3 --}}
            @if($third)
            <div class="flex flex-col items-center flex-1 max-w-[120px]">
                <div class="w-12 h-12 rounded-full border-2 border-black bg-[#FB923C] flex items-center justify-center font-display font-black text-xl mb-2 shadow-[2px_2px_0px_#000]">
                    <x-avatar :code="$third->user->avatar ?? 'cow|none'" size="sm" />
                </div>
                <span class="font-display font-black text-sm text-black dark:text-white truncate w-full text-center">{{ $third->user->name }}</span>
                <span class="font-mono font-bold text-xs text-stone-500 dark:text-slate-400 mb-2">{{ number_format($third->total_score) }}</span>
                <div class="w-full bg-[#FB923C] h-20 border-3 border-black rounded-t-xl flex justify-center pt-2 shadow-[4px_4px_0px_#000]">
                    <span class="font-display font-black text-2xl text-black">3</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Sisa Leaderboard --}}
        <div class="divide-y-2 divide-black/10 dark:divide-slate-700 max-h-96 overflow-y-auto">
            @foreach($others as $rank => $entry)
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl font-display font-black text-lg flex items-center justify-center shrink-0 bg-white dark:bg-slate-900 text-black dark:text-white border-2 border-black">
                    {{ $rank + 4 }}
                </div>
                <div class="w-10 h-10 rounded-full border-2 border-black bg-[#FEF9EF] dark:bg-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                    <x-avatar :code="$entry->user->avatar ?? 'cow|none'" size="sm" />
                </div>
                <div class="flex-1 min-w-0">
                    <span class="font-display font-bold text-sm text-black dark:text-white block truncate">{{ $entry->user->name ?? 'Siswa' }}</span>
                    <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400 font-medium">{{ $entry->correct }}/{{ $entry->answered }} benar</span>
                </div>
                <div class="text-right">
                    <span class="font-mono font-black text-xl text-black dark:text-white">{{ number_format($entry->total_score) }}</span>
                    <span class="block text-[10px] font-mono text-stone-500 dark:text-slate-400 font-medium">pts</span>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
