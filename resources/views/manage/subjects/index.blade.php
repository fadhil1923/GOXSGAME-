@extends('layouts.neo')

@section('title', 'Master Mata Pelajaran — GOXSGAME')
@section('page-title', 'Mata Pelajaran')
@section('page-subtitle', 'Kelola daftar bidang studi & kategori game edukasi')

@section('content')
<div class="flex flex-col gap-6 w-full">

    {{-- Top Action & Add Form --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-7 flex flex-col gap-5">
        <div class="flex items-center justify-between border-b-3 border-black pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FFE600] border-2 border-black flex items-center justify-center text-xl shadow-[2px_2px_0px_#000]">
                    📚
                </div>
                <div>
                    <h3 class="font-display font-black text-base sm:text-lg uppercase text-black dark:text-white">Tambah Mata Pelajaran Baru</h3>
                    <p class="font-mono text-xs text-stone-500 dark:text-slate-400">Buat mata pelajaran baru untuk pengelompokan game kuis edukatif</p>
                </div>
            </div>
            <span class="neo-tag px-3 py-1 bg-[#FFE600] text-black text-xs font-mono font-black">
                {{ $subjects->count() }} MAPEL AKTIF
            </span>
        </div>

        <form method="POST" action="{{ route('manage.subjects.store') }}" class="flex flex-col gap-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6 flex flex-col gap-1.5">
                    <label class="font-display font-black text-xs uppercase text-black dark:text-white tracking-wide">
                        Nama Mata Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="subject-name-input" required placeholder="Contoh: Seni Budaya & Prakarya"
                        class="w-full px-4 py-3 rounded-xl border-3 border-black bg-stone-50 dark:bg-slate-900 text-black dark:text-white font-body text-xs sm:text-sm font-bold shadow-[2px_2px_0px_#000] focus:outline-none focus:bg-white dark:focus:bg-slate-950 transition">
                </div>

                <div class="sm:col-span-3 flex flex-col gap-1.5">
                    <label class="font-display font-black text-xs uppercase text-black dark:text-white tracking-wide">
                        Icon / Emoji / Simbol
                    </label>
                    <div class="relative flex items-center">
                        <input type="text" name="icon" id="subject-icon-input" placeholder="menu_book atau 🎨" maxlength="30"
                            class="w-full pl-4 pr-11 py-3 rounded-xl border-3 border-black bg-stone-50 dark:bg-slate-900 text-black dark:text-white font-mono text-xs sm:text-sm font-bold shadow-[2px_2px_0px_#000] focus:outline-none focus:bg-white dark:focus:bg-slate-950 transition"
                            oninput="previewSubjectIcon(this.value)">
                        <div id="subject-icon-preview" class="absolute right-2.5 w-7 h-7 rounded-lg bg-white dark:bg-slate-800 border-2 border-black flex items-center justify-center text-black dark:text-white pointer-events-none">
                            <span class="material-symbols-outlined text-base">menu_book</span>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl border-3 border-black bg-[#4ADE80] text-black font-display font-black text-xs sm:text-sm uppercase shadow-[3px_3px_0px_#000] hover:bg-emerald-400 active:translate-y-0.5 active:shadow-[1px_1px_0px_#000] flex items-center justify-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-lg">add_circle</span>
                        Tambah Mapel
                    </button>
                </div>
            </div>

            {{-- Quick Icon Presets Pill Bar --}}
            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span class="text-[11px] font-mono font-bold text-stone-500 dark:text-slate-400 mr-1">Ikon Cepat:</span>
                @php
                    $quickIcons = [
                        ['id' => 'calculate', 'emoji' => '📐', 'label' => 'Matematika'],
                        ['id' => 'science', 'emoji' => '🔬', 'label' => 'IPA'],
                        ['id' => 'public', 'emoji' => '🌍', 'label' => 'IPS'],
                        ['id' => 'menu_book', 'emoji' => '📖', 'label' => 'B. Indonesia'],
                        ['id' => 'language', 'emoji' => '🗣️', 'label' => 'B. Inggris'],
                        ['id' => 'code', 'emoji' => '💻', 'label' => 'Coding / IT'],
                        ['id' => 'palette', 'emoji' => '🎨', 'label' => 'Seni'],
                        ['id' => 'sports_soccer', 'emoji' => '⚽', 'label' => 'Olahraga'],
                        ['id' => 'music_note', 'emoji' => '🎵', 'label' => 'Musik'],
                        ['id' => 'history_edu', 'emoji' => '📜', 'label' => 'Sejarah'],
                    ];
                @endphp
                @foreach($quickIcons as $qi)
                    <button type="button" onclick="selectQuickIcon('{{ $qi['id'] }}')" 
                        class="px-2.5 py-1 rounded-lg border-2 border-black bg-stone-100 dark:bg-slate-700 hover:bg-[#FFE600] dark:hover:bg-[#FFE600] text-black dark:text-white dark:hover:text-black font-mono text-xs font-bold transition flex items-center gap-1 shadow-[1px_1px_0px_#000]"
                        title="{{ $qi['label'] }}">
                        <span>{{ $qi['emoji'] }}</span>
                        <span class="hidden md:inline">{{ $qi['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Grid Daftar Mapel --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @php
            $accentColors = ['#FFE600', '#4ADE80', '#06B6D4', '#FB7185', '#A855F7', '#FB923C'];
        @endphp
        @forelse($subjects as $index => $s)
        @php
            $color = $accentColors[$index % count($accentColors)];
            $iconVal = $s->icon ?: 'menu_book';
            $isEmoji = preg_match('/[\x{1F300}-\x{1F9FF}]/u', $iconVal);
        @endphp
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 flex flex-col justify-between gap-4 relative overflow-hidden">
            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 right-0 h-2 border-b-2 border-black" style="background-color: {{ $color }};"></div>

            <div class="pt-2 flex items-start justify-between gap-3">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-13 h-13 w-12 h-12 rounded-2xl border-3 border-black flex items-center justify-center shrink-0 shadow-[3px_3px_0px_#000]" style="background-color: {{ $color }}; color: #000000;">
                        @if($isEmoji)
                            <span class="text-2xl">{{ $iconVal }}</span>
                        @else
                            <span class="material-symbols-outlined text-2xl font-bold">{{ $iconVal }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-display font-black text-base sm:text-lg leading-snug text-black dark:text-white truncate" title="{{ $s->name }}">
                            {{ $s->name }}
                        </h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded-md border-2 border-black bg-stone-100 dark:bg-slate-700 text-stone-700 dark:text-slate-200 text-[11px] font-mono font-bold">
                                {{ $s->topics_count ?? 0 }} Topik Soal
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Delete --}}
                <form method="POST" action="{{ route('manage.subjects.destroy', $s) }}" onsubmit="return confirm('Hapus mapel {{ $s->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl border-2 border-black bg-[#FB7185] hover:bg-rose-500 text-black flex items-center justify-center transition shadow-[2px_2px_0px_#000] shrink-0 active:translate-y-0.5" title="Hapus Mapel">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </form>
            </div>

            {{-- Form Quick Edit Accordion --}}
            <details class="text-xs font-mono border-t-2 border-black/15 dark:border-white/15 pt-2.5 group">
                <summary class="font-bold flex items-center justify-between text-stone-700 dark:text-slate-300 hover:text-black dark:hover:text-white cursor-pointer select-none py-1">
                    <span class="flex items-center gap-1.5 font-display uppercase text-xs font-black">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit Mapel & Icon
                    </span>
                    <span class="material-symbols-outlined text-base transition-transform group-open:rotate-180">expand_more</span>
                </summary>
                <form method="POST" action="{{ route('manage.subjects.update', $s) }}" class="flex flex-col gap-2.5 mt-2.5 pt-2 border-t border-black/10 dark:border-white/10">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-mono font-bold uppercase text-stone-500 dark:text-slate-400">Nama Mapel</label>
                        <input type="text" name="name" value="{{ $s->name }}" class="w-full px-3 py-2 border-2 border-black rounded-xl text-xs font-bold bg-stone-50 dark:bg-slate-900 text-black dark:text-white focus:outline-none" required>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-mono font-bold uppercase text-stone-500 dark:text-slate-400">Icon / Simbol (misal: calculate atau 📐)</label>
                        <input type="text" name="icon" value="{{ $s->icon }}" class="w-full px-3 py-2 border-2 border-black rounded-xl text-xs font-mono bg-stone-50 dark:bg-slate-900 text-black dark:text-white focus:outline-none" placeholder="Icon / 🎨">
                    </div>
                    <button type="submit" class="py-2 px-3 bg-[#FFE600] hover:bg-yellow-400 text-black border-2 border-black rounded-xl font-display font-black text-xs uppercase self-end shadow-[2px_2px_0px_#000] active:translate-y-0.5 transition flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Simpan Perubahan
                    </button>
                </form>
            </details>
        </div>
        @empty
        <div class="col-span-full py-16 text-center neo-box rounded-2xl bg-white dark:bg-slate-800 flex flex-col items-center justify-center gap-3">
            <span class="text-5xl">📚</span>
            <p class="font-display font-black text-lg text-black dark:text-white">Belum ada mata pelajaran.</p>
            <p class="font-mono text-xs text-stone-500 dark:text-slate-400">Silakan tambahkan mata pelajaran pertama Anda pada form di atas.</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function selectQuickIcon(iconName) {
        const input = document.getElementById('subject-icon-input');
        if (input) {
            input.value = iconName;
            previewSubjectIcon(iconName);
        }
    }

    function previewSubjectIcon(val) {
        const preview = document.getElementById('subject-icon-preview');
        if (!preview) return;
        
        const cleanVal = (val || 'menu_book').trim();
        // Check if emoji
        const isEmoji = /\p{Extended_Pictographic}/u.test(cleanVal);
        if (isEmoji) {
            preview.innerHTML = `<span class="text-sm">${cleanVal}</span>`;
        } else {
            preview.innerHTML = `<span class="material-symbols-outlined text-base">${cleanVal}</span>`;
        }
    }
</script>
@endpush
@endsection
