@extends('layouts.neo')

@section('title', 'Edit Game')
@section('page-title', 'Edit Game')
@section('page-subtitle', 'Perbarui detail game edukasi')

@section('content')
<div class="w-full flex flex-col gap-4">
    <div>
        <a href="{{ route('manage.games.index') }}" class="neo-back-btn inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Game
        </a>
    </div>

    <form method="POST" action="{{ route('manage.games.update', $game) }}" class="w-full neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-6 lg:p-8 flex flex-col gap-5 sm:gap-6">
        @csrf
        @method('PATCH')

        {{-- Row 1: Judul Game --}}
        <div class="flex flex-col gap-2">
            <label for="title" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">Judul Game <span class="text-[#FB7185]">*</span></label>
            <input id="title" type="text" name="title" value="{{ old('title', $game->title) }}"
                class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 text-sm sm:text-base w-full"
                required>
        </div>

        {{-- Row 2: Deskripsi --}}
        <div class="flex flex-col gap-2">
            <label for="description" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">Deskripsi <span class="text-[#FB7185]">*</span></label>
            <textarea id="description" name="description" rows="3"
                class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 text-sm sm:text-base w-full resize-none"
                required>{{ old('description', $game->description) }}</textarea>
        </div>

        {{-- Row 3: Grid 2 Kolom untuk Detail Game & Thumbnail --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
            {{-- Kolom Kiri: Mata Pelajaran, URL, Kelas & Kesulitan --}}
            <div class="flex flex-col gap-4 sm:gap-5">
                <div class="flex flex-col gap-2">
                    <label for="subject_id" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">Mata Pelajaran <span class="text-[#FB7185]">*</span></label>
                    <select id="subject_id" name="subject_id" class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white text-sm sm:text-base w-full" required>
                        <option value="">Pilih Mata Pelajaran...</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $game->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="game_url" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">URL Game (Opsional)</label>
                    <input id="game_url" type="url" name="game_url" value="{{ old('game_url', $game->game_url) }}"
                        class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 text-sm sm:text-base w-full"
                        placeholder="https://... (opsional)">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="grade_level" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">Tingkat Kelas</label>
                        <select id="grade_level" name="grade_level" class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white text-sm sm:text-base w-full">
                            <option value="">Semua Kelas</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="Kelas {{ $i }}" {{ old('grade_level', $game->grade_level) == "Kelas $i" ? 'selected' : '' }}>Kelas {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="difficulty" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">Tingkat Kesulitan</label>
                        <select id="difficulty" name="difficulty" class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white text-sm sm:text-base w-full">
                            <option value="">Semua Tingkat</option>
                            <option value="Easy" {{ old('difficulty', $game->difficulty) == 'Easy' ? 'selected' : '' }}>Easy 🌱</option>
                            <option value="Medium" {{ old('difficulty', $game->difficulty) == 'Medium' ? 'selected' : '' }}>Medium ⚡</option>
                            <option value="Hard" {{ old('difficulty', $game->difficulty) == 'Hard' ? 'selected' : '' }}>Hard 🔥</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="xp_reward" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">XP Reward <span class="text-[#FB7185]">*</span></label>
                    <input id="xp_reward" type="number" name="xp_reward" value="{{ old('xp_reward', $game->xp_reward) }}" min="0"
                        class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-mono font-bold text-black dark:text-white text-sm sm:text-base w-full"
                        required>
                </div>
            </div>

            {{-- Kolom Kanan: Thumbnail Game & Preview --}}
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between flex-wrap gap-1">
                    <label for="thumbnail_url" class="font-display font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-wide">Thumbnail Game</label>
                    <span class="text-[10px] sm:text-[11px] font-mono text-stone-500 dark:text-slate-400 font-bold">✨ Otomatis dari Mapel</span>
                </div>
                <input id="thumbnail_url" type="url" name="thumbnail_url" value="{{ old('thumbnail_url', $game->thumbnail_url) }}"
                    class="neo-input rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 font-body font-semibold text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 text-sm sm:text-base w-full"
                    placeholder="https://... (opsional, otomatis jika kosong)">
                
                <div id="thumb-preview-box" class="p-3 sm:p-4 rounded-xl border-2 sm:border-3 border-black bg-[#FEF9EF] dark:bg-slate-900 shadow-[3px_3px_0px_#000] flex flex-col gap-2.5 sm:gap-3">
                    <div class="w-full h-36 sm:h-44 rounded-lg bg-black border-2 border-black overflow-hidden relative shadow-[2px_2px_0px_#000]">
                        <img id="thumb-preview-img" src="{{ $game->auto_thumbnail }}" 
                             class="w-full h-full object-cover" alt="Preview Thumbnail">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display font-black text-xs sm:text-sm text-black dark:text-white">Preview Thumbnail Game</span>
                        <span id="thumb-preview-desc" class="text-[11px] sm:text-xs font-mono text-stone-500 dark:text-slate-400">Thumbnail yang sedang aktif untuk game ini.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="neo-btn flex-1 py-3.5 sm:py-4 rounded-xl bg-[#FFE600] hover:bg-[#FACC15] text-black font-display font-black text-sm sm:text-base uppercase tracking-wider shadow-[3px_3px_0px_#000] sm:shadow-[4px_4px_0px_#000]">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('manage.games.index') }}" class="neo-btn px-5 sm:px-6 py-3.5 sm:py-4 rounded-xl bg-white dark:bg-slate-700 text-black dark:text-white font-display font-black text-xs sm:text-sm uppercase shadow-[3px_3px_0px_#000] sm:shadow-[4px_4px_0px_#000]">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
const subjectThumbnails = @json(\App\Models\Game::defaultSubjectThumbnails());
const subjectSelect = document.getElementById('subject_id');
const thumbInput = document.getElementById('thumbnail_url');
const thumbImg = document.getElementById('thumb-preview-img');
const thumbDesc = document.getElementById('thumb-preview-desc');

function updateThumbnailPreview() {
    if (thumbInput.value.trim() !== '') {
        thumbImg.src = thumbInput.value.trim();
        thumbDesc.textContent = 'Menggunakan URL kustom yang kamu masukkan.';
        return;
    }

    const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
    const subjectName = selectedOption ? selectedOption.text : '';

    if (subjectName && subjectThumbnails[subjectName]) {
        thumbImg.src = subjectThumbnails[subjectName];
        thumbDesc.textContent = `Otomatis sesuai mata pelajaran: ${subjectName}`;
    } else {
        thumbImg.src = subjectThumbnails['Default'];
        thumbDesc.textContent = 'Otomatis thumbnail edukasi default.';
    }
}

subjectSelect.addEventListener('change', updateThumbnailPreview);
thumbInput.addEventListener('input', updateThumbnailPreview);
</script>
@endpush
@endsection
