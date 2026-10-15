@extends('layouts.neo')

@section('title', 'Pengaturan Sistem — GOXSGAME')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Sesuaikan mode tampilan gelap/terang dan efek audio')

@section('content')
<div class="flex flex-col gap-6 w-full max-w-4xl">

    {{-- Kartu Pengaturan Tampilan (Tema Gelap & Terang) --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-7 flex flex-col gap-5">
        <div class="flex items-center justify-between border-b-3 border-black pb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-[#FFE600] border-2 border-black flex items-center justify-center text-2xl shadow-[2px_2px_0px_#000]">
                    🌓
                </div>
                <div>
                    <h3 class="font-display font-black text-lg text-black dark:text-white uppercase">Mode Tema Tampilan</h3>
                    <p class="font-mono text-xs text-stone-500 dark:text-slate-400">Pilih kenyamanan visual antara tema cerah energik atau tema malam gelap.</p>
                </div>
            </div>
            <span class="neo-tag px-2.5 py-0.5 bg-[#4ADE80] text-black text-[10px] font-mono font-bold">THEME</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Mode Terang --}}
            <button type="button" onclick="setTheme('light')" id="btn-theme-light"
                class="theme-card-light p-5 rounded-2xl border-3 border-black text-left flex flex-col gap-3 transition hover:scale-[1.01] bg-white !text-black shadow-[3px_3px_0px_#000]">
                <div class="flex items-center justify-between">
                    <span class="text-3xl">☀️</span>
                    <span id="badge-theme-light" class="neo-tag px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#FFE600] text-black">AKTIF</span>
                </div>
                <div>
                    <h4 class="font-display font-black text-base uppercase !text-black">Mode Terang (Light Mode)</h4>
                    <p class="font-body text-xs !text-stone-800 mt-0.5">Latar biru cerah dengan kartu putih neo-brutalism ceria standar game.</p>
                </div>
            </button>

            {{-- Mode Gelap --}}
            <button type="button" onclick="setTheme('dark')" id="btn-theme-dark"
                class="p-5 rounded-2xl border-3 border-black text-left flex flex-col gap-3 transition hover:scale-[1.01] bg-[#0F172A] !text-white shadow-[3px_3px_0px_#000]">
                <div class="flex items-center justify-between">
                    <span class="text-3xl">🌙</span>
                    <span id="badge-theme-dark" class="neo-tag px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#FFE600] text-black hidden">AKTIF</span>
                </div>
                <div>
                    <h4 class="font-display font-black text-base uppercase !text-white">Mode Gelap (Dark Mode)</h4>
                    <p class="font-body text-xs !text-slate-300 mt-0.5">Latar slate gelap yang ramah mata saat digunakan di ruangan redup.</p>
                </div>
            </button>
        </div>
    </div>

    {{-- Kartu Pengaturan Suara (Sound & Audio FX) --}}
    <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-7 flex flex-col gap-5">
        <div class="flex items-center justify-between border-b-3 border-black pb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-[#A855F7] text-white border-2 border-black flex items-center justify-center text-2xl shadow-[2px_2px_0px_#000]">
                    🔊
                </div>
                <div>
                    <h3 class="font-display font-black text-lg text-black dark:text-white uppercase">Pengaturan Suara & Audio</h3>
                    <p class="font-mono text-xs text-stone-500 dark:text-slate-400">Atur musik latar (BGM) dan efek audio permainan kuis.</p>
                </div>
            </div>
            <span class="neo-tag px-2.5 py-0.5 bg-[#A855F7] text-white text-[10px] font-mono font-bold">AUDIO</span>
        </div>

        <div class="flex flex-col gap-4">
            {{-- Switch BGM --}}
            <div class="p-4 rounded-xl border-2 border-black bg-stone-50 dark:bg-slate-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎵</span>
                    <div>
                        <span class="font-display font-black text-sm text-black dark:text-white uppercase block">Musik Latar Game (BGM)</span>
                        <span class="text-xs font-mono text-stone-500 dark:text-slate-400">Mainkan musik instrumen otomatis saat sesi game berlangsung.</span>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="setting-bgm" class="sr-only peer" onchange="toggleSoundSetting('bgm')">
                    <div class="w-11 h-6 bg-stone-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-black after:border-2 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4ADE80] border-2 border-black"></div>
                </label>
            </div>

            {{-- Switch Sound FX --}}
            <div class="p-4 rounded-xl border-2 border-black bg-stone-50 dark:bg-slate-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🔔</span>
                    <div>
                        <span class="font-display font-black text-sm text-black dark:text-white uppercase block">Efek Suara Jawaban (SFX)</span>
                        <span class="text-xs font-mono text-stone-500 dark:text-slate-400">Bunyi denting saat jawaban benar / salah dan powerup digunakan.</span>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="setting-sfx" checked class="sr-only peer" onchange="toggleSoundSetting('sfx')">
                    <div class="w-11 h-6 bg-stone-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-black after:border-2 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4ADE80] border-2 border-black"></div>
                </label>
            </div>

            {{-- Slider Volume --}}
            <div class="p-4 rounded-xl border-2 border-black bg-stone-50 dark:bg-slate-900 flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="font-display font-black text-xs uppercase text-black dark:text-white">Tingkat Volume Efek:</span>
                    <span id="volume-val" class="font-mono font-bold text-xs text-black dark:text-white">80%</span>
                </div>
                <input type="range" id="setting-volume" min="0" max="100" value="80" 
                    oninput="updateVolume(this.value)"
                    class="w-full accent-[#4ADE80] cursor-pointer">
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function setTheme(mode) {
        if (mode === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('goxs_theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('goxs_theme', 'light');
        }
        updateThemeUI();
        if (typeof updateThemeIcon === 'function') updateThemeIcon();
    }

    function updateThemeUI() {
        const isDark = document.documentElement.classList.contains('dark');
        const badgeLight = document.getElementById('badge-theme-light');
        const badgeDark = document.getElementById('badge-theme-dark');
        
        if (badgeLight && badgeDark) {
            if (isDark) {
                badgeLight.classList.add('hidden');
                badgeDark.classList.remove('hidden');
            } else {
                badgeLight.classList.remove('hidden');
                badgeDark.classList.add('hidden');
            }
        }
    }

    function toggleSoundSetting(key) {
        const el = document.getElementById('setting-' + key);
        if (el) {
            localStorage.setItem('goxs_sound_' + key, el.checked ? 'on' : 'off');
        }
    }

    function updateVolume(val) {
        document.getElementById('volume-val').textContent = val + '%';
        localStorage.setItem('goxs_sound_volume', val);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateThemeUI();
        // Load sound preferences
        const bgmPref = localStorage.getItem('goxs_sound_bgm');
        if (bgmPref) document.getElementById('setting-bgm').checked = (bgmPref === 'on');
        const sfxPref = localStorage.getItem('goxs_sound_sfx');
        if (sfxPref) document.getElementById('setting-sfx').checked = (sfxPref !== 'off');
        const volPref = localStorage.getItem('goxs_sound_volume');
        if (volPref) {
            document.getElementById('setting-volume').value = volPref;
            document.getElementById('volume-val').textContent = volPref + '%';
        }
    });
</script>
@endpush
@endsection
