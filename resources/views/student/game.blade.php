@extends('layouts.neo')

@section('title', 'Game Live!')
@section('page-title', $room->game->title)
@section('page-subtitle', 'Room: ' . $room->room_code)

@push('styles')
<style>


    /* Container Box Animasi */
    .game-box {
        animation: floatIdle 4s ease-in-out infinite;
        border: 4px solid #000;
        box-shadow: 8px 8px 0px #000;
    }
    @keyframes floatIdle { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }

    /* Transisi Halus & Animasi */
    #timer-bar { transition: width 1s linear, background-color 0.5s ease; }
    .option-btn { transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1); border: 3px solid #000; }
    
    /* Efek 3D Button Neo-Brutalism */
    .option-btn:hover:not(:disabled) { transform: translate(-4px, -4px) rotate(-1deg); box-shadow: 6px 6px 0px #000 !important; filter: brightness(1.1); }
    .option-btn:active:not(:disabled) { transform: translate(0px, 0px) rotate(0deg); box-shadow: 0px 0px 0px #000 !important; }
    
    .option-btn.correct { background-color: #4ADE80 !important; color: #000 !important; animation: correctPulse 0.5s ease infinite alternate; }
    @keyframes correctPulse { from { transform: scale(1); box-shadow: 0px 0px 0px #000; } to { transform: scale(1.03); box-shadow: 4px 4px 0px #000; } }
    
    .option-btn.wrong { background-color: #FB7185 !important; color: #000 !important; }
    .option-btn.selected { transform: translate(0px, 0px); box-shadow: 0px 0px 0px #000 !important; border-width: 4px; }
    .option-btn:disabled { cursor: not-allowed; opacity: 0.6; filter: grayscale(50%); }
    
    @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-10px) rotate(-2deg)} 75%{transform:translateX(10px) rotate(2deg)} }
    .shake { animation: shake 0.4s ease; }
    
    @keyframes pop { 0%{transform:scale(1)} 50%{transform:scale(1.3) rotate(5deg)} 100%{transform:scale(1)} }
    .pop { animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    
    /* Animasi Masuk Soal Baru */
    @keyframes slideUpFade { 
        0% { opacity: 0; transform: translateY(40px) scale(0.9); } 
        60% { transform: translateY(-10px) scale(1.02); }
        100% { opacity: 1; transform: translateY(0) scale(1); } 
    }
    .question-enter { animation: slideUpFade 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    
    /* Timer Panik */
    @keyframes panicPulse { 0% { transform: scale(1); color: #FB7185; } 50% { transform: scale(1.2); color: #DC2626; } 100% { transform: scale(1); color: #FB7185; } }
    .timer-panic { animation: panicPulse 0.5s infinite; }
</style>
<!-- Confetti Library -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
@endpush

@section('content')
<div class="w-full max-w-4xl mx-auto flex flex-col gap-3 sm:gap-5" id="game-wrapper">

    {{-- Header: Progress + Timer --}}
    <div class="game-box neo-box rounded-2xl bg-white dark:bg-slate-800 p-3 sm:p-4 flex flex-col gap-2.5 sm:gap-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1.5 sm:gap-2">
                <span id="q-counter" class="neo-tag px-2.5 sm:px-3 py-1 rounded-lg bg-[#2563EB] text-white font-mono text-[11px] sm:text-xs font-black">
                    Soal 1 / ?
                </span>
                <span id="q-points" class="neo-tag px-2.5 sm:px-3 py-1 rounded-lg bg-[#A855F7] text-white font-mono text-[11px] sm:text-xs font-black">
                    +100 pts
                </span>
            </div>
            <div id="timer-display" class="font-mono font-black text-2xl sm:text-3xl text-black dark:text-white">--</div>
        </div>
        {{-- Timer bar --}}
        <div class="w-full h-3.5 sm:h-4 bg-[#FEF9EF] dark:bg-slate-900 rounded-full border-2 border-black overflow-hidden">
            <div id="timer-bar" class="h-full bg-[#FFE600] rounded-full border-r-2 border-black" style="width:100%"></div>
        </div>
        {{-- Team Badge (Hidden if not team) --}}
        <div id="team-badge-container" class="hidden flex justify-center mt-1">
            <span id="team-badge" class="neo-tag px-3 sm:px-4 py-1 rounded-xl text-black font-display font-black text-xs sm:text-sm uppercase"></span>
        </div>
        
        {{-- Survival Hearts (Hidden if classic) --}}
        <div id="survival-lives" class="hidden flex items-center justify-center gap-2 mt-1">
            <span class="text-xs sm:text-sm font-display font-black text-stone-500 dark:text-slate-400 uppercase">Nyawa:</span>
            <div id="hearts-container" class="flex gap-1 text-xl sm:text-2xl"></div>
        </div>

        {{-- Power-up 50:50 --}}
        <div id="powerup-container" class="hidden flex justify-center mt-1">
            <button id="btn-powerup" class="neo-btn px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-[#F59E0B] text-black font-display font-black text-xs sm:text-sm flex items-center gap-2" onclick="usePowerup()">
                💡 Gunakan Bantuan 50:50
            </button>
        </div>
    </div>

    {{-- Media Display Container (Gambar / Audio Tebak Lagu) --}}
    <div id="media-display" class="hidden neo-box rounded-2xl bg-white dark:bg-slate-800 p-3 sm:p-4 flex flex-col items-center justify-center overflow-hidden">
        {{-- Diisi secara dinamis oleh JavaScript --}}
    </div>

    {{-- Pertanyaan --}}
    <div class="game-box neo-box rounded-2xl bg-white dark:bg-slate-800 p-4 sm:p-6 relative overflow-hidden group min-h-[110px] sm:min-h-[140px] flex items-center justify-center">
        <!-- Hiasan Background di dalam box pertanyaan -->
        <div class="absolute -top-10 -right-10 text-8xl sm:text-9xl opacity-5 group-hover:rotate-12 transition-transform duration-500 pointer-events-none">❓</div>
        <p id="question-text" class="font-display font-black text-lg sm:text-xl text-black dark:text-white leading-snug text-center relative z-10 break-words w-full">
            Memuat soal...
        </p>
    </div>

    {{-- Pilihan Jawaban (1 kolom di HP vertikal, 2 kolom di tablet/desktop) --}}
    <div id="options-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
        {{-- Diisi JS --}}
    </div>

    {{-- Feedback (muncul setelah jawab) --}}
    <div id="feedback" class="hidden neo-box rounded-2xl p-4 sm:p-5 text-center flex flex-col items-center gap-2">
        <div id="feedback-icon" class="text-4xl sm:text-5xl"></div>
        <p id="feedback-text" class="font-display font-black text-lg sm:text-xl text-black"></p>
        <p id="feedback-score" class="font-mono font-black text-xs sm:text-sm text-black"></p>
        <p class="text-xs font-mono text-black/80 font-bold mt-1">Menunggu guru untuk soal berikutnya...</p>
    </div>

    {{-- Skor Kumulatif & Audio (Responsive flex wrap) --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-4">
        <div class="flex items-center justify-center gap-3 py-2 sm:py-2.5 px-4 neo-box rounded-xl bg-[#FEF9EF] dark:bg-slate-800 flex-1">
            <span class="font-mono font-black text-xs sm:text-sm text-stone-500 dark:text-slate-400">SKOR KAMU:</span>
            <span id="total-score" class="font-mono font-black text-xl sm:text-2xl text-black dark:text-white">0</span>
            <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-black">pts</span>
        </div>
        
        <div class="flex items-center justify-between sm:justify-end gap-2 px-1">
            <audio id="bgm-audio" src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3" loop preload="auto"></audio>
            <button id="bgm-toggle" class="neo-btn px-3 sm:px-4 py-2 rounded-xl bg-white dark:bg-slate-800 text-black dark:text-white font-display font-black text-xs uppercase flex items-center justify-center gap-1.5 w-full sm:w-auto">
                <span id="bgm-icon">🔇</span> Music: OFF
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const COLORS = ['bg-[#EF4444] text-white', 'bg-[#3B82F6] text-white', 'bg-[#FACC15] text-black', 'bg-[#10B981] text-white'];
const SHAPES = ['change_history', 'check_box_outline_blank', 'circle', 'star'];
const LETTERS = ['A', 'B', 'C', 'D'];

const stateUrl = "{{ route('student.room.state', $room, false) }}";
const answerUrl = "{{ route('student.room.answer', $room, false) }}";
const powerupUrl = "{{ route('student.room.powerup', $room, false) }}";
const resultUrl = "{{ route('student.result', $room, false) }}";
const csrfToken = "{{ csrf_token() }}";

let currentQuestionId = null;
let timerInterval = null;
let totalScore = 0;
let pollInterval = null;
let timeLimit = 30;
let startedAt = null;
let answered = false;

async function fetchState() {
    try {
        const res = await fetch(stateUrl + (stateUrl.includes('?') ? '&' : '?') + "t=" + Date.now(), {
            cache: 'no-store',
            credentials: 'same-origin',
            headers: { 
                'Accept': 'application/json', 
                'X-Requested-With': 'XMLHttpRequest' 
            }
        });

        if (res.status === 401 || res.status === 403) {
            console.warn('Student session expired or unauthorized.');
            window.location.href = "{{ route('student.join', [], false) }}";
            return;
        }

        if (!res.ok) {
            console.warn('State fetch returned non-ok:', res.status);
            return;
        }

        const data = await res.json();
        handleState(data);
    } catch(e) { 
        console.error('fetchState error:', e);
    }
}

let isEliminated = false;
function handleState(data) {
    if (data.status === 'finished') {
        clearInterval(pollInterval);
        window.location.href = resultUrl;
        return;
    }

    if (data.status === 'eliminated') {
        if (!isEliminated) {
            isEliminated = true;
            playSound('eliminated');
        }
        clearInterval(pollInterval);
        document.getElementById('question-text').textContent = '☠️ ELIMINATED ☠️';
        document.getElementById('options-grid').innerHTML = '';
        document.getElementById('feedback').classList.add('hidden');
        document.getElementById('timer-display').textContent = '--';
        document.getElementById('timer-bar').style.width = '0%';
        if (document.getElementById('survival-lives')) {
            document.getElementById('survival-lives').classList.remove('hidden');
            document.getElementById('hearts-container').innerHTML = '💔💔💔 (0 Nyawa)';
        }
        return;
    }

    if (data.status !== 'playing') return;

    // Render Hearts for Survival mode
    if (data.game_mode === 'survival') {
        document.getElementById('survival-lives').classList.remove('hidden');
        const container = document.getElementById('hearts-container');
        container.innerHTML = '';
        for (let i = 0; i < 3; i++) {
            container.innerHTML += i < data.lives ? '❤️' : '💔';
        }
    } else {
        document.getElementById('survival-lives').classList.add('hidden');
    }

    // Render Team Badge for Team mode
    if (data.game_mode === 'team' && data.team) {
        document.getElementById('team-badge-container').classList.remove('hidden');
        const badge = document.getElementById('team-badge');
        badge.textContent = data.team;
        // Set color based on team name
        badge.className = 'neo-tag px-4 py-1 rounded-xl text-black font-display font-black text-sm uppercase'; // reset
        if (data.team.includes('Merah')) badge.classList.add('bg-[#FB7185]');
        else if (data.team.includes('Biru')) badge.classList.add('bg-[#2563EB]', 'text-white');
        else if (data.team.includes('Hijau')) badge.classList.add('bg-[#4ADE80]');
        else if (data.team.includes('Kuning')) badge.classList.add('bg-[#FFE600]');
        else badge.classList.add('bg-white');
    } else {
        document.getElementById('team-badge-container').classList.add('hidden');
    }

    // Power-up logic
    if (data.has_used_5050 || answered) {
        document.getElementById('powerup-container').classList.add('hidden');
    } else {
        document.getElementById('powerup-container').classList.remove('hidden');
    }

    // Soal baru muncul
    if (data.question.id !== currentQuestionId) {
        currentQuestionId = data.question.id;
        answered = false;
        timeLimit = data.time_limit;
        startedAt = Date.now() - ((data.time_limit - data.time_remaining) * 1000);

        renderQuestion(data);
        startTimer(data.time_remaining, data.time_limit);

        // Update counter
        document.getElementById('q-counter').textContent = `Soal ${data.question_index + 1} / ${data.total_questions}`;
        document.getElementById('q-points').textContent = `+${data.question.options.length > 0 ? '' : ''}100 pts`;

        // Kalau sudah pernah jawab (misalnya refresh)
        if (data.my_answer) {
            answered = true;
            showFeedback(data.my_answer.is_correct, data.my_answer.score_earned, data.question.options.find(o => o.id === data.my_answer.option_id));
            disableOptions(data.my_answer.option_id, null);
        }
    } else if (data.my_answer && !answered) {
        // State sudah punya jawaban tapi UI belum update
        answered = true;
        totalScore += data.my_answer.score_earned;
        document.getElementById('total-score').textContent = totalScore;
    }
}

function renderQuestion(data) {
    const questionBox = document.getElementById('question-text').parentElement;
    const mediaDisplay = document.getElementById('media-display');
    
    // Trigger ulang animasi masuk soal
    questionBox.classList.remove('question-enter');
    void questionBox.offsetWidth; // trigger reflow
    questionBox.classList.add('question-enter');

    document.getElementById('question-text').textContent = data.question.text;
    document.getElementById('feedback').classList.add('hidden');

    // Handle Media (Gambar / Audio)
    if (data.question.media_url) {
        mediaDisplay.classList.remove('hidden');
        if (data.question.type === 'audio') {
            mediaDisplay.innerHTML = `
                <div class="flex flex-col items-center gap-3 w-full py-2">
                    <div class="flex items-center gap-2 text-stone-800 dark:text-slate-200 font-display font-black text-sm uppercase">
                        <span class="text-2xl animate-pulse">🎵</span> Dengarkan Potongan Lagu:
                    </div>
                    <audio controls autoplay class="w-full max-w-md rounded-xl border-2 border-black" src="${data.question.media_url}"></audio>
                </div>
            `;
        } else {
            mediaDisplay.innerHTML = `
                <div class="max-h-64 sm:max-h-80 overflow-hidden rounded-xl border-3 border-black w-full flex items-center justify-center bg-stone-50 dark:bg-slate-900">
                    <img src="${data.question.media_url}" alt="Gambar Soal" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='https://placehold.co/600x400/2563EB/FFFFFF?text=Gambar+Soal';" class="max-h-64 sm:max-h-80 w-auto object-contain hover:scale-105 transition duration-300">
                </div>
            `;
        }
    } else {
        mediaDisplay.classList.add('hidden');
        mediaDisplay.innerHTML = '';
    }

    const grid = document.getElementById('options-grid');
    grid.innerHTML = '';
    
    // Trigger ulang animasi masuk opsi
    grid.classList.remove('question-enter');
    void grid.offsetWidth; // trigger reflow
    grid.classList.add('question-enter');

    // Check if True / False
    const isTrueFalse = data.question.type === 'true_false' || (data.question.options.length === 2 && data.question.options.some(o => o.text.toUpperCase() === 'BENAR'));

    data.question.options.forEach((opt, i) => {
        const btn = document.createElement('button');
        btn.id = `opt-${opt.id}`;
        
        let colorClass = COLORS[i % 4];
        let shapeIcon = SHAPES[i % 4];

        if (isTrueFalse) {
            const isBenar = opt.text.toUpperCase() === 'BENAR';
            colorClass = isBenar ? 'bg-[#4ADE80] text-black' : 'bg-[#FB7185] text-black';
            shapeIcon = isBenar ? 'check_circle' : 'cancel';
        }

        // Tambahkan efek hover 3D Neo Brutalism yang responsif
        btn.className = `option-btn neo-btn p-3 sm:p-4 rounded-xl sm:rounded-2xl ${colorClass} font-display font-black text-sm sm:text-base md:text-lg text-left flex items-center gap-2.5 sm:gap-4 active:scale-95`;
        btn.innerHTML = `
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-black/20 border-2 sm:border-3 border-black flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-xl sm:text-2xl md:text-3xl font-black text-white mix-blend-overlay">${shapeIcon}</span>
            </div>
            <span class="flex-1 break-words">${opt.text}</span>
        `;
        btn.addEventListener('click', () => submitAnswer(opt.id, data.question.id, data));
        grid.appendChild(btn);
    });
}

function startTimer(remaining, limit) {
    clearInterval(timerInterval);
    const timerDisplay = document.getElementById('timer-display');
    const timerBar = document.getElementById('timer-bar');

    function tick() {
        const elapsed = Math.floor((Date.now() - startedAt) / 1000);
        const left = Math.max(0, limit - elapsed);
        timerDisplay.textContent = left + 's';
        const pct = (left / limit) * 100;
        if (left <= 5) {
            timerBar.style.backgroundColor = '#FB7185';
            timerDisplay.classList.add('timer-panic');
        } else if (left <= 10) {
            timerBar.style.backgroundColor = '#FB923C';
            timerDisplay.classList.remove('timer-panic');
        } else {
            timerBar.style.backgroundColor = '#FFE600';
            timerDisplay.classList.remove('timer-panic');
        }
        
        if (left === 0) {
            clearInterval(timerInterval);
            if (!answered) {
                // Waktu habis, disable semua
                disableOptions(null, null);
                showFeedback(false, 0, null, true);
            }
        }
    }
    tick();
    timerInterval = setInterval(tick, 500);
}

async function submitAnswer(optionId, questionId, data) {
    if (answered) return;
    answered = true;

    const btn = document.getElementById(`opt-${optionId}`);
    if (btn) btn.classList.add('selected');

    try {
        const res = await fetch(answerUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ question_id: questionId, option_id: optionId })
        });
        const result = await res.json();

        if (result.error) { answered = false; return; }

        // Tandai benar/salah di UI
        disableOptions(optionId, result.correct_option_id);
        showFeedback(result.is_correct, result.score_earned);

        totalScore += result.score_earned;
        const scoreEl = document.getElementById('total-score');
        scoreEl.textContent = totalScore;
        scoreEl.classList.add('pop');
        setTimeout(() => scoreEl.classList.remove('pop'), 400);
    } catch(e) {
        answered = false;
    }
}

async function usePowerup() {
    if (answered) return;
    
    // Optimistic UI hide
    document.getElementById('powerup-container').classList.add('hidden');
    
    try {
        const res = await fetch(powerupUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await res.json();
        
        if (result.removed_options) {
            playSound('correct'); // Just a chime to indicate it worked
            result.removed_options.forEach(optId => {
                const btn = document.getElementById(`opt-${optId}`);
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '---'; // Visual feedback that it's removed
                    btn.classList.add('opacity-30', 'grayscale');
                }
            });
        }
    } catch(e) {
        // Rollback on fail
        document.getElementById('powerup-container').classList.remove('hidden');
    }
}

function disableOptions(selectedId, correctId) {
    document.querySelectorAll('.option-btn').forEach(btn => {
        btn.disabled = true;
        const id = parseInt(btn.id.replace('opt-', ''));
        if (correctId && id === correctId) btn.classList.add('correct');
        else if (selectedId && id === selectedId && id !== correctId) btn.classList.add('wrong', 'shake');
    });
}

function showFeedback(isCorrect, score, option = null, timesUp = false) {
    const fb = document.getElementById('feedback');
    fb.classList.remove('hidden');
    // Animasi pop in untuk feedback
    fb.classList.remove('pop');
    void fb.offsetWidth;
    fb.classList.add('pop');
    
    fb.style.backgroundColor = isCorrect ? '#4ADE80' : '#FB7185';

    if (timesUp) {
        playSound('wrong');
        document.getElementById('feedback-icon').textContent = '⏰';
        document.getElementById('feedback-text').textContent = 'Waktu Habis!';
        document.getElementById('feedback-score').textContent = '+0 pts';
    } else {
        playSound(isCorrect ? 'correct' : 'wrong');
        document.getElementById('feedback-icon').textContent = isCorrect ? '🎉' : '❌';
        document.getElementById('feedback-text').textContent = isCorrect ? 'BENAR JAWABANMU!' : 'Sayang Sekali Salah!';
        document.getElementById('feedback-score').textContent = isCorrect ? `+${score} pts (Kecepatan & Ketepatan)` : '+0 pts';
        
        // Letuskan Confetti jika jawaban benar!
        if (isCorrect && window.confetti) {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#FFE600', '#4ADE80', '#A855F7', '#2563EB']
            });
        }
    }
}

// ===== AUDIO SYNTHESIZER =====
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
function playSound(type) {
    if (audioCtx.state === 'suspended') audioCtx.resume();
    const osc = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    osc.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    if (type === 'correct') {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
        osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.1); // E5
        osc.frequency.setValueAtTime(783.99, audioCtx.currentTime + 0.2); // G5
        gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.5);
    } else if (type === 'wrong') {
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(150, audioCtx.currentTime);
        osc.frequency.linearRampToValueAtTime(100, audioCtx.currentTime + 0.3);
        gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.3);
    } else if (type === 'eliminated') {
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(200, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(50, audioCtx.currentTime + 1);
        gainNode.gain.setValueAtTime(0.8, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 1.5);
        osc.start();
        osc.stop(audioCtx.currentTime + 1.5);
    }
}
// =============================

// ===== BGM TOGGLE =====
const bgmAudio = document.getElementById('bgm-audio');
const bgmToggle = document.getElementById('bgm-toggle');
const bgmIcon = document.getElementById('bgm-icon');
let bgmPlaying = false;
bgmAudio.volume = 0.3;

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

// Render state awal langsung dari server tanpa jeda (0 millisecond loading)
const initialState = @json($initialState ?? null);
if (initialState && initialState.status === 'playing') {
    handleState(initialState);
} else {
    fetchState();
}

// Mulai sinkronisasi polling berkala
pollInterval = setInterval(fetchState, 1500);
</script>
@endpush
@endsection
