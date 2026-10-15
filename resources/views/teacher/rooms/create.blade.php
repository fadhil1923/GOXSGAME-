@extends('layouts.neo')

@section('title', 'Buat Room')
@section('page-title', 'Buat Room Baru')
@section('page-subtitle', 'Pilih game dan buat room untuk siswa Anda')

@push('styles')
<style>
    /* Unselected Game & Mode Cards */
    .game-option-card,
    .mode-option-card {
        background-color: #F8FAFC;
        border: 3px solid #000000;
        box-shadow: 2px 2px 0px #000000;
        transition: all 0.15s ease;
    }
    .game-option-card .game-title,
    .mode-option-card .mode-title {
        color: #000000 !important;
    }
    .mode-option-card .mode-desc {
        color: #57534E !important;
    }

    /* Unselected Cards in Dark Mode */
    html.dark .game-option-card,
    html.dark .mode-option-card {
        background-color: #0F172A !important;
        border-color: #000000 !important;
        box-shadow: 3px 3px 0px #000000 !important;
    }
    html.dark .game-option-card .game-title,
    html.dark .mode-option-card .mode-title {
        color: #F8FAFC !important;
    }
    html.dark .mode-option-card .mode-desc {
        color: #94A3B8 !important;
    }

    /* Hover effect for unselected cards */
    .game-option-card:hover,
    .mode-option-card:hover {
        transform: translateY(-2px);
        box-shadow: 4px 4px 0px #000000;
    }

    /* Checked Game Card: Solid Bright Yellow with JET BLACK TEXT */
    input[name="game_id"]:checked + .game-option-card {
        background-color: #FFE600 !important;
        box-shadow: 5px 5px 0px #000000 !important;
        transform: translateY(-3px);
    }
    input[name="game_id"]:checked + .game-option-card .game-title {
        color: #000000 !important;
    }

    /* Checked Mode Klasik: Solid Bright Yellow with JET BLACK TEXT */
    input#mode_classic:checked + .mode-option-card {
        background-color: #FFE600 !important;
        box-shadow: 5px 5px 0px #000000 !important;
        transform: translateY(-3px);
    }
    input#mode_classic:checked + .mode-option-card .mode-title,
    input#mode_classic:checked + .mode-option-card .mode-desc {
        color: #000000 !important;
        font-weight: 800 !important;
    }

    /* Checked Mode Survival: Solid Bright Coral Pink with JET BLACK TEXT */
    input#mode_survival:checked + .mode-option-card {
        background-color: #FB7185 !important;
        box-shadow: 5px 5px 0px #000000 !important;
        transform: translateY(-3px);
    }
    input#mode_survival:checked + .mode-option-card .mode-title,
    input#mode_survival:checked + .mode-option-card .mode-desc {
        color: #000000 !important;
        font-weight: 800 !important;
    }

    /* Checked Mode Team: Solid Royal Blue with PURE WHITE TEXT */
    input#mode_team:checked + .mode-option-card {
        background-color: #2563EB !important;
        box-shadow: 5px 5px 0px #000000 !important;
        transform: translateY(-3px);
    }
    input#mode_team:checked + .mode-option-card .mode-title,
    input#mode_team:checked + .mode-option-card .mode-desc {
        color: #FFFFFF !important;
        font-weight: 800 !important;
    }
</style>
@endpush

@section('content')
<div class="w-full flex flex-col gap-4">
    <div>
        <a href="{{ route('teacher.rooms.index') }}" class="neo-back-btn inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Room
        </a>
    </div>

    <form method="POST" action="{{ route('teacher.rooms.store') }}" class="w-full neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-6 lg:p-8 flex flex-col gap-5 sm:gap-6">
        @csrf

        <div class="flex flex-col gap-4">
            <div>
                <label class="font-display font-black text-sm sm:text-base lg:text-lg text-black dark:text-white uppercase tracking-wide">Pilih Game Edukasi <span class="text-[#FB7185]">*</span></label>
                <p class="text-xs font-mono text-stone-500 dark:text-slate-400 mt-0.5">Pilih salah satu game yang ingin dimainkan bersama siswa di kelas.</p>
            </div>

            @if($games->isEmpty())
                <div class="py-10 sm:py-12 rounded-xl bg-[#FEF9EF] dark:bg-slate-900 border-2 sm:border-3 border-black text-center px-4">
                    <p class="font-display font-black text-stone-500 dark:text-slate-400 text-base sm:text-lg">Belum ada game yang tersedia.</p>
                    <p class="text-xs text-stone-400 dark:text-slate-500 mt-1">Hubungi Admin untuk menambahkan game terlebih dahulu.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4" id="game-list">
                    @foreach($games as $game)
                    <label for="game_{{ $game->id }}" class="cursor-pointer">
                        <input type="radio" id="game_{{ $game->id }}" name="game_id" value="{{ $game->id }}"
                            class="sr-only" {{ ($loop->first && !old('game_id')) || old('game_id') == $game->id ? 'checked' : '' }}>
                        <div class="game-option-card rounded-xl p-3 sm:p-4 flex gap-3 items-center h-full">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-[#2563EB] border-2 border-black overflow-hidden shrink-0 shadow-[2px_2px_0px_#000]">
                                <img src="{{ $game->auto_thumbnail }}" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';">
                            </div>
                            <div class="flex flex-col flex-1 min-w-0">
                                <span class="game-title font-display font-black text-xs sm:text-sm line-clamp-1">{{ $game->title }}</span>
                                <span class="neo-tag text-[10px] bg-[#4ADE80] !text-black px-1.5 py-0.5 rounded font-mono w-fit mt-1">+{{ $game->xp_reward }} XP</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <div class="mt-3 sm:mt-4 flex flex-col gap-3">
                    <label class="font-display font-black text-sm sm:text-base lg:text-lg text-black dark:text-white uppercase tracking-wide">Pilih Mode Game <span class="text-[#FB7185]">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <label for="mode_classic" class="cursor-pointer">
                            <input type="radio" id="mode_classic" name="game_mode" value="classic" class="sr-only" checked>
                            <div class="mode-option-card rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 items-start h-full">
                                <span class="mode-title font-display font-black text-xs sm:text-sm">🎯 Mode Klasik</span>
                                <span class="mode-desc text-[11px] sm:text-xs font-mono">Kumpulkan poin sebanyak-banyaknya tanpa pinalti nyawa.</span>
                            </div>
                        </label>
                        <label for="mode_survival" class="cursor-pointer">
                            <input type="radio" id="mode_survival" name="game_mode" value="survival" class="sr-only">
                            <div class="mode-option-card rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 items-start h-full">
                                <span class="mode-title font-display font-black text-xs sm:text-sm">☠️ Mode Survival</span>
                                <span class="mode-desc text-[11px] sm:text-xs font-mono">Siswa memiliki 3 nyawa. Salah jawab = hilang 1 nyawa. Habis = gugur!</span>
                            </div>
                        </label>
                        <label for="mode_team" class="cursor-pointer sm:col-span-2 lg:col-span-1">
                            <input type="radio" id="mode_team" name="game_mode" value="team" class="sr-only">
                            <div class="mode-option-card rounded-xl p-3.5 sm:p-4 flex flex-col gap-1 items-start h-full">
                                <span class="mode-title font-display font-black text-xs sm:text-sm">🤝 Mode Team</span>
                                <span class="mode-desc text-[11px] sm:text-xs font-mono">Siswa otomatis dibagi ke tim secara acak. Skor tim digabung bersama!</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-2 p-3 sm:p-4 rounded-xl bg-[#FEF9EF] dark:bg-slate-900 border-2 sm:border-3 border-black text-[11px] sm:text-xs font-mono text-stone-600 dark:text-slate-300 shadow-[2px_2px_0px_#000]">
                    💡 PIN room akan dibuat otomatis secara acak setelah Anda menekan tombol "Buat Room".
                </div>

                <button type="submit" class="neo-btn w-full py-3.5 sm:py-4 rounded-xl bg-[#4ADE80] hover:bg-green-400 !text-black font-display font-black text-sm sm:text-base uppercase tracking-wider mt-2 shadow-[3px_3px_0px_#000] sm:shadow-[4px_4px_0px_#000]">
                    🚀 Buat Room Sekarang
                </button>
            @endif
        </div>
    </form>
</div>
@endsection
