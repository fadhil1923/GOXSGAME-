@extends('layouts.neo')

@section('title', 'AI Auto-Generate Soal')
@section('page-title', '✨ AI Generator')
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
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 border-b-2 sm:border-b-3 border-black pb-4 sm:pb-6">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-[#A855F7] rounded-xl sm:rounded-2xl border-2 sm:border-3 border-black flex items-center justify-center text-2xl sm:text-3xl shadow-[3px_3px_0px_#000] shrink-0">
                🤖
            </div>
            <div>
                <h2 class="font-display font-black text-lg sm:text-xl lg:text-2xl text-black dark:text-white">Generate Soal Otomatis via File</h2>
                <p class="font-body text-stone-600 dark:text-slate-300 text-xs sm:text-sm mt-0.5">Upload file dokumen atau materi (PDF, TXT, DOCX, JPG/PNG), AI akan merangkum dan membuatkan soal secara otomatis!</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 sm:p-4 rounded-xl bg-[#4ADE80] border-2 border-black font-body font-bold text-xs sm:text-sm text-black text-center">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-3 sm:p-4 rounded-xl bg-[#FB7185] border-2 border-black font-body font-bold text-xs sm:text-sm text-black text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('manage.games.questions.ai_import.store', $game) }}" method="POST" enctype="multipart/form-data" id="ai-form">
            @csrf
            <div class="flex flex-col gap-5 sm:gap-6">
                {{-- File Upload --}}
                <div class="flex flex-col gap-2">
                    <label class="font-display font-black text-black dark:text-white text-xs sm:text-sm uppercase tracking-wide">Upload File Materi</label>
                    <div class="relative group">
                        <input type="file" name="file" id="file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".pdf,.txt,.docx,.doc,.jpg,.jpeg,.png,.csv">
                        <div class="neo-input rounded-xl bg-[#FEF9EF] dark:bg-slate-900 p-6 sm:p-8 md:p-10 text-center border-dashed border-2 sm:border-3 border-stone-300 dark:border-slate-700 group-hover:bg-[#FFF] dark:group-hover:bg-slate-800 group-hover:border-black dark:group-hover:border-slate-500 transition flex flex-col items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-4xl sm:text-5xl text-stone-400 group-hover:text-[#A855F7] transition" id="upload-icon">upload_file</span>
                            <span class="font-display font-black text-sm sm:text-base text-stone-700 dark:text-slate-200" id="file-name">Pilih atau Tarik File Materi Kesini</span>
                            <span class="font-mono text-[11px] sm:text-xs text-stone-400 dark:text-slate-500">Format didukung: PDF, DOCX, TXT, JPG, PNG (Maks. 5MB)</span>
                        </div>
                    </div>
                </div>

                {{-- Jumlah Soal --}}
                <div class="flex flex-col gap-2">
                    <label class="font-display font-black text-black dark:text-white text-xs sm:text-sm uppercase tracking-wide">Jumlah Soal yang Dibuat</label>
                    <select name="count" class="neo-input w-full rounded-xl p-3 sm:p-3.5 font-body text-sm sm:text-base bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white">
                        <option value="3">3 Soal</option>
                        <option value="5" selected>5 Soal</option>
                        <option value="10">10 Soal</option>
                    </select>
                </div>

                <div class="mt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                    <a href="{{ route('manage.games.questions.index', $game) }}" class="neo-btn order-2 sm:order-1 px-6 py-3 sm:py-3.5 rounded-xl bg-white dark:bg-slate-700 text-black dark:text-white font-display font-black text-xs sm:text-sm uppercase text-center shadow-[3px_3px_0px_#000]">
                        Batal
                    </a>
                    <button type="submit" id="btn-submit" class="neo-btn order-1 sm:order-2 flex-1 sm:flex-none px-6 sm:px-8 py-3.5 rounded-xl bg-[#A855F7] text-white font-display font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-[4px_4px_0px_#000]">
                        <span class="material-symbols-outlined text-lg">auto_awesome</span>
                        Generate Soal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const fileInput = document.getElementById('file');
    const fileName = document.getElementById('file-name');
    const uploadIcon = document.getElementById('upload-icon');
    const form = document.getElementById('ai-form');
    const btnSubmit = document.getElementById('btn-submit');

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            fileName.textContent = e.target.files[0].name;
            uploadIcon.textContent = 'description';
            uploadIcon.classList.remove('text-stone-400');
            uploadIcon.classList.add('text-[#2563EB]');
        } else {
            fileName.textContent = 'Pilih atau Tarik File Kesini';
            uploadIcon.textContent = 'upload_file';
            uploadIcon.classList.add('text-stone-400');
            uploadIcon.classList.remove('text-[#2563EB]');
        }
    });

    form.addEventListener('submit', () => {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<span class="material-symbols-outlined text-lg animate-spin">refresh</span> Sedang Membaca AI...`;
        btnSubmit.classList.remove('bg-[#A855F7]');
        btnSubmit.classList.add('bg-stone-500');
    });
</script>
@endpush
@endsection
