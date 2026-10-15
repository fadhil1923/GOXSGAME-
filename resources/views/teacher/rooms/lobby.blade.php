@extends('layouts.neo')

@section('title', 'Game Control — ' . $room->game->title)
@section('page-title', 'Game Control Panel')
@section('page-subtitle', $room->game->title . ' • PIN: ' . $room->room_code)

@push('styles')
<style>
    .btn-pulse { animation: pulse-border 1.5s infinite; }
    @keyframes pulse-border {
        0%, 100% { box-shadow: 4px 4px 0px #000, 0 0 0 0 rgba(74, 222, 128, 0.6); }
        50% { box-shadow: 4px 4px 0px #000, 0 0 0 12px rgba(74, 222, 128, 0); }
    }
    #teacher-timer { font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('content')
@php
    $session = $room->session;
    $questions = $room->game->questions;
    $isPlaying = $session && $session->isPlaying();
    $isFinished = $session && $session->isFinished();
    $currentIndex = $session ? $session->current_question_index : 0;
    $currentQuestion = $isPlaying ? $questions->get($currentIndex) : null;
    $totalQuestions = $questions->count();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ===== LEFT: Kontrol Utama ===== --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Status Bar --}}
        <div class="p-4 rounded-2xl border-4 border-black shadow-[5px_5px_0px_#000]
            {{ $isFinished ? 'bg-[#4ADE80]' : ($isPlaying ? 'bg-[#2563EB]' : 'bg-[#FFE600]') }}
            flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full {{ $isPlaying ? 'bg-white animate-ping' : 'bg-black' }}"></div>
                <span class="font-display font-black {{ $isPlaying ? 'text-white' : 'text-black' }} text-base uppercase tracking-wide">
                    @if($isFinished) 🏁 Game Selesai
                    @elseif($isPlaying) ▶ Game Berjalan — Soal {{ $currentIndex + 1 }} / {{ $totalQuestions }}
                    @else ⏳ Menunggu Dimulai
                    @endif
                </span>
            </div>
            @if($isPlaying)
                <div id="teacher-timer" class="font-mono font-black text-2xl text-white bg-black px-4 py-1 rounded-xl border-2 border-white">
                    --s
                </div>
            @endif
        </div>

        {{-- Kartu Soal Aktif --}}
        @if($isPlaying && $currentQuestion)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-6 flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <span class="neo-tag px-3 py-1 rounded-lg bg-[#FFE600] text-black text-xs font-mono font-black">
                    Soal {{ $currentIndex + 1 }} / {{ $totalQuestions }}
                </span>
                <span class="neo-tag px-3 py-1 rounded-lg bg-[#A855F7] text-white text-xs font-mono font-black">
                    +{{ $currentQuestion->points }} pts
                </span>
                <span class="neo-tag px-3 py-1 rounded-lg bg-[#06B6D4] text-black text-xs font-mono font-black">
                    ⏱ {{ $session->current_time_limit }}s
                </span>
            </div>
            <p class="font-display font-black text-xl text-black dark:text-white leading-snug">{{ $currentQuestion->question_text }}</p>
            <div class="grid grid-cols-2 gap-4 mt-2">
                @php 
                    $colors = ['#EF4444', '#3B82F6', '#FACC15', '#10B981']; 
                    $shapes = ['change_history', 'check_box_outline_blank', 'circle', 'star'];
                @endphp
                @foreach($currentQuestion->options as $i => $option)
                <div class="flex items-center gap-3 px-4 py-3 rounded-2xl border-4 border-black {{ $option->is_correct ? 'bg-white dark:bg-slate-700 shadow-[6px_6px_0px_#000000] scale-[1.02]' : 'bg-stone-50 dark:bg-slate-900/60 opacity-50' }} transition">
                    <div class="w-10 h-10 rounded-xl border-3 border-black flex items-center justify-center" style="background-color: {{ $colors[$i % 4] }}">
                        <span class="material-symbols-outlined text-white text-2xl font-black">{{ $shapes[$i % 4] }}</span>
                    </div>
                    <span class="text-lg font-body font-bold text-black dark:text-white flex-1">{{ $option->option_text }}</span>
                    @if($option->is_correct)
                        <span class="material-symbols-outlined text-green-500 font-black text-3xl drop-shadow-md">check_circle</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($isFinished)
        <div class="neo-box rounded-2xl bg-[#4ADE80] p-8 text-center flex flex-col items-center gap-4">
            <div class="text-6xl">🏆</div>
            <h3 class="font-display font-black text-2xl text-black">Game Selesai!</h3>
            <a href="{{ route('teacher.rooms.results', $room) }}" class="neo-btn px-8 py-3 rounded-xl bg-[#FFE600] text-black font-display font-black text-base uppercase tracking-wider">
                Lihat Leaderboard 🏆
            </a>
        </div>
        @endif

        {{-- Kontrol Aksi --}}
        <div class="flex flex-wrap gap-3">
            @if(! $isPlaying && ! $isFinished)
                @if($totalQuestions === 0)
                    <div class="w-full py-4 rounded-xl bg-[#FB7185] border-3 border-black text-center font-display font-black text-black">
                        ⚠️ Game belum punya soal! Hubungi Admin.
                    </div>
                @else
                    {{-- Timer Override --}}
                    <form method="POST" action="{{ route('teacher.rooms.play', $room) }}" class="w-full flex flex-col gap-4">
                        @csrf
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-[#FEF9EF] dark:bg-slate-900 border-3 border-black">
                            <label for="custom_time" class="font-display font-black text-sm text-black dark:text-white whitespace-nowrap">
                                ⏱ Override Waktu/Soal:
                            </label>
                            <input id="custom_time" type="number" name="custom_time" min="5" max="300"
                                placeholder="Default ({{ $room->game->questions->first()?->time_limit_seconds ?? 30 }}s)"
                                class="neo-input flex-1 rounded-xl px-3 py-2 bg-white dark:bg-slate-800 font-mono font-bold text-black dark:text-white text-sm">
                            <span class="text-xs font-mono text-stone-500 dark:text-slate-400">detik</span>
                        </div>
                        <button type="submit" id="start-btn"
                            class="neo-btn btn-pulse w-full py-4 rounded-xl bg-[#4ADE80] text-black font-display font-black text-xl uppercase tracking-widest flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined text-3xl">play_arrow</span>
                            START GAME
                        </button>
                    </form>
                @endif
            @endif

            @if($isPlaying)
                <form method="POST" action="{{ route('teacher.rooms.next', $room) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="neo-btn w-full py-4 rounded-xl bg-[#FFE600] text-black font-display font-black text-lg uppercase tracking-wider flex items-center justify-center gap-2">
                        <span>Soal Berikutnya</span>
                        <span class="material-symbols-outlined text-2xl">skip_next</span>
                    </button>
                </form>
                <form method="POST" action="{{ route('teacher.rooms.end', $room) }}" onsubmit="return confirm('Akhiri game sekarang?')">
                    @csrf
                    <button type="submit" class="neo-btn px-6 py-4 rounded-xl bg-[#FB7185] text-black font-display font-black text-sm uppercase">
                        ⏹ Stop
                    </button>
                </form>
            @endif
        </div>

        {{-- Daftar Soal (Preview) --}}
        @if($totalQuestions > 0 && ! $isFinished)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
            <div class="px-5 py-3 border-b-3 border-black bg-[#FEF9EF] dark:bg-slate-900">
                <h3 class="font-display font-black text-sm text-black dark:text-white uppercase">📋 Daftar Soal ({{ $totalQuestions }} Total)</h3>
            </div>
            <div class="divide-y-2 divide-black/10 dark:divide-slate-700">
                @foreach($questions as $i => $q)
                <div class="px-5 py-3 flex items-center gap-3 {{ $isPlaying && $i === $currentIndex ? 'bg-[#FFE600]/30 dark:bg-yellow-500/20' : '' }}">
                    <span class="w-7 h-7 rounded-lg {{ $isPlaying && $i < $currentIndex ? 'bg-[#4ADE80] text-black' : ($isPlaying && $i === $currentIndex ? 'bg-[#FFE600] text-black' : 'bg-white dark:bg-slate-900 text-black dark:text-white') }} border-2 border-black font-mono font-black text-xs flex items-center justify-center shrink-0">
                        {{ $isPlaying && $i < $currentIndex ? '✓' : ($i + 1) }}
                    </span>
                    <span class="text-sm font-body font-semibold text-black dark:text-white line-clamp-1 flex-1">{{ $q->question_text }}</span>
                    <span class="text-[10px] font-mono text-stone-500 dark:text-slate-400">{{ $q->time_limit_seconds }}s</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ===== RIGHT: Siswa & PIN ===== --}}
    <div class="flex flex-col gap-5">
        {{-- PIN Card --}}
        <div class="neo-box rounded-2xl bg-[#FFE600] p-6 flex flex-col items-center text-center gap-3">
            <span class="font-mono font-black text-xs text-black uppercase tracking-widest">PIN ROOM</span>
            <div class="font-mono font-black text-5xl text-black tracking-[0.3em] py-3 px-5 bg-white rounded-xl border-3 border-black shadow-[3px_3px_0px_#000]">
                {{ $room->room_code }}
            </div>
            <p class="text-xs font-bold text-black">Bagikan ke siswa!</p>
        </div>

        {{-- Daftar Siswa / Live Leaderboard --}}
        @if(! $isPlaying)
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden flex-1">
            <div class="px-5 py-3 border-b-3 border-black bg-[#FEF9EF] dark:bg-slate-900 flex items-center justify-between">
                <h3 class="font-display font-black text-sm text-black dark:text-white uppercase">Siswa</h3>
                <div class="flex items-center gap-2">
                    <span id="online-dot" class="w-2 h-2 rounded-full bg-[#4ADE80] border border-black animate-ping"></span>
                    <span id="student-count" class="neo-tag px-2 py-0.5 rounded bg-[#2563EB] text-white text-xs font-mono font-black">
                        {{ $room->students->count() }}
                    </span>
                </div>
            </div>
            <div id="student-list" class="divide-y-2 divide-black/10 dark:divide-slate-700 max-h-80 overflow-y-auto">
                @if($room->students->isEmpty())
                    <div id="empty-state" class="py-10 text-center">
                        <p class="text-3xl">👩‍🎓</p>
                        <p class="font-display font-black text-stone-400 dark:text-slate-500 mt-2 text-sm">Menunggu siswa bergabung...</p>
                    </div>
                @else
                    @foreach($room->students as $student)
                    <div class="student-item px-5 py-3 flex items-center gap-3" data-id="{{ $student->id }}">
                        <div class="w-10 h-10 rounded-full border-2 border-black overflow-hidden bg-[#FEF9EF] dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <x-avatar :code="$student->avatar" size="sm" />
                        </div>
                        <span class="text-sm font-display font-bold text-black dark:text-white">{{ $student->name }}</span>
                        @if($student->pivot?->team)
                            @php
                                $bg = 'bg-white text-black';
                                if (str_contains($student->pivot->team, 'Merah')) $bg = 'bg-[#FB7185] text-black';
                                elseif (str_contains($student->pivot->team, 'Biru')) $bg = 'bg-[#2563EB] text-white';
                                elseif (str_contains($student->pivot->team, 'Hijau')) $bg = 'bg-[#4ADE80] text-black';
                                elseif (str_contains($student->pivot->team, 'Kuning')) $bg = 'bg-[#FFE600] text-black';
                            @endphp
                            <span class="neo-tag text-[9px] px-1.5 py-0.5 rounded {{ $bg }} font-mono font-black">{{ $student->pivot->team }}</span>
                        @endif
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @else
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden flex-1 flex flex-col">
            <div class="px-5 py-3 border-b-3 border-black bg-[#A855F7] flex items-center justify-between">
                <h3 class="font-display font-black text-sm text-white uppercase flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">leaderboard</span> Live Leaderboard
                </h3>
            </div>
            <div id="live-leaderboard" class="divide-y-2 divide-black/10 dark:divide-slate-700 max-h-[400px] overflow-y-auto flex-1">
                <div class="py-10 text-center">
                    <span class="material-symbols-outlined text-4xl text-stone-300 dark:text-slate-600 animate-spin">refresh</span>
                    <p class="font-display font-black text-stone-400 dark:text-slate-500 mt-2 text-sm">Memuat skor...</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Back --}}
        <a href="{{ route('teacher.rooms.index') }}" class="neo-back-btn w-full text-center block">
            ← Kembali ke Daftar Room
        </a>
    </div>
</div>

{{-- Audio --}}
<div class="fixed bottom-6 right-6 z-50">
    <audio id="bgm-audio" src="{{ $isPlaying ? 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3' : 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3' }}" loop preload="auto"></audio>
    <button id="bgm-toggle" class="neo-btn px-4 py-3 rounded-full bg-white dark:bg-slate-800 text-black dark:text-white font-display font-black text-xs uppercase flex items-center gap-2 shadow-[4px_4px_0px_#000]">
        <span id="bgm-icon">🔇</span> Music: OFF
    </button>
</div>

@push('scripts')
<script>
    // ===== POLLING: Daftar Siswa Real-time =====
    const studentsUrl = "{{ route('teacher.rooms.students', $room) }}";
    const countEl = document.getElementById('student-count');
    const listEl = document.getElementById('student-list');
    let knownStudentIds = new Set([
        @foreach($room->students as $s) {{ $s->id }}, @endforeach
    ]);

    async function pollStudents() {
        try {
            const res = await fetch(studentsUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (countEl) countEl.textContent = data.count;

            data.students.forEach(student => {
                if (!knownStudentIds.has(student.id)) {
                    knownStudentIds.add(student.id);

                    // Hapus empty-state jika ada
                    const emptyState = document.getElementById('empty-state');
                    if (emptyState) emptyState.remove();

                    // Tambah item siswa baru dengan animasi
                    const div = document.createElement('div');
                    div.className = 'student-item px-5 py-3 flex items-center gap-3 bg-[#4ADE80]/20';
                    div.dataset.id = student.id;
                    
                    let teamHtml = '';
                    if (student.team) {
                        let bg = 'bg-white';
                        if (student.team.includes('Merah')) bg = 'bg-[#FB7185]';
                        else if (student.team.includes('Biru')) bg = 'bg-[#2563EB] text-white';
                        else if (student.team.includes('Hijau')) bg = 'bg-[#4ADE80]';
                        else if (student.team.includes('Kuning')) bg = 'bg-[#FFE600]';
                        teamHtml = `<span class="neo-tag text-[9px] px-1.5 py-0.5 rounded ${bg} text-black font-mono font-black">${student.team}</span>`;
                    }

                    div.innerHTML = `
                        <div class="w-10 h-10 rounded-full border-2 border-black overflow-hidden bg-[#FEF9EF] flex items-center justify-center shrink-0">
                            ${student.avatar_html}
                        </div>
                        <span class="text-sm font-display font-bold text-black">${student.name}</span>
                        ${teamHtml}
                        <span class="new-badge ml-auto neo-tag text-[9px] px-1.5 py-0.5 rounded bg-[#4ADE80] text-black font-mono font-black">BARU!</span>
                    `;
                    listEl.appendChild(div);
                    listEl.scrollTop = listEl.scrollHeight;

                    // Hapus highlight BARU setelah 4 detik
                    setTimeout(() => {
                        div.classList.remove('bg-[#4ADE80]/20');
                        const badge = div.querySelector('.new-badge');
                        if (badge) badge.remove();
                    }, 4000);
                }
            });
        } catch(e) { /* silent */ }
    }

    @if(! $isPlaying)
    setInterval(pollStudents, 3000);
    @endif

    @if($isPlaying)
    // ===== POLLING: Live Leaderboard =====
    const leaderboardUrl = "{{ route('teacher.rooms.leaderboardData', $room) }}";
    const leaderboardEl = document.getElementById('live-leaderboard');
    let previousScores = {};

    async function pollLeaderboard() {
        try {
            const res = await fetch(leaderboardUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            
            if (data.leaderboard.length === 0) {
                leaderboardEl.innerHTML = `
                    <div class="py-10 text-center">
                        <p class="text-3xl">👻</p>
                        <p class="font-display font-black text-stone-400 mt-2 text-sm">Belum ada skor...</p>
                    </div>
                `;
                return;
            }

            let html = '';
            data.leaderboard.forEach((player, index) => {
                const prevScore = previousScores[player.id] || 0;
                const scoreChanged = player.score > prevScore;
                previousScores[player.id] = player.score;

                const bgClass = scoreChanged ? 'bg-[#4ADE80]/30 transition duration-500' : '';
                
                let rankBadge = '';
                if (index === 0) rankBadge = '<span class="text-xl drop-shadow-md">🥇</span>';
                else if (index === 1) rankBadge = '<span class="text-xl drop-shadow-md">🥈</span>';
                else if (index === 2) rankBadge = '<span class="text-xl drop-shadow-md">🥉</span>';
                else rankBadge = `<span class="font-mono font-black text-stone-400 w-6 text-center text-sm">${index + 1}</span>`;

                html += `
                    <div class="px-5 py-3 flex items-center gap-3 ${bgClass}">
                        ${rankBadge}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-display font-black text-black dark:text-white truncate">${player.name}</h4>
                            <p class="text-[10px] font-mono text-stone-500 dark:text-slate-400">${player.correct} Benar</p>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-black text-lg text-black dark:text-white ${scoreChanged ? 'text-green-600 dark:text-green-400' : ''}">${player.score}</span>
                            <span class="text-[10px] text-stone-500 dark:text-slate-400 font-bold block -mt-1">pts</span>
                        </div>
                    </div>
                `;
            });
            leaderboardEl.innerHTML = html;
        } catch(e) { /* silent */ }
    }

    pollLeaderboard();
    setInterval(pollLeaderboard, 2500);
    @endif

    @if($isPlaying)
    // ===== Timer countdown untuk guru =====
    let timeLimit = {{ $session->current_time_limit ?? 30 }};
    let startedAt = {{ $session->question_started_at->timestamp }} * 1000;
    const timerEl = document.getElementById('teacher-timer');

    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startedAt) / 1000);
        const remaining = Math.max(0, timeLimit - elapsed);
        if (timerEl) timerEl.textContent = remaining + 's';
        if (remaining === 0) {
            if (timerEl) timerEl.classList.add('bg-[#FB7185]', 'text-black');
        }
    }
    updateTimer();
    setInterval(updateTimer, 1000);
    @endif

    // ===== BGM TOGGLE =====
    const bgmAudio = document.getElementById('bgm-audio');
    const bgmToggle = document.getElementById('bgm-toggle');
    const bgmIcon = document.getElementById('bgm-icon');
    let bgmPlaying = false;
    bgmAudio.volume = 0.4;

    bgmToggle.addEventListener('click', () => {
        if (bgmPlaying) {
            bgmAudio.pause();
            bgmIcon.textContent = '🔇';
            bgmToggle.innerHTML = '<span id="bgm-icon">🔇</span> Music: OFF';
            bgmPlaying = false;
        } else {
            bgmAudio.play().then(() => {
                bgmIcon.textContent = '🔊';
                bgmToggle.innerHTML = '<span id="bgm-icon">🔊</span> Music: ON';
                bgmPlaying = true;
            }).catch((err) => {
                alert("Gagal memutar audio: " + err.message);
                console.error(err);
            });
        }
    });
    // ======================
</script>
@endpush
@endsection
