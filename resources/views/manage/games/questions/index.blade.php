@extends('layouts.neo')

@section('title', 'Soal — ' . $game->title)
@section('page-title', 'Bank Soal')
@section('page-subtitle', $game->title)

@section('content')<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-2 sm:gap-3">
        <a href="{{ route('manage.games.index') }}" class="neo-back-btn px-3 py-2 rounded-xl font-display font-black text-xs uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Games
        </a>
        <span class="neo-tag px-3 py-1.5 rounded-lg bg-[#2563EB] text-white font-mono text-xs font-bold">
            {{ $questions->count() }} Soal
        </span>
    </div>
    <div class="grid grid-cols-3 sm:flex items-center gap-2">
        <a href="{{ route('manage.games.questions.ai_generate', $game) }}" class="neo-btn px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-[#FCD34D] text-black font-display font-black text-[11px] sm:text-sm uppercase tracking-wider flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000]">
            <span>💡</span> <span class="truncate">AI Topik</span>
        </a>
        <a href="{{ route('manage.games.questions.ai_import', $game) }}" class="neo-btn px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-[#A855F7] text-white font-display font-black text-[11px] sm:text-sm uppercase tracking-wider flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000]">
            <span>✨</span> <span class="truncate">AI File</span>
        </a>
        <a href="{{ route('manage.games.questions.create', $game) }}" class="neo-btn px-2.5 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-[#FFE600] text-black font-display font-black text-[11px] sm:text-sm uppercase tracking-wider flex items-center justify-center gap-1 shadow-[2px_2px_0px_#000]">
            <span class="material-symbols-outlined text-base">add</span>
            <span class="truncate">Tambah</span>
        </a>
    </div>
</div>

@if($questions->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 sm:py-24 rounded-2xl bg-white dark:bg-slate-800 border-3 sm:border-4 border-black shadow-[4px_4px_0px_#000000] sm:shadow-[6px_6px_0px_#000000] p-6 text-center">
        <div class="text-6xl sm:text-7xl mb-4">📝</div>
        <p class="font-display font-black text-xl sm:text-2xl text-black dark:text-white">Belum ada soal!</p>
        <p class="font-body text-stone-500 dark:text-slate-400 mt-1 mb-6 text-sm">Tambahkan soal pertama untuk game ini.</p>
        <a href="{{ route('manage.games.questions.create', $game) }}" class="neo-btn px-6 py-3 rounded-xl bg-[#2563EB] text-white font-display font-black text-sm uppercase shadow-[3px_3px_0px_#000]">
            + Tambah Soal
        </a>
    </div>
@else
    <div class="flex flex-col gap-4">
        @foreach($questions as $index => $question)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
            <div class="p-3.5 sm:p-5 flex flex-col gap-3.5">
                {{-- Baris Atas: Nomor Soal + Pertanyaan + Tombol Aksi --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2.5 sm:gap-3.5 flex-1 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-[#FFE600] text-black border-2 sm:border-3 border-black flex items-center justify-center font-mono font-black text-sm sm:text-lg shrink-0 shadow-[2px_2px_0px_#000000]">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex flex-col gap-1 flex-1 min-w-0">
                            <p class="font-display font-black text-sm sm:text-base text-black dark:text-white leading-snug break-words">{{ $question->question_text }}</p>
                        </div>
                    </div>
                    {{-- Actions Edit & Delete --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ route('manage.games.questions.edit', [$game, $question]) }}" class="neo-btn px-2.5 sm:px-3 py-1.5 rounded-xl bg-[#2563EB] text-white font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000] flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            <span class="hidden sm:inline">Edit</span>
                        </a>
                        <form method="POST" action="{{ route('manage.games.questions.destroy', [$game, $question]) }}" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="neo-btn px-2 sm:px-2.5 py-1.5 rounded-xl bg-[#FB7185] text-black font-display font-black text-xs uppercase shadow-[2px_2px_0px_#000] flex items-center justify-center" title="Hapus Soal">
                                <span class="material-symbols-outlined text-sm sm:text-base">delete</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Preview Media Langsung di Kartu Soal (Gambar / Audio) --}}
                @php
                    $mediaSrc = $question->formatted_media_url ?? $question->media_url;
                @endphp
                @if($mediaSrc)
                    @if($question->type === 'audio')
                        <div class="p-3 sm:p-3.5 rounded-xl bg-[#FEF9EF] dark:bg-slate-900 border-2 sm:border-3 border-black shadow-[3px_3px_0px_#000] flex flex-col gap-2.5 w-full max-w-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 font-display font-black text-xs uppercase text-stone-800 dark:text-white">
                                    <span class="text-lg">🎵</span> Pemutar Audio Kuis
                                </div>
                                <span class="neo-tag px-2 py-0.5 rounded bg-[#A855F7] text-white text-[10px] font-mono font-bold">Audio Track</span>
                            </div>
                            <audio controls preload="metadata" class="w-full" src="{{ $mediaSrc }}"></audio>
                            <div class="flex items-center justify-between text-[11px] font-mono text-stone-500 dark:text-slate-400">
                                <span>Klik play untuk dengar audio</span>
                                <a href="{{ $mediaSrc }}" target="_blank" class="hover:text-black dark:hover:text-white underline font-bold">Buka file ↗</a>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col gap-1.5 w-fit max-w-full">
                            <div class="flex items-center gap-1.5 font-display font-black text-xs uppercase text-stone-700 dark:text-slate-300">
                                <span class="text-sm">🖼️</span> Gambar Soal:
                            </div>
                            <div class="rounded-xl border-2 sm:border-3 border-black overflow-hidden bg-stone-100 dark:bg-slate-900 p-1 w-fit max-w-full shadow-[2px_2px_0px_#000] sm:shadow-[3px_3px_0px_#000]">
                                <img src="{{ $mediaSrc }}" alt="Media Soal" 
                                    class="max-h-44 sm:max-h-60 w-auto object-contain rounded-lg hover:scale-105 transition duration-300"
                                    loading="lazy"
                                    referrerpolicy="no-referrer"
                                    onerror="this.onerror=null; this.src='https://placehold.co/600x400/2563EB/FFFFFF?text=Preview+Gambar+Soal';">
                            </div>
                        </div>
                    @endif
                @elseif($question->type === 'image')
                    <div class="p-2.5 sm:p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-300 dark:border-amber-700 text-amber-900 dark:text-amber-200 font-mono text-xs flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span>🖼️</span> Soal tipe gambar belum memiliki media gambar.
                        </div>
                        <a href="{{ route('manage.games.questions.edit', [$game, $question]) }}" class="underline font-bold text-black dark:text-white hover:text-[#2563EB]">
                            Upload Gambar ↗
                        </a>
                    </div>
                @elseif($question->type === 'audio')
                    <div class="p-2.5 sm:p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 border-2 border-purple-300 dark:border-purple-700 text-purple-900 dark:text-purple-200 font-mono text-xs flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span>🎵</span> Soal tipe lagu belum memiliki media audio.
                        </div>
                        <a href="{{ route('manage.games.questions.edit', [$game, $question]) }}" class="underline font-bold text-black dark:text-white hover:text-[#2563EB]">
                            Upload Audio ↗
                        </a>
                    </div>
                @endif

                {{-- Pilihan jawaban --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    @php 
                        $colors = ['#EF4444', '#3B82F6', '#FACC15', '#10B981']; 
                        $shapes = ['change_history', 'check_box_outline_blank', 'circle', 'star'];
                    @endphp
                    @foreach($question->options as $index => $option)
                    <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border-2 sm:border-3 border-black {{ $option->is_correct ? 'bg-emerald-100/70 dark:bg-emerald-950/70 border-emerald-600 dark:border-emerald-400 shadow-[3px_3px_0px_#000000] scale-[1.01]' : 'bg-stone-50 dark:bg-slate-900/60 opacity-80' }} transition">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg border-2 border-black flex items-center justify-center shrink-0" style="background-color: {{ $colors[$index % 4] }}">
                            <span class="material-symbols-outlined text-white text-base sm:text-lg font-black">{{ $shapes[$index % 4] }}</span>
                        </div>
                        <span class="text-xs sm:text-sm font-body font-bold text-black dark:text-white flex-1 leading-snug break-words">
                            {{ $option->option_text }}
                        </span>
                        @if($option->is_correct)
                            <span class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#4ADE80] text-black font-mono font-black text-[10px] border-2 border-black shrink-0 shadow-[1px_1px_0px_#000]">
                                <span class="material-symbols-outlined text-xs">check</span>
                                BENAR
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Meta & Type Badge Footer --}}
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-2 border-t-2 border-black/10 dark:border-white/10">
                    @if($question->type === 'true_false')
                        <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-bold">
                            ⚖️ Benar / Salah
                        </span>
                    @elseif($question->type === 'image')
                        <span class="neo-tag px-2 py-0.5 rounded bg-[#FACC15] text-black text-[10px] font-mono font-bold">
                            🖼️ Tebak Gambar
                        </span>
                    @elseif($question->type === 'audio')
                        <span class="neo-tag px-2 py-0.5 rounded bg-[#A855F7] text-white text-[10px] font-mono font-bold">
                            🎵 Tebak Lagu
                        </span>
                    @else
                        <span class="neo-tag px-2 py-0.5 rounded bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-200 text-[10px] font-mono font-bold">
                            🧠 Pilihan Ganda
                        </span>
                    @endif

                    @if($question->media_url)
                        <a href="{{ $question->media_url }}" target="_blank" class="neo-tag px-2 py-0.5 rounded bg-stone-200 dark:bg-slate-700 text-black dark:text-white text-[10px] font-mono font-bold hover:bg-[#FFE600] dark:hover:bg-[#FFE600] dark:hover:text-black transition flex items-center gap-1">
                            🔗 Media
                        </a>
                    @endif

                    <span class="neo-tag px-2 py-0.5 rounded bg-[#06B6D4] text-black text-[10px] font-mono font-bold">
                        ⏱ {{ $question->time_limit_seconds }}s
                    </span>
                    <span class="neo-tag px-2 py-0.5 rounded bg-[#A855F7] text-white text-[10px] font-mono font-bold">
                        +{{ $question->points }} pts
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
