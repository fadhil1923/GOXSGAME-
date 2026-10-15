@extends('layouts.neo')

@section('title', 'AI Generate Soal per Topik')
@section('page-title', '✨ AI Generator by Topik')
@section('page-subtitle', 'Game: ' . $game->title)

@section('content')
<div class="w-full flex flex-col gap-4">
    <div>
        <a href="{{ route('manage.games.questions.index', $game) }}" class="neo-back-btn inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Bank Soal
        </a>
    </div>

    <div class="w-full neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-6 lg:p-8 flex flex-col gap-5 sm:gap-6">
        <div class="flex items-center gap-3 sm:gap-4 border-b-2 sm:border-b-3 border-black pb-4 sm:pb-6">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#FCD34D] rounded-2xl border-2 sm:border-3 border-black flex items-center justify-center text-2xl sm:text-3xl shadow-[3px_3px_0px_#000] shrink-0">
                💡
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="font-display font-black text-lg sm:text-2xl text-black dark:text-white truncate">Generate Soal (Topik / Bab)</h2>
                <p class="font-body text-stone-600 dark:text-slate-300 text-xs sm:text-sm mt-0.5 line-clamp-2 sm:line-clamp-none">Masukkan topik materi, tentukan format, dan biarkan AI meraciknya untukmu!</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 sm:p-4 rounded-xl bg-[#4ADE80] border-2 border-black font-body font-bold text-black text-xs sm:text-sm text-center">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-3.5 sm:p-4 rounded-xl bg-[#FB7185] border-2 border-black font-body font-bold text-black text-xs sm:text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('manage.games.questions.ai_generate.store', $game) }}" method="POST" id="ai-generate-form">
            @csrf
            <div class="flex flex-col gap-5 sm:gap-6">
                {{-- Keyword Input --}}
                <div class="flex flex-col gap-2">
                    <label class="font-display font-black text-black dark:text-white text-xs sm:text-sm uppercase tracking-wide">Topik / Bab Materi <span class="text-[#FB7185]">*</span></label>
                    <input type="text" name="keyword" required placeholder="Contoh: Tebak Lagu Taylor Swift, Musik Pop, Lagu Daerah, Tata Surya, Sejarah..." class="neo-input w-full rounded-xl p-3 sm:p-3.5 font-body text-sm bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500" autocomplete="off">
                </div>

                {{-- Tipe Soal yang Diinginkan --}}
                <div class="flex flex-col gap-2">
                    <label class="font-display font-black text-black dark:text-white text-xs sm:text-sm uppercase tracking-wide">Jenis / Format Game Soal <span class="text-[#FB7185]">*</span></label>
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="question_type" value="multiple_choice" checked class="sr-only peer">
                            <div class="p-3 sm:p-3.5 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#2563EB] peer-checked:!text-white peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition h-full justify-center">
                                <span class="text-xl sm:text-2xl">🧠</span>
                                <span>Pilihan Ganda</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="question_type" value="true_false" class="sr-only peer">
                            <div class="p-3 sm:p-3.5 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#4ADE80] peer-checked:!text-black peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition h-full justify-center">
                                <span class="text-xl sm:text-2xl">⚖️</span>
                                <span>Benar / Salah</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="question_type" value="image" class="sr-only peer">
                            <div class="p-3 sm:p-3.5 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#FACC15] peer-checked:!text-black peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition h-full justify-center">
                                <span class="text-xl sm:text-2xl">🖼️</span>
                                <span>Tebak Gambar</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="question_type" value="audio" class="sr-only peer">
                            <div class="p-3 sm:p-3.5 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#A855F7] peer-checked:!text-white peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition h-full justify-center">
                                <span class="text-xl sm:text-2xl">🎵</span>
                                <span>Tebak Musik</span>
                            </div>
                        </label>
                        <label class="cursor-pointer col-span-2 lg:col-span-1">
                            <input type="radio" name="question_type" value="mixed" class="sr-only peer">
                            <div class="p-3 sm:p-3.5 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#FB7185] peer-checked:!text-white peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition h-full justify-center">
                                <span class="text-xl sm:text-2xl">🎲</span>
                                <span>Campuran (Mix)</span>
                            </div>
                        </label>
                    </div>
                    <p class="text-[11px] sm:text-xs font-mono text-stone-500 dark:text-slate-400 mt-1">
                        💡 <strong>Kuis Global Otomatis:</strong> Semua format (Pilihan Ganda, Tebak Gambar, Tebak Musik, dan Campuran) otomatis mengambil materi global sesuai topikmu (misal: <em>Taylor Swift, Landmark Dunia, Tata Surya, Sains, Pop Culture</em>). Gambar & audio dicari otomatis!
                    </p>
                </div>

                {{-- Jumlah Soal --}}
                <div class="flex flex-col gap-2">
                    <label class="font-display font-black text-black dark:text-white text-sm uppercase tracking-wide">Jumlah Soal yang Dibuat</label>
                    <select name="count" class="neo-input w-full rounded-xl p-3.5 font-body bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white">
                        <option value="3">3 Soal</option>
                        <option value="5" selected>5 Soal</option>
                        <option value="10">10 Soal</option>
                        <option value="15">15 Soal</option>
                        <option value="20">20 Soal</option>
                    </select>
                </div>

                <div class="mt-2 flex items-center justify-between gap-4">
                    <a href="{{ route('manage.games.questions.index', $game) }}" class="neo-btn px-6 py-3.5 rounded-xl bg-white text-black font-display font-black text-sm uppercase shadow-[3px_3px_0px_#000]">
                        Batal
                    </a>
                    <button type="submit" id="btn-submit-generate" class="neo-btn flex-1 sm:flex-none px-8 py-3.5 rounded-xl bg-[#A855F7] text-white font-display font-black text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-[4px_4px_0px_#000]">
                        <span class="material-symbols-outlined text-lg">auto_awesome</span>
                        Generate Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const formGenerate = document.getElementById('ai-generate-form');
    const btnSubmitGenerate = document.getElementById('btn-submit-generate');

    formGenerate.addEventListener('submit', () => {
        btnSubmitGenerate.disabled = true;
        btnSubmitGenerate.innerHTML = `<span class="material-symbols-outlined text-lg animate-spin">refresh</span> AI Sedang Bekerja...`;
        btnSubmitGenerate.classList.remove('bg-[#A855F7]');
        btnSubmitGenerate.classList.add('bg-stone-500');
    });
</script>
@endpush
@endsection
