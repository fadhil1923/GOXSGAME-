@extends('layouts.neo')

@section('title', 'Master Kelas — GOXSGAME')
@section('page-title', 'Master Kelas & Tingkat')
@section('page-subtitle', 'Kelola daftar tingkatan kelas untuk game dan siswa')

@section('content')
<div class="flex flex-col gap-6 w-full">

    {{-- Form Tambah Kelas Baru --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-6 flex flex-col gap-4">
        <div class="flex items-center justify-between border-b-3 border-black pb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#06B6D4] border-2 border-black flex items-center justify-center text-xl shadow-[2px_2px_0px_#000]">
                    🏫
                </div>
                <div>
                    <h3 class="font-display font-black text-base sm:text-lg uppercase text-black dark:text-white">Tambah Tingkat Kelas Baru</h3>
                    <p class="font-mono text-xs text-stone-500 dark:text-slate-400">Atur jenjang SD, SMP, SMA, atau Umum</p>
                </div>
            </div>
            <span class="neo-tag px-3 py-1 bg-[#06B6D4] text-black text-xs font-mono font-black">
                {{ $classes->count() }} KELAS TERDAFTAR
            </span>
        </div>

        <form method="POST" action="{{ route('manage.classes.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="font-display font-black text-xs uppercase text-black dark:text-white">Nama Kelas <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Kelas 1"
                    class="w-full px-3.5 py-2.5 rounded-xl border-3 border-black bg-stone-50 dark:bg-slate-900 text-black dark:text-white font-body text-xs sm:text-sm font-bold shadow-[2px_2px_0px_#000] focus:outline-none">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="font-display font-black text-xs uppercase text-black dark:text-white">Jenjang</label>
                <select name="level" class="w-full px-3.5 py-2.5 rounded-xl border-3 border-black bg-stone-50 dark:bg-slate-900 text-black dark:text-white font-body text-xs sm:text-sm font-bold shadow-[2px_2px_0px_#000] focus:outline-none">
                    <option value="SD">SD / MI</option>
                    <option value="SMP">SMP / MTs</option>
                    <option value="SMA">SMA / SMK</option>
                    <option value="Umum">Umum</option>
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="font-display font-black text-xs uppercase text-black dark:text-white">Keterangan / Fase</label>
                <input type="text" name="description" placeholder="Contoh: Fase A"
                    class="w-full px-3.5 py-2.5 rounded-xl border-3 border-black bg-stone-50 dark:bg-slate-900 text-black dark:text-white font-body text-xs sm:text-sm font-bold shadow-[2px_2px_0px_#000] focus:outline-none">
            </div>
            <button type="submit" class="w-full py-2.5 px-4 rounded-xl border-3 border-black bg-[#4ADE80] text-black font-display font-black text-xs uppercase shadow-[3px_3px_0px_#000] hover:bg-emerald-400 active:translate-y-0.5 active:shadow-[1px_1px_0px_#000] flex items-center justify-center gap-1.5 transition">
                <span class="material-symbols-outlined text-base">add</span>
                Tambah Kelas
            </button>
        </form>
    </div>

    {{-- Grid Kartu Kelas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($classes as $c)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-5 flex flex-col justify-between gap-3">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-[#06B6D4] text-black border-2 border-black flex items-center justify-center font-display font-black text-lg shrink-0 shadow-[2px_2px_0px_#000]">
                        {{ substr($c->name, -1) }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-display font-black text-base text-black dark:text-white leading-snug truncate">{{ $c->name }}</h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="neo-tag px-2 py-0.5 rounded bg-stone-100 dark:bg-slate-700 text-stone-800 dark:text-slate-200 text-[10px] font-mono font-bold">{{ $c->level ?? 'SD' }}</span>
                            @if($c->description)
                                <span class="font-mono text-xs text-stone-500 dark:text-slate-400 truncate">• {{ $c->description }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Delete --}}
                <form method="POST" action="{{ route('manage.classes.destroy', $c) }}" onsubmit="return confirm('Hapus data {{ $c->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg border-2 border-black bg-[#FB7185] hover:bg-rose-500 text-black flex items-center justify-center transition shadow-[2px_2px_0px_#000] shrink-0 active:translate-y-0.5" title="Hapus Kelas">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </form>
            </div>

            {{-- Form Quick Edit --}}
            <details class="text-xs font-mono border-t-2 border-black/15 dark:border-white/15 pt-2 cursor-pointer group">
                <summary class="font-bold flex items-center justify-between text-stone-600 dark:text-slate-300 hover:text-black dark:hover:text-white py-0.5">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">edit</span>
                        Edit Kelas & Jenjang
                    </span>
                    <span class="material-symbols-outlined text-sm transition-transform group-open:rotate-180">expand_more</span>
                </summary>
                <form method="POST" action="{{ route('manage.classes.update', $c) }}" class="flex flex-col gap-2 mt-2 pt-1 border-t border-black/10 dark:border-white/10">
                    @csrf
                    @method('PATCH')
                    <div class="flex gap-2">
                        <input type="text" name="name" value="{{ $c->name }}" class="flex-1 px-2.5 py-1.5 border-2 border-black rounded-lg text-xs font-bold bg-stone-50 dark:bg-slate-900 text-black dark:text-white focus:outline-none" required>
                        <select name="level" class="px-2 py-1.5 border-2 border-black rounded-lg text-xs font-bold bg-stone-50 dark:bg-slate-900 text-black dark:text-white focus:outline-none">
                            <option value="SD" {{ $c->level === 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ $c->level === 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ $c->level === 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="Umum" {{ $c->level === 'Umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="description" value="{{ $c->description }}" placeholder="Fase / Keterangan" class="flex-1 px-2.5 py-1.5 border-2 border-black rounded-lg text-xs bg-stone-50 dark:bg-slate-900 text-black dark:text-white focus:outline-none">
                        <button type="submit" class="px-3 py-1.5 bg-[#FFE600] text-black border-2 border-black rounded-lg font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000]">Simpan</button>
                    </div>
                </form>
            </details>
        </div>
        @empty
        <div class="col-span-full py-12 text-center neo-box rounded-2xl bg-white dark:bg-slate-800">
            <p class="font-display font-black text-stone-500 dark:text-slate-400">Belum ada data kelas.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
