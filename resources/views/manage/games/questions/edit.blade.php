@extends('layouts.neo')

@section('title', 'Edit Soal')
@section('page-title', 'Edit Soal')
@section('page-subtitle', $game->title)

@section('content')
<div class="w-full flex flex-col gap-4">
    <div>
        <a href="{{ route('manage.games.questions.index', $game) }}" class="neo-back-btn inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Soal
        </a>
    </div>

    <form method="POST" action="{{ route('manage.games.questions.update', [$game, $question]) }}" enctype="multipart/form-data" class="w-full neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-6 lg:p-8 flex flex-col gap-5 sm:gap-6" id="edit-question-form">
        @csrf
        @method('PATCH')

        @php
            $currentType = old('type', $question->type ?? 'multiple_choice');
            $mediaSrc = $question->formatted_media_url ?? $question->media_url;
        @endphp

        {{-- Tipe Soal --}}
        <div class="flex flex-col gap-2">
            <label class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                Tipe Kuis & Soal <span class="text-[#FB7185]">*</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="multiple_choice" class="sr-only peer" {{ $currentType === 'multiple_choice' ? 'checked' : '' }} onchange="handleTypeChange('multiple_choice')">
                    <div class="p-2.5 sm:p-3 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#2563EB] peer-checked:!text-white peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-[11px] sm:text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition">
                        <span class="text-xl sm:text-2xl">🧠</span>
                        <span>Pilihan Ganda</span>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="true_false" class="sr-only peer" {{ $currentType === 'true_false' ? 'checked' : '' }} onchange="handleTypeChange('true_false')">
                    <div class="p-2.5 sm:p-3 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#4ADE80] peer-checked:!text-black peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-[11px] sm:text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition">
                        <span class="text-xl sm:text-2xl">⚖️</span>
                        <span>Benar / Salah</span>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="image" class="sr-only peer" {{ $currentType === 'image' ? 'checked' : '' }} onchange="handleTypeChange('image')">
                    <div class="p-2.5 sm:p-3 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#FACC15] peer-checked:!text-black peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-[11px] sm:text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition">
                        <span class="text-xl sm:text-2xl">🖼️</span>
                        <span>Tebak Gambar</span>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="audio" class="sr-only peer" {{ $currentType === 'audio' ? 'checked' : '' }} onchange="handleTypeChange('audio')">
                    <div class="p-2.5 sm:p-3 rounded-xl border-2 sm:border-3 border-black text-center peer-checked:!bg-[#A855F7] peer-checked:!text-white peer-checked:shadow-[3px_3px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-[11px] sm:text-xs uppercase flex flex-col items-center gap-1 sm:gap-1.5 transition">
                        <span class="text-xl sm:text-2xl">🎵</span>
                        <span>Tebak Lagu</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- Pertanyaan --}}
        <div class="flex flex-col gap-2">
            <label for="question_text" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                Pertanyaan / Petunjuk <span class="text-[#FB7185]">*</span>
            </label>
            <textarea id="question_text" name="question_text" rows="3"
                class="neo-input rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-sm sm:text-base text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 w-full resize-none"
                required>{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        {{-- Upload / Ganti Media Container (Gambar / Audio) --}}
        <div id="media-container" class="{{ in_array($currentType, ['image', 'audio']) ? '' : 'hidden' }} flex flex-col gap-3 p-3.5 sm:p-4 rounded-xl bg-[#FEF9EF] dark:bg-slate-900 border-2 sm:border-3 border-black">
            <div class="flex items-center gap-2">
                <span id="media-icon" class="text-xl sm:text-2xl">{{ $currentType === 'audio' ? '🎵' : '🖼️' }}</span>
                <span id="media-title" class="font-display font-black text-xs sm:text-sm uppercase text-black dark:text-white">
                    {{ $currentType === 'audio' ? 'Media Audio / Lagu Kuis' : 'Media Gambar Kuis' }}
                </span>
            </div>

            @if($mediaSrc)
                <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-black flex flex-col gap-2">
                    <span class="text-[11px] sm:text-xs font-mono font-bold text-stone-500 dark:text-slate-400">Preview Media Saat Ini:</span>
                    @if($question->type === 'audio')
                        <audio controls preload="metadata" class="w-full" src="{{ $mediaSrc }}"></audio>
                    @else
                        <div class="rounded-lg border-2 border-black overflow-hidden bg-stone-100 dark:bg-slate-900 max-h-48 w-fit">
                            <img src="{{ $mediaSrc }}" alt="Media Soal" class="max-h-48 w-auto object-contain">
                        </div>
                    @endif
                </div>
            @endif
            
            <div class="flex flex-col gap-2">
                <label class="text-[11px] sm:text-xs font-mono font-bold text-stone-600 dark:text-slate-400">Ganti File dari Komputer/HP (Max: 10MB):</label>
                <input type="file" id="media_file" name="media_file" 
                    accept="{{ $currentType === 'audio' ? 'audio/*' : 'image/*' }}"
                    class="text-xs font-mono text-black dark:text-white file:mr-3 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-xl file:border-2 file:border-black file:bg-white dark:file:bg-slate-800 dark:file:text-white file:font-black file:cursor-pointer">
            </div>

            <div class="flex items-center gap-2 my-1">
                <span class="text-[11px] sm:text-xs font-mono font-bold text-stone-400 dark:text-slate-500">--- ATAU GANTI DENGAN LINK / URL ---</span>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[11px] sm:text-xs font-mono font-bold text-stone-600 dark:text-slate-400">URL Gambar/Audio Web:</label>
                <input type="url" id="media_url" name="media_url" value="{{ old('media_url', $question->media_url) }}" placeholder="https://example.com/audio.mp3 atau gambar.png"
                    class="neo-input rounded-xl px-3 py-2 text-xs font-mono w-full bg-white dark:bg-slate-800 text-black dark:text-white">
            </div>
        </div>

        {{-- Timer & Poin --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <div class="flex flex-col gap-1.5 sm:gap-2">
                <label for="time_limit_seconds" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">⏱ Waktu (detik)</label>
                <input id="time_limit_seconds" type="number" name="time_limit_seconds"
                    value="{{ old('time_limit_seconds', $question->time_limit_seconds) }}" min="5" max="300"
                    class="neo-input rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-mono font-bold text-sm sm:text-base text-black dark:text-white w-full" required>
            </div>
            <div class="flex flex-col gap-1.5 sm:gap-2">
                <label for="points" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">🏆 Poin</label>
                <input id="points" type="number" name="points"
                    value="{{ old('points', $question->points) }}" min="0"
                    class="neo-input rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-mono font-bold text-sm sm:text-base text-black dark:text-white w-full" required>
            </div>
        </div>

        {{-- Opsi Benar / Salah --}}
        @php
            $isTrueCorrect = true;
            if ($question->type === 'true_false' && $question->options->count() >= 2) {
                $correctOpt = $question->options->firstWhere('is_correct', true);
                $isTrueCorrect = $correctOpt && strtoupper($correctOpt->option_text) === 'BENAR';
            }
        @endphp
        <div id="true-false-container" class="{{ $currentType === 'true_false' ? '' : 'hidden' }} flex flex-col gap-3">
            <label class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                Jawaban yang Benar <span class="text-[#FB7185]">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="tf_correct" value="true" class="sr-only peer" {{ $isTrueCorrect ? 'checked' : '' }}>
                    <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl border-3 sm:border-4 border-black text-center peer-checked:!bg-[#4ADE80] peer-checked:!text-black peer-checked:shadow-[3px_3px_0px_#000] sm:peer-checked:shadow-[4px_4px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-sm sm:text-lg uppercase flex items-center justify-center gap-2 transition opacity-50 peer-checked:opacity-100">
                        <span>✅</span> BENAR
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="tf_correct" value="false" class="sr-only peer" {{ ! $isTrueCorrect ? 'checked' : '' }}>
                    <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl border-3 sm:border-4 border-black text-center peer-checked:!bg-[#FB7185] peer-checked:!text-black peer-checked:shadow-[3px_3px_0px_#000] sm:peer-checked:shadow-[4px_4px_0px_#000] bg-stone-100 dark:bg-slate-900 text-black dark:text-white font-display font-black text-sm sm:text-lg uppercase flex items-center justify-center gap-2 transition opacity-50 peer-checked:opacity-100">
                        <span>❌</span> SALAH
                    </div>
                </label>
            </div>
        </div>

        {{-- Pilihan Jawaban 4 Opsi (Pilihan Ganda, Gambar, Audio) --}}
        <div id="multiple-choice-container" class="{{ $currentType === 'true_false' ? 'hidden' : '' }} flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <label class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                    Pilihan Jawaban <span class="text-[#FB7185]">*</span>
                </label>
                <span class="text-[11px] sm:text-xs font-mono text-stone-500 dark:text-slate-400">Klik icon warna untuk kunci jawaban</span>
            </div>

            @php
                $letters = ['A', 'B', 'C', 'D'];
                $colors = ['#EF4444', '#3B82F6', '#FACC15', '#10B981'];
                $shapes = ['change_history', 'check_box_outline_blank', 'circle', 'star'];
                $correctIndex = old('correct_option', $question->options->search(fn($o) => $o->is_correct));
                if ($correctIndex === false || $correctIndex === null) $correctIndex = 0;
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
                @for($i = 0; $i < 4; $i++)
                @php
                    $existingOpt = $question->options->get($i);
                    $optVal = old("options.$i.text", $existingOpt ? $existingOpt->option_text : '');
                @endphp
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <label class="cursor-pointer shrink-0" title="Tandai sebagai jawaban benar">
                        <input type="radio" name="correct_option" value="{{ $i }}"
                            {{ $correctIndex == $i ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl border-2 sm:border-3 border-black flex items-center justify-center transition 
                            peer-checked:shadow-[3px_3px_0px_#000] peer-checked:scale-105 opacity-40 peer-checked:opacity-100 hover:opacity-80"
                            style="background-color: {{ $colors[$i] }}">
                            <span class="material-symbols-outlined text-white font-black text-xl sm:text-2xl">{{ $shapes[$i] }}</span>
                        </div>
                    </label>
                    <div class="flex-1 relative">
                        <input type="text" name="options[{{ $i }}][text]"
                            value="{{ $optVal }}"
                            class="mc-input neo-input w-full rounded-xl pl-10 sm:pl-11 py-2.5 sm:py-3.5 bg-white dark:bg-slate-900 font-body font-bold text-sm sm:text-base text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500"
                            placeholder="Tulis pilihan {{ $letters[$i] }}..." {{ $currentType !== 'true_false' ? 'required' : '' }}>
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-display font-black text-lg sm:text-xl" style="color: {{ $colors[$i] }}">{{ $letters[$i] }}</span>
                    </div>
                </div>
                @endfor
            </div>
            <p class="text-[11px] sm:text-xs font-mono text-stone-500 dark:text-slate-400 mt-1">💡 Klik icon warna di sebelah kiri untuk memilih jawaban yang benar</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="submit" class="neo-btn flex-1 py-3 sm:py-3.5 rounded-xl bg-[#FFE600] text-black font-display font-black text-sm sm:text-base uppercase shadow-[3px_3px_0px_#000] sm:shadow-[4px_4px_0px_#000]">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('manage.games.questions.index', $game) }}" class="neo-btn px-6 py-3 sm:py-3.5 rounded-xl bg-white dark:bg-slate-700 text-black dark:text-white font-display font-black text-xs sm:text-sm uppercase text-center shadow-[3px_3px_0px_#000]">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function handleTypeChange(type) {
    const mediaContainer = document.getElementById('media-container');
    const mediaTitle = document.getElementById('media-title');
    const mediaIcon = document.getElementById('media-icon');
    const mediaFileInput = document.getElementById('media_file');
    const mcContainer = document.getElementById('multiple-choice-container');
    const tfContainer = document.getElementById('true-false-container');
    const mcInputs = document.querySelectorAll('.mc-input');

    if (type === 'true_false') {
        mcContainer.classList.add('hidden');
        tfContainer.classList.remove('hidden');
        mediaContainer.classList.add('hidden');
        mcInputs.forEach(input => input.removeAttribute('required'));
    } else {
        mcContainer.classList.remove('hidden');
        tfContainer.classList.add('hidden');
        mcInputs.forEach(input => input.setAttribute('required', 'required'));

        if (type === 'image') {
            mediaContainer.classList.remove('hidden');
            mediaTitle.textContent = 'Upload / Masukkan Gambar Kuis';
            mediaIcon.textContent = '🖼️';
            mediaFileInput.setAttribute('accept', 'image/*');
        } else if (type === 'audio') {
            mediaContainer.classList.remove('hidden');
            mediaTitle.textContent = 'Upload / Masukkan Lagu/Audio Kuis';
            mediaIcon.textContent = '🎵';
            mediaFileInput.setAttribute('accept', 'audio/*');
        } else {
            mediaContainer.classList.add('hidden');
        }
    }
}
</script>
@endpush
@endsection
