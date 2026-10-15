@extends('layouts.neo')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Student Dashboard')
@section('page-subtitle', 'Welcome back, ' . $user->name . '!')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Profil & Join --}}
    <div class="flex flex-col gap-6">
        
        {{-- Profile Card --}}
        <div class="neo-box rounded-2xl bg-[#FFE600] p-6 flex flex-col items-center text-center relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 text-7xl opacity-20">🎮</div>
            
            <div class="w-24 h-24 rounded-full border-4 border-black bg-white flex items-center justify-center font-display font-black text-4xl mb-4 relative z-10 shadow-[4px_4px_0px_#000]">
                <x-avatar :code="$user->avatar ?? 'cow|none'" size="lg" />
            </div>
            
            <h2 class="font-display font-black text-2xl text-black relative z-10">{{ $user->name }}</h2>
            <span class="neo-tag px-3 py-1 rounded-lg bg-black text-[#FFE600] text-xs font-mono font-black mt-2 relative z-10">
                Level 1 Siswa
            </span>

            <div class="w-full grid grid-cols-2 gap-3 mt-6 relative z-10">
                <div class="bg-white rounded-xl border-3 border-black p-3 shadow-[3px_3px_0px_#000]">
                    <span class="block text-[10px] font-mono font-bold text-stone-500">TOTAL SKOR</span>
                    <span class="block font-mono font-black text-xl text-[#2563EB]">{{ number_format($myScore) }}</span>
                </div>
                <div class="bg-white rounded-xl border-3 border-black p-3 shadow-[3px_3px_0px_#000]">
                    <span class="block text-[10px] font-mono font-bold text-stone-500">KUIS DIIKUTI</span>
                    <span class="block font-mono font-black text-xl text-[#EF4444]">{{ $myGamesCount }}</span>
                </div>
            </div>
        </div>

        {{-- Join Game Card --}}
        <div class="neo-box rounded-2xl bg-[#2563EB] p-6 text-white text-center">
            <h3 class="font-display font-black text-xl mb-2">Punya PIN Baru?</h3>
            <p class="font-body font-bold text-white/80 text-sm mb-4">Ayo bergabung dan kalahkan teman-temanmu!</p>
            <a href="{{ route('student.join') }}" class="neo-btn w-full py-4 rounded-xl bg-[#4ADE80] text-black font-display font-black text-lg uppercase tracking-wider flex items-center justify-center gap-2">
                Join Kuis 🚀
            </a>
        </div>

    </div>

    {{-- Kolom Kanan: Global Leaderboard --}}
    <div class="lg:col-span-2">
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden h-full flex flex-col">
            <div class="px-6 py-5 border-b-3 border-black bg-[#A855F7] text-white flex items-center gap-3">
                <span class="text-3xl animate-bounce">🌍</span>
                <div>
                    <h2 class="font-display font-black text-xl uppercase leading-tight">Global Leaderboard</h2>
                    <span class="text-xs font-mono font-bold text-white/80">Top 10 Pemain Terbaik GoxsGrow</span>
                </div>
            </div>

            @if($globalLeaderboard->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center p-10 text-center">
                    <p class="text-5xl mb-4">🌟</p>
                    <p class="font-display font-black text-stone-400 dark:text-slate-500">Jadilah yang pertama di papan peringkat!</p>
                </div>
            @else
                <div class="divide-y-2 divide-black/10 dark:divide-slate-700 flex-1 overflow-y-auto">
                    @foreach($globalLeaderboard as $rank => $entry)
                    @php
                        $isMe = $entry->user_id === $user->id;
                        $medals = ['🥇', '🥈', '🥉'];
                    @endphp
                    <div class="px-6 py-4 flex items-center gap-4 {{ $isMe ? 'bg-[#FFE600]/30 dark:bg-yellow-500/20' : '' }} hover:bg-stone-50 dark:hover:bg-slate-700/50 transition">
                        
                        {{-- Rank --}}
                        <div class="w-10 h-10 rounded-xl font-display font-black text-lg flex items-center justify-center shrink-0
                            {{ $rank === 0 ? 'bg-[#FFE600] border-3 border-black shadow-[2px_2px_0px_#000] scale-110 text-black' : ($rank === 1 ? 'bg-stone-200 dark:bg-slate-700 border-2 border-black text-black dark:text-white' : ($rank === 2 ? 'bg-[#FB923C] border-2 border-black text-black' : 'bg-white dark:bg-slate-900 border-2 border-black text-stone-500 dark:text-slate-400')) }}">
                            {{ isset($medals[$rank]) ? $medals[$rank] : ($rank + 1) }}
                        </div>
                        
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full border-2 border-black bg-[#FEF9EF] dark:bg-slate-700 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                            <x-avatar :code="$entry->user->avatar ?? 'cow|none'" size="sm" />
                        </div>
                        
                        {{-- Info --}}
                        <div class="flex-1 min-w-0 flex items-center gap-2">
                            <span class="font-display font-bold text-base text-black dark:text-white truncate {{ $isMe ? '!text-[#2563EB] dark:!text-blue-400' : '' }}">
                                {{ $entry->user->name ?? 'Siswa' }}
                            </span>
                            @if($isMe)
                                <span class="neo-tag px-2 py-0.5 rounded bg-[#2563EB] text-white text-[10px] font-mono font-black shrink-0">KAMU</span>
                            @endif
                        </div>
                        
                        {{-- Skor --}}
                        <div class="text-right">
                            <span class="font-mono font-black text-2xl text-black dark:text-white {{ $rank === 0 ? '!text-[#EF4444] dark:!text-red-400' : '' }}">
                                {{ number_format($entry->total_score) }}
                            </span>
                            <span class="block text-[10px] font-mono text-stone-500 dark:text-slate-400 font-medium uppercase tracking-widest mt-0.5">Points</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
