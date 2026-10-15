@extends('layouts.neo')

@section('title', 'Kelola Game')
@section('page-title', 'Kelola Game')
@section('page-subtitle', 'Tambah, edit, atau hapus game edukasi')

@section('content')
<div class="flex items-center justify-between">
    <div>
        <span class="neo-tag px-3 py-1 rounded-lg bg-[#FB7185] text-black font-mono text-xs font-bold">
            {{ $games->total() }} Game Tersedia
        </span>
    </div>
    <a href="{{ route('manage.games.create') }}" class="neo-btn px-5 py-2.5 rounded-xl bg-[#FFE600] hover:bg-[#FACC15] text-black font-display font-black text-sm uppercase tracking-wider flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">add</span>
        Tambah Game
    </a>
</div>

@if($games->isEmpty())
    <div class="neo-box flex flex-col items-center justify-center py-24 rounded-2xl bg-white dark:bg-slate-800 border-4 border-black shadow-[6px_6px_0px_#000000]">
        <div class="text-7xl mb-4">🎮</div>
        <p class="font-display font-black text-2xl text-black dark:text-white">Belum ada game!</p>
        <p class="font-body text-stone-500 dark:text-slate-400 mt-1 mb-6">Mulai dengan menambahkan game edukasi pertama.</p>
        <a href="{{ route('manage.games.create') }}" class="neo-btn px-6 py-3 rounded-xl bg-[#2563EB] text-white font-display font-black text-sm uppercase tracking-wider">
            + Tambah Game Sekarang
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($games as $game)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden flex flex-col group">
            {{-- Thumbnail --}}
            <div class="h-44 bg-[#FFE600] border-b-3 border-black overflow-hidden relative">
                <img src="{{ $game->auto_thumbnail }}" alt="{{ $game->title }}" 
                     referrerpolicy="no-referrer"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';">
                <span class="absolute top-2 right-2 neo-tag bg-[#4ADE80] text-black text-[10px] font-mono px-2 py-0.5 rounded">
                    +{{ $game->xp_reward }} XP
                </span>
            </div>
            {{-- Content --}}
            <div class="p-4 flex flex-col gap-3 flex-1">
                <div class="flex flex-wrap gap-1">
                    @if($game->subject)
                        <span class="neo-tag bg-blue-200 dark:bg-blue-900 text-blue-900 dark:text-blue-100 text-[10px] font-black px-2 py-0.5 rounded">{{ $game->subject->name }}</span>
                    @endif
                    @if($game->grade_level)
                        <span class="neo-tag bg-pink-200 dark:bg-pink-900 text-pink-900 dark:text-pink-100 text-[10px] font-black px-2 py-0.5 rounded">{{ $game->grade_level }}</span>
                    @endif
                    @if($game->difficulty)
                        <span class="neo-tag bg-orange-200 dark:bg-orange-900 text-orange-900 dark:text-orange-100 text-[10px] font-black px-2 py-0.5 rounded">{{ $game->difficulty }}</span>
                    @endif
                </div>
                <h3 class="font-display font-black text-base text-black dark:text-white leading-tight">{{ $game->title }}</h3>
                <p class="text-xs font-body text-stone-600 dark:text-slate-300 leading-relaxed flex-1 line-clamp-2">{{ $game->description }}</p>
                <div class="flex flex-col gap-2 pt-2 border-t-2 border-black">
                    <a href="{{ route('manage.games.questions.index', $game) }}" class="neo-btn w-full py-2 rounded-xl bg-[#FFE600] text-black font-display font-black text-xs uppercase tracking-wider text-center flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">quiz</span>
                        Kelola Soal ({{ $game->questions->count() }})
                    </a>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('manage.games.edit', $game) }}" class="neo-btn flex-1 py-2 rounded-xl bg-[#2563EB] text-white font-display font-black text-xs uppercase tracking-wider text-center">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('manage.games.destroy', $game) }}" onsubmit="return confirm('Hapus game ini?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="neo-btn w-full py-2 rounded-xl bg-[#FB7185] text-black font-display font-black text-xs uppercase tracking-wider">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $games->links() }}
    </div>
@endif
@endsection
