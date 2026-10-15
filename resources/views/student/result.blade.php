@extends('layouts.neo')

@section('title', 'Hasil Game')
@section('page-title', 'Hasil Akhir')
@section('page-subtitle', $room->game->title)

@section('content')
<div class="w-full max-w-4xl mx-auto flex flex-col gap-4 sm:gap-6">

    {{-- My Score Card --}}
    @php $rankColors = ['bg-[#FFE600]', 'bg-stone-300', 'bg-[#FB923C]']; @endphp
    <div class="neo-box rounded-2xl p-5 sm:p-8 text-center flex flex-col items-center gap-3 sm:gap-4
        {{ $myRank === 1 ? 'bg-[#FFE600]' : ($myRank === 2 ? 'bg-stone-200 dark:bg-slate-700' : ($myRank === 3 ? 'bg-[#FB923C]' : 'bg-white dark:bg-slate-800')) }}">

        <div class="text-4xl sm:text-6xl">
            {{ $myRank === 1 ? '🥇' : ($myRank === 2 ? '🥈' : ($myRank === 3 ? '🥉' : '🎮')) }}
        </div>

        <div>
            <p class="font-mono font-black text-xs sm:text-sm text-black dark:text-white uppercase tracking-widest">Peringkat Kamu</p>
            <p class="font-display font-black text-4xl sm:text-6xl text-black dark:text-white">#{{ $myRank }}</p>
        </div>

        <div class="neo-box bg-white dark:bg-slate-900 px-5 sm:px-8 py-3 sm:py-4 rounded-2xl flex flex-col items-center w-full max-w-xs">
            <span class="font-mono font-black text-xs text-stone-500 dark:text-slate-400 uppercase">Total Skor</span>
            <span class="font-mono font-black text-2xl sm:text-4xl text-black dark:text-white">{{ $myScore?->total_score ?? 0 }}</span>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs font-mono text-[#4ADE80] font-black">{{ $summary['correct'] ?? ($myScore?->correct ?? 0) }} Benar</span>
                <span class="text-xs font-mono text-stone-400">•</span>
                <span class="text-xs font-mono text-[#FB7185] font-black">{{ $summary['wrong'] ?? 0 }} Salah</span>
                @if(($summary['unanswered'] ?? 0) > 0)
                <span class="text-xs font-mono text-stone-400">•</span>
                <span class="text-xs font-mono text-stone-500 font-black">{{ $summary['unanswered'] }} Kosong</span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="#riwayat-soal" class="neo-btn px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-[#FFE600] text-black font-display font-black text-xs sm:text-sm uppercase tracking-wider flex items-center gap-2 shadow-[3px_3px_0px_#000]">
                <span class="material-symbols-outlined text-base">fact_check</span> Lihat Pembahasan Soal ({{ $summary['total'] ?? 0 }})
            </a>
            <a href="{{ route('student.join') }}" class="neo-btn px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-[#2563EB] text-white font-display font-black text-xs sm:text-sm uppercase tracking-wider shadow-[3px_3px_0px_#000]">
                Masuk Room Lain
            </a>
        </div>
    </div>

    {{-- ===== RIWAYAT & PEMBAHASAN SOAL ===== --}}
    @if(isset($reviewItems) && $reviewItems->isNotEmpty())
    <div id="riwayat-soal" class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden scroll-mt-6">
        {{-- Header Section --}}
        <div class="px-5 sm:px-6 py-4 border-b-3 border-black bg-[#FEF9EF] dark:bg-slate-900 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FFE600] border-2 border-black flex items-center justify-center text-xl shadow-[2px_2px_0px_#000] shrink-0">
                    📝
                </div>
                <div>
                    <h2 class="font-display font-black text-base sm:text-lg text-black dark:text-white uppercase flex items-center gap-2">
                        Riwayat & Pembahasan Soal
                    </h2>
                    <p class="text-xs font-mono font-bold text-stone-500 dark:text-slate-400">
                        Cek jawaban kamu dan pelajari kunci jawaban yang benar
                    </p>
                </div>
            </div>

            {{-- Summary Chips --}}
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="neo-tag px-2.5 py-1 rounded-lg bg-[#4ADE80] text-black text-[11px] sm:text-xs font-mono font-black flex items-center gap-1">
                    ✓ {{ $summary['correct'] }} Benar
                </span>
                <span class="neo-tag px-2.5 py-1 rounded-lg bg-[#FB7185] text-black text-[11px] sm:text-xs font-mono font-black flex items-center gap-1">
                    ✕ {{ $summary['wrong'] }} Salah
                </span>
                @if($summary['unanswered'] > 0)
                <span class="neo-tag px-2.5 py-1 rounded-lg bg-stone-200 dark:bg-slate-700 text-stone-700 dark:text-stone-300 text-[11px] sm:text-xs font-mono font-black flex items-center gap-1">
                    ⏰ {{ $summary['unanswered'] }} Kosong
                </span>
                @endif
                <span class="neo-tag px-2.5 py-1 rounded-lg bg-[#2563EB] text-white text-[11px] sm:text-xs font-mono font-black flex items-center gap-1">
                    🎯 {{ $summary['accuracy'] }}% Akurasi
                </span>
            </div>
        </div>

        {{-- Interactive Filter Tabs --}}
        <div class="flex items-center gap-2 px-4 sm:px-6 py-2.5 bg-stone-100 dark:bg-slate-950 border-b-2 border-black/10 dark:border-slate-700 overflow-x-auto">
            <span class="text-[11px] font-display font-black uppercase text-stone-500 dark:text-slate-400 shrink-0">Filter:</span>
            <button type="button" onclick="filterReview('all')" id="filter-btn-all" class="review-filter-btn neo-tag px-3 py-1 rounded-lg bg-black text-white text-xs font-mono font-black transition cursor-pointer shrink-0">
                Semua ({{ $summary['total'] }})
            </button>
            <button type="button" onclick="filterReview('correct')" id="filter-btn-correct" class="review-filter-btn neo-tag px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-black dark:text-white border border-black text-xs font-mono font-black transition hover:bg-[#4ADE80] cursor-pointer shrink-0">
                Benar ({{ $summary['correct'] }})
            </button>
            <button type="button" onclick="filterReview('wrong')" id="filter-btn-wrong" class="review-filter-btn neo-tag px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-black dark:text-white border border-black text-xs font-mono font-black transition hover:bg-[#FB7185] cursor-pointer shrink-0">
                Salah ({{ $summary['wrong'] }})
            </button>
            @if($summary['unanswered'] > 0)
            <button type="button" onclick="filterReview('unanswered')" id="filter-btn-unanswered" class="review-filter-btn neo-tag px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-black dark:text-white border border-black text-xs font-mono font-black transition hover:bg-stone-200 cursor-pointer shrink-0">
                Kosong ({{ $summary['unanswered'] }})
            </button>
            @endif
        </div>

        {{-- Question Items List --}}
        <div id="review-list" class="divide-y-3 divide-black/10 dark:divide-slate-700">
            @foreach($reviewItems as $item)
            @php
                $statusColor = match($item->status) {
                    'correct' => 'border-l-8 border-l-[#4ADE80]',
                    'wrong' => 'border-l-8 border-l-[#FB7185]',
                    default => 'border-l-8 border-l-stone-400 dark:border-l-slate-600',
                };
            @endphp
            <div class="review-card p-5 sm:p-6 flex flex-col gap-3.5 transition {{ $statusColor }}" data-status="{{ $item->status }}">
                {{-- Top Badge Row --}}
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-black text-white font-mono font-black text-xs flex items-center justify-center">
                            {{ $item->index }}
                        </span>
                        <span class="font-display font-black text-xs uppercase text-stone-600 dark:text-slate-300">
                            Soal {{ $item->index }} dari {{ $summary['total'] }}
                        </span>
                        @if($item->question->type && $item->question->type !== 'multiple_choice')
                            <span class="neo-tag px-2 py-0.5 rounded text-[10px] font-mono font-black bg-[#A855F7] text-white uppercase">
                                {{ $item->question->type === 'audio' ? '🎵 Tebak Lagu' : ($item->question->type === 'image' ? '🖼️ Gambar' : ($item->question->type === 'true_false' ? '⚖️ Benar/Salah' : 'Campuran')) }}
                            </span>
                        @endif
                    </div>

                    {{-- Status Badge --}}
                    <div>
                        @if($item->status === 'correct')
                            <span class="neo-tag px-3 py-1 rounded-xl bg-[#4ADE80] text-black text-xs font-mono font-black flex items-center gap-1 shadow-[2px_2px_0px_#000]">
                                <span class="material-symbols-outlined text-sm">check_circle</span> BENAR (+{{ $item->score_earned }} pts)
                            </span>
                        @elseif($item->status === 'wrong')
                            <span class="neo-tag px-3 py-1 rounded-xl bg-[#FB7185] text-black text-xs font-mono font-black flex items-center gap-1 shadow-[2px_2px_0px_#000]">
                                <span class="material-symbols-outlined text-sm">cancel</span> SALAH (+0 pts)
                            </span>
                        @else
                            <span class="neo-tag px-3 py-1 rounded-xl bg-stone-200 dark:bg-slate-700 text-stone-800 dark:text-stone-200 text-xs font-mono font-black flex items-center gap-1 shadow-[2px_2px_0px_#000]">
                                <span class="material-symbols-outlined text-sm">timer_off</span> TIDAK DIJAWAB (+0 pts)
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Question Text --}}
                <div class="font-display font-black text-base sm:text-lg text-black dark:text-white leading-snug">
                    {{ $item->question->question_text }}
                </div>

                {{-- Question Media (Audio / Image) --}}
                @if($item->question->formatted_media_url)
                    @if($item->question->type === 'audio')
                    <div class="p-3 bg-stone-50 dark:bg-slate-900 rounded-xl border-2 border-black inline-flex flex-col gap-1.5 w-full max-w-md">
                        <span class="text-xs font-display font-black uppercase text-stone-700 dark:text-slate-300 flex items-center gap-1.5">
                            🎵 Potongan Lagu Soal:
                        </span>
                        <audio controls class="w-full h-9 rounded border border-black/40" src="{{ $item->question->formatted_media_url }}"></audio>
                    </div>
                    @else
                    <div class="max-h-48 max-w-sm rounded-xl border-2 border-black overflow-hidden bg-stone-50 dark:bg-slate-900 p-1 inline-block">
                        <img src="{{ $item->question->formatted_media_url }}" alt="Gambar Soal" class="max-h-44 w-auto object-contain mx-auto rounded-lg">
                    </div>
                    @endif
                @endif

                {{-- Alert for unanswered question --}}
                @if($item->status === 'unanswered')
                <div class="text-xs font-mono font-bold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/40 px-3 py-2 rounded-xl border border-amber-300 dark:border-amber-700 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Kamu tidak sempat mengirimkan jawaban untuk soal ini (waktu habis / tereliminasi).
                </div>
                @endif

                {{-- Options Breakdown --}}
                <div class="mt-1 flex flex-col gap-2">
                    <span class="text-[11px] font-display font-black uppercase text-stone-500 dark:text-slate-400">
                        Pilihan Jawaban & Pembahasan:
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($item->question->options as $option)
                        @php
                            $isMyAnswer = $item->selected_option && $item->selected_option->id === $option->id;
                            $isCorrect = (bool) $option->is_correct;

                            if ($isMyAnswer && $isCorrect) {
                                $boxStyle = 'bg-[#4ADE80] text-black border-2 border-black shadow-[2px_2px_0px_#000]';
                                $badgeHtml = '<span class="neo-tag px-2 py-0.5 rounded bg-black text-[#4ADE80] text-[10px] font-mono font-black shrink-0">✓ JAWABAN KAMU (TEPAT)</span>';
                            } elseif ($isMyAnswer && ! $isCorrect) {
                                $boxStyle = 'bg-[#FB7185] text-black border-2 border-black shadow-[2px_2px_0px_#000]';
                                $badgeHtml = '<span class="neo-tag px-2 py-0.5 rounded bg-black text-[#FB7185] text-[10px] font-mono font-black shrink-0">✕ JAWABAN KAMU (SALAH)</span>';
                            } elseif ($isCorrect) {
                                $boxStyle = 'bg-[#4ADE80]/20 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 border-2 border-[#4ADE80] font-bold';
                                $badgeHtml = '<span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-black shrink-0">✓ KUNCI JAWABAN BENAR</span>';
                            } else {
                                $boxStyle = 'bg-stone-50 dark:bg-slate-900/60 text-stone-500 dark:text-slate-400 border border-black/10 dark:border-slate-700 opacity-60';
                                $badgeHtml = '';
                            }
                        @endphp
                        <div class="p-3 rounded-xl flex items-center justify-between gap-3 {{ $boxStyle }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-md border border-black/30 flex items-center justify-center font-mono font-bold text-xs shrink-0 {{ $isCorrect ? 'bg-[#4ADE80] text-black' : ($isMyAnswer ? 'bg-[#FB7185] text-black' : 'bg-black/10 text-stone-500') }}">
                                    {{ $isCorrect ? '✓' : ($isMyAnswer ? '✕' : '•') }}
                                </span>
                                <span class="font-display font-bold text-sm truncate {{ $isMyAnswer || $isCorrect ? 'font-black' : '' }}">
                                    {{ $option->option_text }}
                                </span>
                            </div>
                            {!! $badgeHtml !!}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Empty Filter State --}}
        <div id="filter-empty-state" class="hidden py-12 text-center">
            <span class="text-4xl">🔍</span>
            <p class="font-display font-black text-sm text-stone-400 dark:text-slate-500 mt-2">Tidak ada soal pada kategori filter ini.</p>
        </div>
    </div>
    @endif

    {{-- Team Leaderboard --}}
    @if(isset($teamLeaderboard) && $teamLeaderboard->isNotEmpty())
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b-3 border-black bg-[#2563EB] text-white flex items-center gap-3">
            <span class="text-2xl">🤝</span>
            <h2 class="font-display font-black text-base uppercase">Leaderboard Tim</h2>
        </div>
        <div class="divide-y-2 divide-black/10 dark:divide-slate-700">
            @foreach($teamLeaderboard as $index => $t)
            <div class="px-6 py-4 flex items-center gap-4 {{ $myTeam === $t->team ? 'bg-[#2563EB]/10 dark:bg-[#2563EB]/30' : '' }}">
                <div class="font-display font-black text-xl w-8 text-center text-black dark:text-white">{{ $t->rank }}</div>
                <div class="flex-1">
                    <div class="font-display font-black text-lg text-black dark:text-white">{{ $t->team }}</div>
                    @if($myTeam === $t->team)
                        <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-black">TIM KAMU</span>
                    @endif
                </div>
                <div class="font-mono font-black text-2xl text-black dark:text-white">{{ number_format($t->total_score) }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Individual Leaderboard --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b-3 border-black bg-[#FEF9EF] dark:bg-slate-900 flex items-center gap-3">
            <span class="text-2xl">🏆</span>
            <h2 class="font-display font-black text-base text-black dark:text-white uppercase">Leaderboard Room</h2>
            <span class="neo-tag px-2 py-0.5 rounded bg-[#2563EB] text-white text-xs font-mono font-black ml-auto">
                {{ $leaderboard->count() }} Siswa
            </span>
        </div>

        @if($leaderboard->isEmpty())
            <div class="py-12 text-center">
                <p class="font-display font-black text-stone-400 dark:text-slate-500">Belum ada data hasil.</p>
            </div>
        @else
            @php
                $top3 = $leaderboard->take(3);
                $others = $leaderboard->skip(3);
                $first = $top3->get(0);
                $second = $top3->get(1);
                $third = $top3->get(2);
            @endphp

            {{-- Podium 3 Besar --}}
            <div class="pt-8 pb-4 bg-white dark:bg-slate-800 flex items-end justify-center gap-3 px-4 border-b-3 border-black">
                {{-- Juara 2 --}}
                @if($second)
                <div class="flex flex-col items-center flex-1 max-w-[100px]">
                    <div class="w-10 h-10 rounded-full border-2 border-black bg-stone-200 dark:bg-slate-700 flex items-center justify-center font-display font-black text-lg mb-1 shadow-[2px_2px_0px_#000]">
                        <x-avatar :code="$second->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    <span class="font-display font-black text-[10px] text-black dark:text-white truncate w-full text-center">{{ $second->user->name }}</span>
                    <span class="font-mono font-bold text-[9px] text-stone-500 dark:text-slate-400 mb-1">{{ number_format($second->total_score) }}</span>
                    <div class="w-full bg-stone-300 dark:bg-slate-600 h-16 border-2 border-black rounded-t-lg flex justify-center pt-1 shadow-[2px_2px_0px_#000]">
                        <span class="font-display font-black text-xl text-black dark:text-white">2</span>
                    </div>
                </div>
                @endif

                {{-- Juara 1 --}}
                @if($first)
                <div class="flex flex-col items-center flex-1 max-w-[120px] z-10">
                    <span class="text-3xl mb-0 animate-bounce">👑</span>
                    <div class="w-14 h-14 rounded-full border-3 border-black bg-[#FFE600] flex items-center justify-center font-display font-black text-xl mb-1 shadow-[3px_3px_0px_#000]">
                        <x-avatar :code="$first->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    <span class="font-display font-black text-xs text-black dark:text-white truncate w-full text-center">{{ $first->user->name }}</span>
                    <span class="font-mono font-black text-[10px] text-[#2563EB] dark:text-blue-400 mb-1">{{ number_format($first->total_score) }}</span>
                    <div class="w-full bg-[#FFE600] h-24 border-2 border-black rounded-t-lg flex justify-center pt-1 shadow-[3px_3px_0px_#000]">
                        <span class="font-display font-black text-2xl text-black">1</span>
                    </div>
                </div>
                @endif

                {{-- Juara 3 --}}
                @if($third)
                <div class="flex flex-col items-center flex-1 max-w-[100px]">
                    <div class="w-10 h-10 rounded-full border-2 border-black bg-[#FB923C] flex items-center justify-center font-display font-black text-lg mb-1 shadow-[2px_2px_0px_#000]">
                        <x-avatar :code="$third->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    <span class="font-display font-black text-[10px] text-black dark:text-white truncate w-full text-center">{{ $third->user->name }}</span>
                    <span class="font-mono font-bold text-[9px] text-stone-500 dark:text-slate-400 mb-1">{{ number_format($third->total_score) }}</span>
                    <div class="w-full bg-[#FB923C] h-12 border-2 border-black rounded-t-lg flex justify-center pt-1 shadow-[2px_2px_0px_#000]">
                        <span class="font-display font-black text-xl text-black">3</span>
                    </div>
                </div>
                @endif
            </div>

            <div class="divide-y-2 divide-black/10 dark:divide-slate-700 max-h-64 overflow-y-auto">
                @foreach($others as $rank => $entry)
                @php
                    $isMe = $entry->user_id === auth()->id();
                @endphp
                <div class="px-6 py-4 flex items-center gap-4 {{ $isMe ? 'bg-[#FFE600]/30 dark:bg-yellow-500/20' : '' }}">
                    {{-- Rank --}}
                    <div class="w-8 h-8 rounded-lg font-display font-black text-sm flex items-center justify-center shrink-0 bg-white dark:bg-slate-900 text-black dark:text-white border-2 border-black">
                        {{ $rank + 4 }}
                    </div>
                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full border-2 border-black bg-[#FEF9EF] dark:bg-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                        <x-avatar :code="$entry->user->avatar ?? 'cow|none'" size="sm" />
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-display font-bold text-sm text-black dark:text-white truncate">{{ $entry->user->name ?? 'Siswa' }}</span>
                            @if($isMe)
                                <span class="neo-tag px-1.5 py-0.5 rounded bg-[#2563EB] text-white text-[9px] font-mono font-black">KAMU</span>
                            @endif
                        </div>
                        <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400 font-medium">{{ $entry->correct }} benar</span>
                    </div>
                    {{-- Skor --}}
                    <span class="font-mono font-black text-base text-black dark:text-white">{{ number_format($entry->total_score) }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function filterReview(status) {
    const buttons = document.querySelectorAll('.review-filter-btn');
    buttons.forEach(btn => {
        btn.classList.remove('bg-black', 'text-white');
        btn.classList.add('bg-white', 'text-black', 'dark:bg-slate-800', 'dark:text-white');
    });

    const activeBtn = document.getElementById('filter-btn-' + status);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'text-black', 'dark:bg-slate-800', 'dark:text-white');
        activeBtn.classList.add('bg-black', 'text-white');
    }

    const cards = document.querySelectorAll('.review-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        if (status === 'all' || cardStatus === status) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    const emptyState = document.getElementById('filter-empty-state');
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
}
</script>
@endpush
@endsection
