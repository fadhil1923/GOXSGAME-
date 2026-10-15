@extends('layouts.neo')

@section('title', 'Hasil Game')
@section('page-title', 'Hasil Akhir')
@section('page-subtitle', $room->game->title)

@section('content')
<div class="w-full max-w-4xl mx-auto flex flex-col gap-4 sm:gap-6">

    {{-- My Score Card --}}
    @php $rankColors = ['bg-[#FFE600]', 'bg-stone-300', 'bg-[#FB923C]']; @endphp
    <div class="neo-box rounded-2xl p-5 sm:p-8 text-center flex flex-col items-center gap-3 sm:gap-4
        {{ $myRank === 1 ? 'bg-[#FFE600]' : ($myRank === 2 ? 'bg-stone-200 dark:bg-slate-700' : ($myRank === 3 ? 'bg-[#FB923C]' : 'bg-white dark:bg-slate-800')) }}">

        <div class="text-4xl sm:text-6xl">
            {{ $myRank === 1 ? '🥇' : ($myRank === 2 ? '🥈' : ($myRank === 3 ? '🥉' : '🎮')) }}
        </div>

        <div>
            <p class="font-mono font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-widest">Peringkat Kamu</p>
            <p class="font-display font-black text-4xl sm:text-6xl text-black dark:text-white">#{{ $myRank }}</p>
        </div>

        <div class="neo-box bg-white dark:bg-slate-900 px-5 sm:px-8 py-3 sm:py-4 rounded-2xl flex flex-col items-center w-full max-w-xs">
            <span class="font-mono font-black text-xs text-stone-500 dark:text-slate-400 uppercase">Total Skor</span>
            <span class="font-mono font-black text-2xl sm:text-4xl text-black dark:text-white">{{ $myScore?->total_score ?? 0 }}</span>
            <span class="text-xs font-mono text-stone-500 dark:text-slate-400 font-bold">{{ $myScore?->correct ?? 0 }} jawaban benar</span>
        </div>

        <a href="{{ route('student.join') }}" class="neo-btn px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-[#2563EB] text-white font-display font-black text-xs sm:text-sm uppercase tracking-wider">
            Masuk Room Lain
        </a>
    </div>

    {{-- Team Leaderboard --}}
    @if(isset($teamLeaderboard) && $teamLeaderboard->isNotEmpty())
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b-3 border-black bg-[#2563EB] text-white flex items-center gap-3">
            <span class="text-2xl">🤝</span>
            <h2 class="font-display font-black text-base uppercase">Leaderboard Tim</h2>
        </div>
        <div class="divide-y-2 divide-black/10 dark:divide-slate-700">
            @foreach($teamLeaderboard as $index => $t)
            <div class="px-6 py-4 flex items-center gap-4 {{ $myTeam === $t->team ? 'bg-[#2563EB]/10 dark:bg-[#2563EB]/30' : '' }}">
                <div class="font-display font-black text-xl w-8 text-center text-black dark:text-white">{{ $t->rank }}</div>
                <div class="flex-1">
                    <div class="font-display font-black text-lg text-black dark:text-white">{{ $t->team }}</div>
                    @if($myTeam === $t->team)
                        <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-black">TIM KAMU</span>
                    @endif
                </div>
                <div class="font-mono font-black text-2xl text-black dark:text-white">{{ number_format($t->total_score) }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Individual Leaderboard --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b-3 border-black bg-[#FEF9EF] dark:bg-slate-900 flex items-center gap-3">
            <span class="text-2xl">🏆</span>
            <h2 class="font-display font-black text-base text-black dark:text-white uppercase">Leaderboard Room</h2>
            <span class="neo-tag px-2 py-0.5 rounded bg-[#2563EB] text-white text-xs font-mono font-black ml-auto">
                {{ $leaderboard->count() }} Siswa
            </span>
        </div>

        @if($leaderboard->isEmpty())
            <div class="py-12 text-center">
                <p class="font-display font-black text-stone-400 dark:text-slate-500">Belum ada data hasil.</p>
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
            <div class="pt-8 pb-4 bg-white dark:bg-slate-800 flex items-end justify-center gap-3 px-4 border-b-3 border-black">
                {{-- Juara 2 --}}
                @if($second)
                <div class="flex flex-col items-center flex-1 max-w-[100px]">
                    <div class="w-10 h-10 rounded-full border-2 border-black bg-stone-200 dark:bg-slate-700 flex items-center justify-center font-display font-black text-lg mb-1 shadow-[2px_2px_0px_#000]">
                        <x-avatar :code="$second->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    <span class="font-display font-black text-[10px] text-black dark:text-white truncate w-full text-center">{{ $second->user->name }}</span>
                    <span class="font-mono font-bold text-[9px] text-stone-500 dark:text-slate-400 mb-1">{{ number_format($second->total_score) }}</span>
                    <div class="w-full bg-stone-300 dark:bg-slate-600 h-16 border-2 border-black rounded-t-lg flex justify-center pt-1 shadow-[2px_2px_0px_#000]">
                        <span class="font-display font-black text-xl text-black dark:text-white">2</span>
                    </div>
                </div>
                @endif

                {{-- Juara 1 --}}
                @if($first)
                <div class="flex flex-col items-center flex-1 max-w-[120px] z-10">
                    <span class="text-3xl mb-0 animate-bounce">👑</span>
                    <div class="w-14 h-14 rounded-full border-3 border-black bg-[#FFE600] flex items-center justify-center font-display font-black text-xl mb-1 shadow-[3px_3px_0px_#000]">
                        <x-avatar :code="$first->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    <span class="font-display font-black text-xs text-black dark:text-white truncate w-full text-center">{{ $first->user->name }}</span>
                    <span class="font-mono font-black text-[10px] text-[#2563EB] dark:text-blue-400 mb-1">{{ number_format($first->total_score) }}</span>
                    <div class="w-full bg-[#FFE600] h-24 border-2 border-black rounded-t-lg flex justify-center pt-1 shadow-[3px_3px_0px_#000]">
                        <span class="font-display font-black text-2xl text-black">1</span>
                    </div>
                </div>
                @endif

                {{-- Juara 3 --}}
                @if($third)
                <div class="flex flex-col items-center flex-1 max-w-[100px]">
                    <div class="w-10 h-10 rounded-full border-2 border-black bg-[#FB923C] flex items-center justify-center font-display font-black text-lg mb-1 shadow-[2px_2px_0px_#000]">
                        <x-avatar :code="$third->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    <span class="font-display font-black text-[10px] text-black dark:text-white truncate w-full text-center">{{ $third->user->name }}</span>
                    <span class="font-mono font-bold text-[9px] text-stone-500 dark:text-slate-400 mb-1">{{ number_format($third->total_score) }}</span>
                    <div class="w-full bg-[#FB923C] h-12 border-2 border-black rounded-t-lg flex justify-center pt-1 shadow-[2px_2px_0px_#000]">
                        <span class="font-display font-black text-xl text-black">3</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="divide-y-2 divide-black/10 dark:divide-slate-700 max-h-64 overflow-y-auto">
                @foreach($others as $rank => $entry)
                @php
                    $isMe = $entry->user_id === auth()->id();
                @endphp
                <div class="px-6 py-4 flex items-center gap-4 {{ $isMe ? 'bg-[#FFE600]/30 dark:bg-yellow-500/20' : '' }}">
                    {{-- Rank --}}
                    <div class="w-8 h-8 rounded-lg font-display font-black text-sm flex items-center justify-center shrink-0 bg-white dark:bg-slate-900 text-black dark:text-white border-2 border-black">
                        {{ $rank + 4 }}
                    </div>
                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full border-2 border-black bg-[#FEF9EF] dark:bg-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                        <x-avatar :code="$entry->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-display font-bold text-sm text-black dark:text-white truncate">{{ $entry->user->name ?? 'Siswa' }}</span>
                            @if($isMe)
                                <span class="neo-tag px-1.5 py-0.5 rounded bg-[#2563EB] text-white text-[9px] font-mono font-black">KAMU</span>
                            @endif
                        </div>
                        <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400 font-medium">{{ $entry->correct }} benar</span>
                    </div>
                    {{-- Skor --}}
                    <span class="font-mono font-black text-base text-black dark:text-white">{{ number_format($entry->total_score) }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
