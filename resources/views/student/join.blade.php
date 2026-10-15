@extends('layouts.neo')

@section('title', 'Main GOXSGAME!')
@section('page-title', 'Join Game')

@push('styles')
<style>

    /* Sembunyikan header default layout neo jika ada (hanya untuk halaman ini) */
    header {
        display: none !important;
    }
    
    main {
        padding: 0 !important;
    }


    
    /* Animasi masuk pop-in yang "Bouncy" */
    @keyframes popIn {
        0% { transform: scale(0.5); opacity: 0; }
        60% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .game-box {
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    
    /* Tombol Super Bulat & Bouncy */
    .blooket-btn {
        border-radius: 999px !important;
        border: 4px solid #000000 !important;
        box-shadow: 0px 6px 0px #000000 !important;
        transition: all 0.1s ease;
    }
    .blooket-btn:active {
        transform: translateY(6px);
        box-shadow: 0px 0px 0px #000000 !important;
    }
    
    /* Input Super Bulat */
    .blooket-input {
        border-radius: 999px !important;
        border: 4px solid #000000 !important;
        box-shadow: 0px 6px 0px #000000 !important;
        text-align: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 900 !important;
        font-size: 1.25rem !important;
        transition: all 0.1s ease;
    }
    .blooket-input:focus {
        outline: none;
        box-shadow: 0px 3px 0px #000000 !important;
        transform: translateY(3px);
    }
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

<div class="relative z-10 w-full max-w-sm mx-auto my-4 sm:my-8 md:my-16 flex flex-col items-center gap-4 sm:gap-6 game-box px-3 sm:px-4">
    
    {{-- Game Title Logo --}}
    <div class="text-center animate-bounce" style="animation-duration: 2s;">
        <h1 class="font-display font-black text-white text-[2.5rem] sm:text-[3rem] tracking-widest uppercase leading-none" 
            style="text-shadow: 3px 3px 0px #000, -2px -2px 0px #000, 2px -2px 0px #000, -2px 2px 0px #000, 0px 6px 0px #000;">
            GOXS<br>GAME
        </h1>
    </div>

    {{-- The Game Card --}}
    <div class="w-full bg-white dark:bg-slate-800 rounded-[32px] sm:rounded-[40px] p-5 sm:p-7 border-4 border-black shadow-[0px_8px_0px_rgba(0,0,0,1)] sm:shadow-[0px_10px_0px_rgba(0,0,0,1)] flex flex-col gap-5 sm:gap-6 relative overflow-hidden">
        
        <form method="POST" action="{{ route('student.enter') }}" class="w-full flex flex-col gap-5">
            @csrf
            
            {{-- PIN Input --}}
            <div class="flex flex-col gap-1 relative">
                <input
                    id="pin"
                    type="text"
                    name="pin"
                    maxlength="6"
                    value="{{ old('pin') }}"
                    autocomplete="off"
                    class="blooket-input w-full px-6 py-4 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 uppercase tracking-[0.25em]"
                    placeholder="PIN ROOM"
                    oninput="this.value = this.value.toUpperCase()"
                    required
                >
                @error('pin') <span class="text-sm font-bold text-[#FB7185] text-center mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Name Input --}}
            <div class="flex flex-col gap-1">
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', auth()->user()?->name) }}"
                    class="blooket-input w-full px-6 py-4 bg-white dark:bg-slate-900 text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500 tracking-normal"
                    placeholder="NAMA KAMU"
                    required
                >
                @error('name') <span class="text-sm font-bold text-[#FB7185] text-center mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Character Selector Bouncy --}}
            <div class="bg-[#FEF9EF] dark:bg-slate-900/80 p-4 rounded-[32px] border-4 border-black shadow-[0px_5px_0px_#000] flex flex-col items-center gap-3">
                <span class="font-display font-black text-sm uppercase text-black dark:text-white tracking-wide">Pilih Avatar</span>
                
                <div class="w-24 h-24 bg-white dark:bg-slate-800 rounded-full border-4 border-black flex items-center justify-center shadow-inner relative" id="avatar-preview">
                    {{-- JS populated --}}
                </div>
                
                <div class="w-full flex justify-between gap-3 px-2">
                    <button type="button" class="blooket-btn flex-1 py-2 bg-[#FB7185] text-black font-black text-xl flex justify-center items-center" onclick="prevBase()">◀</button>
                    <button type="button" class="blooket-btn flex-1 py-2 bg-[#06B6D4] text-black font-black text-2xl flex justify-center items-center" onclick="nextAcc()">🎩</button>
                    <button type="button" class="blooket-btn flex-1 py-2 bg-[#4ADE80] text-black font-black text-xl flex justify-center items-center" onclick="nextBase()">▶</button>
                </div>
                <input type="hidden" name="avatar" id="avatar-input" value="{{ old('avatar', auth()->user()?->avatar ?? 'cow|none') }}">
            </div>

            <button type="submit" id="join-btn" class="blooket-btn w-full py-5 mt-2 bg-[#FFE600] text-black font-display font-black text-2xl uppercase tracking-wider">
                MAIN! 🚀
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-focus PIN input
    document.getElementById('pin').focus();

    const bases = ['cow', 'horse', 'goat', 'pig', 'bear', 'fox', 'rabbit', 'dog', 'cat'];
    const accs = ['none', 'cap', 'glasses', 'helmet', 'crown', 'tophat', 'toga', 'bow'];
    
    let baseIndex = 0;
    let accIndex = 0;

    function updatePreview() {
        const base = bases[baseIndex];
        const acc = accs[accIndex];
        const code = `${base}|${acc}`;
        
        document.getElementById('avatar-input').value = code;

        const baseMap = { cow:'🐮', horse:'🐴', goat:'🐐', pig:'🐷', bear:'🐻', fox:'🦊', rabbit:'🐰', dog:'🐶', cat:'🐱' };
        const accMap = { cap:'🧢', glasses:'🕶️', helmet:'🪖', crown:'👑', tophat:'🎩', toga:'🎓', bow:'🎀', none:'' };
        
        const previewEl = document.getElementById('avatar-preview');
        // Tambahkan efek memantul ringan setiap diganti
        previewEl.style.transform = 'scale(0.8)';
        setTimeout(() => { previewEl.style.transform = 'scale(1)'; }, 100);
        previewEl.style.transition = 'transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

        previewEl.innerHTML = `
            <div class="relative inline-flex items-center justify-center pointer-events-none select-none" style="line-height: 1; font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;">
                <span class="text-[4rem] relative z-0 drop-shadow-sm">${baseMap[base]}</span>
                ${acc !== 'none' ? `<span class="absolute left-1/2 -translate-x-1/2 text-[3rem] -top-3 z-10 drop-shadow-md">${accMap[acc]}</span>` : ''}
            </div>
        `;
    }

    window.prevBase = () => { baseIndex = (baseIndex - 1 + bases.length) % bases.length; updatePreview(); };
    window.nextBase = () => { baseIndex = (baseIndex + 1) % bases.length; updatePreview(); };
    window.nextAcc = () => { accIndex = (accIndex + 1) % accs.length; updatePreview(); };

    // Initialize from old input
    const oldVal = document.getElementById('avatar-input').value;
    if(oldVal && oldVal.includes('|')) {
        const [ob, oa] = oldVal.split('|');
        if(bases.includes(ob)) baseIndex = bases.indexOf(ob);
        if(accs.includes(oa)) accIndex = accs.indexOf(oa);
    }
    updatePreview();
</script>
@endpush
@endsection
