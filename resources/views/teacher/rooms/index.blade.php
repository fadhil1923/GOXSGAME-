@extends('layouts.neo')

@section('title', 'Room Saya')
@section('page-title', 'Room Saya')
@section('page-subtitle', 'Kelola room game yang Anda buat')

@section('content')
<div class="flex items-center justify-between">
    <span class="neo-tag px-3 py-1 rounded-lg bg-[#4ADE80] text-black font-mono text-xs font-bold">
        {{ $rooms->count() }} Room Aktif
    </span>
    <a href="{{ route('teacher.rooms.create') }}" class="neo-btn px-5 py-2.5 rounded-xl bg-[#FFE600] hover:bg-[#FACC15] text-black font-display font-black text-sm uppercase tracking-wider flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">add</span>
        Buat Room Baru
    </a>
</div>

@if($rooms->isEmpty())
    <div class="neo-box flex flex-col items-center justify-center py-24 rounded-2xl bg-white dark:bg-slate-800 border-4 border-black shadow-[6px_6px_0px_#000000]">
        <div class="text-7xl mb-4">🏫</div>
        <p class="font-display font-black text-2xl text-black dark:text-white">Belum ada room!</p>
        <p class="font-body text-stone-500 dark:text-slate-400 mt-1 mb-6">Buat room untuk siswa Anda mulai bermain.</p>
        <a href="{{ route('teacher.rooms.create') }}" class="neo-btn px-6 py-3 rounded-xl bg-[#2563EB] text-white font-display font-black text-sm uppercase tracking-wider">
            + Buat Room Sekarang
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($rooms as $room)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden flex flex-col">
            {{-- Game Thumbnail --}}
            <div class="h-36 bg-[#2563EB] border-b-3 border-black overflow-hidden relative">
                <img src="{{ $room->game->auto_thumbnail }}" alt="{{ $room->game->title }}" referrerpolicy="no-referrer" class="w-full h-full object-cover opacity-80" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-3">
                    <span class="font-display font-black text-white text-sm line-clamp-1">{{ $room->game->title }}</span>
                </div>
            </div>
            {{-- Content --}}
            <div class="p-5 flex flex-col gap-4 flex-1">
                {{-- PIN Code --}}
                <div class="flex flex-col items-center py-3 rounded-xl bg-[#FFE600] border-3 border-black shadow-[3px_3px_0px_#000000]">
                    <span class="text-[10px] font-mono font-black text-black uppercase tracking-widest mb-1">PIN ROOM</span>
                    <span class="font-mono font-black text-3xl text-black tracking-[0.3em]">{{ $room->room_code }}</span>
                </div>
                {{-- Stats --}}
                <div class="flex items-center justify-between text-xs font-mono font-bold">
                    <span class="flex items-center gap-1 text-black dark:text-white">
                        <span class="material-symbols-outlined text-base text-[#2563EB] dark:text-blue-400">group</span>
                        {{ $room->students->count() }} Siswa
                    </span>
                    <span class="neo-tag px-2 py-0.5 rounded text-[10px] {{ $room->status === 'active' ? 'bg-[#4ADE80] text-black' : 'bg-stone-200 dark:bg-slate-700 text-black dark:text-white' }} uppercase font-bold">
                        {{ $room->status }}
                    </span>
                </div>
                {{-- Actions --}}
                <div class="flex gap-2 pt-2 border-t-2 border-black dark:border-white/20">
                    <a href="{{ route('teacher.rooms.lobby', $room) }}" class="neo-btn flex-1 py-2 rounded-xl bg-[#4ADE80] text-black font-display font-black text-xs uppercase text-center">
                        ▶ Lobby
                    </a>
                    <a href="{{ route('teacher.rooms.show', $room) }}" class="neo-btn flex-1 py-2 rounded-xl bg-[#2563EB] text-white font-display font-black text-xs uppercase text-center">
                        Detail
                    </a>
                    <form method="POST" action="{{ route('teacher.rooms.destroy', $room) }}" onsubmit="return confirm('Hapus room ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="neo-btn px-3 py-2 rounded-xl bg-[#FB7185] text-black font-black text-xs uppercase">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
