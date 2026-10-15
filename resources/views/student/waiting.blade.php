@extends('layouts.neo')

@section('title', 'Menunggu Guru...')
@section('page-title', 'Lobby Room')
@section('page-subtitle', $room->game->title . ' • PIN: ' . $room->room_code)

@push('styles')
<style>
    /* Sembunyikan header default layout neo jika ada (hanya untuk halaman ini) */
    header { display: none !important; }
    main { padding: 0 !important; }
</style>
@endpush

@section('content')
{{-- Floating Theme Toggle Button --}}
<div class="fixed top-3 right-3 sm:top-5 sm:right-5 z-50">
    <button type="button" onclick="toggleThemeMode()" 
            class="p-2.5 sm:p-3 rounded-2xl border-3 border-black bg-white dark:bg-slate-800 hover:bg-[#FFE600] dark:hover:bg-[#FFE600] text-black dark:text-white dark:hover:text-black shadow-[3px_3px_0px_#000] flex items-center justify-center transition-all hover:scale-105 active:translate-y-1 active:shadow-none cursor-pointer" 
            title="Ganti Mode Gelap / Terang">
        <span class="material-symbols-outlined text-xl sm:text-2xl theme-toggle-icon" id="student-theme-icon">dark_mode</span>
    </button>
</div>

<div class="max-w-md mx-auto flex flex-col items-center gap-5 sm:gap-6 pt-4 sm:pt-10 text-center px-3 sm:px-0">
    {{-- Animasi menunggu --}}
    <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-2xl sm:rounded-3xl bg-[#FFE600] border-4 border-black shadow-[4px_4px_0px_#000] sm:shadow-[6px_6px_0px_#000] flex items-center justify-center animate-bounce">
        <span class="text-4xl sm:text-6xl">🎮</span>
    </div>

    <div>
        <h1 class="font-display font-black text-2xl sm:text-3xl text-white uppercase" style="text-shadow: 2px 2px 0px #000;">Menunggu Guru...</h1>
        <p class="font-body text-white/90 font-bold mt-1 sm:mt-2 text-sm sm:text-lg">Guru sedang mempersiapkan game. Bersiaplah!</p>
    </div>

    {{-- Info Room --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 w-full flex flex-col gap-3 text-left">
        <div class="flex items-center justify-between">
            <span class="font-display font-black text-sm text-black dark:text-white">🎮 {{ $room->game->title }}</span>
            <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-black">READY</span>
        </div>
        <div class="flex items-center gap-2 text-xs font-mono font-bold text-stone-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-sm text-[#2563EB] dark:text-blue-400">person</span>
            Guru: {{ $room->teacher->name }}
        </div>
    </div>

    {{-- Efek dots loading --}}
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-[#2563EB] border-2 border-black animate-bounce" style="animation-delay:0s"></span>
        <span class="w-3 h-3 rounded-full bg-[#FFE600] border-2 border-black animate-bounce" style="animation-delay:0.15s"></span>
        <span class="w-3 h-3 rounded-full bg-[#FB7185] border-2 border-black animate-bounce" style="animation-delay:0.3s"></span>
    </div>
    <p class="text-xs font-mono font-bold text-white/80">Halaman akan otomatis berpindah saat game dimulai</p>
    
    {{-- Audio --}}
    <audio id="bgm-audio" src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" loop preload="auto"></audio>
    <button id="bgm-toggle" class="mt-4 neo-btn px-4 py-2 rounded-xl bg-white dark:bg-slate-800 text-black dark:text-white font-display font-black text-xs uppercase flex items-center gap-2">
        <span id="bgm-icon">🔇</span> Music: OFF
    </button>
</div>

@push('scripts')
<script>
    // Poll setiap 1.2 detik — kalau state berubah, redirect ke halaman game
    const pollUrl = "{{ route('student.room.state', $room) }}";
    const gameUrl = "{{ route('student.room', $room) }}";
    let interval = null;
    let isTransitioning = false;

    async function pollState() {
        if (isTransitioning) return;
        try {
            // Tambahkan cache-buster (?t=...) dan cache: 'no-store' agar browser selalu mendapatkan status terbaru
            const res = await fetch(pollUrl + (pollUrl.includes('?') ? '&' : '?') + "t=" + Date.now(), {
                cache: 'no-store',
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                credentials: 'same-origin'
            });

            if (!res.ok) return;

            const data = await res.json();
            if (data && (data.status === 'playing' || data.status === 'finished')) {
                isTransitioning = true;
                stopPolling();

                // Beri indikator visual seketika pada layar siswa
                const titleEl = document.querySelector('h1');
                if (titleEl) {
                    titleEl.textContent = '🚀 Game Dimulai!';
                }
                const subEl = document.querySelector('p.font-body');
                if (subEl) {
                    subEl.textContent = 'Menyiapkan arena kuis...';
                }

                // Paksa browser berpindah halaman ke tampilan game dengan parameter unik
                const redirectTarget = gameUrl + (gameUrl.includes('?') ? '&' : '?') + 'play=' + Date.now();
                window.location.replace(redirectTarget);
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }

    function startPolling() {
        if (!interval && !isTransitioning) {
            pollState();
            interval = setInterval(pollState, 1200);
        }
    }

    function stopPolling() {
        if (interval) {
            clearInterval(interval);
            interval = null;
        }
    }

    startPolling();

    // Stop polling jika halaman di-hide (hemat resource), dan mulai lagi saat terlihat
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            startPolling();
        }
    });

    // BGM Toggle
    const bgmAudio = document.getElementById('bgm-audio');
    const bgmToggle = document.getElementById('bgm-toggle');
    const bgmIcon = document.getElementById('bgm-icon');
    let isPlaying = false;

    // Try auto-play
    bgmAudio.volume = 0.4;
    
    bgmToggle.addEventListener('click', () => {
        if (isPlaying) {
            bgmAudio.pause();
            bgmIcon.textContent = '🔇';
            bgmToggle.innerHTML = '<span id="bgm-icon">🔇</span> Music: OFF';
            isPlaying = false;
        } else {
            bgmAudio.play().then(() => {
                bgmIcon.textContent = '🔊';
                bgmToggle.innerHTML = '<span id="bgm-icon">🔊</span> Music: ON';
                isPlaying = true;
            }).catch((err) => {
                alert("Gagal memutar audio: " + err.message);
                console.error(err);
            });
        }
    });
</script>
@endpush
@endsection
