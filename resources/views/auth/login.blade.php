<x-guest-layout>
    <div class="w-full max-w-md mx-auto flex flex-col items-center gap-5 sm:gap-6 pop-in">
        
        {{-- Brand / Title Logo --}}
        <div class="text-center flex flex-col items-center">
            <a href="/" class="group flex flex-col items-center gap-3">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#FFE600] rounded-2xl sm:rounded-3xl border-4 border-black shadow-[5px_5px_0px_#000] flex items-center justify-center text-3xl sm:text-4xl group-hover:scale-105 group-hover:-rotate-3 transition transform duration-200">
                    🎮
                </div>
                <h1 class="font-display font-black text-white text-3xl sm:text-4xl tracking-widest uppercase leading-tight pt-1"
                    style="text-shadow: 3px 3px 0px #000, -2px -2px 0px #000, 2px -2px 0px #000, -2px 2px 0px #000, 0px 6px 0px #000;">
                    GOXS GAME
                </h1>
            </a>
            <p class="font-mono text-xs sm:text-sm font-bold text-white/95 mt-2 bg-black/40 px-3.5 py-1 rounded-full border-2 border-black/50 shadow-[2px_2px_0px_rgba(0,0,0,0.3)]">
                Portal Masuk Guru & Administrator
            </p>
        </div>

        {{-- Login Card Neo-Box --}}
        <div class="w-full neo-box p-6 sm:p-8 flex flex-col gap-5">
            
            <div class="flex items-center justify-between border-b-3 border-black pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🔐</span>
                    <h2 class="font-display font-black text-xl text-black uppercase tracking-tight">Masuk Akun</h2>
                </div>
                <span class="bg-[#4ADE80] text-black text-xs font-mono font-black px-2.5 py-1 rounded-full border-2 border-black shadow-[2px_2px_0px_#000]">
                    PORTAL
                </span>
            </div>

            <!-- Session Status Alert -->
            <x-auth-session-status class="p-3 rounded-xl bg-[#4ADE80] border-2 border-black text-black font-body font-bold text-xs" :status="session('status')" />

            @if($errors->any())
                <div class="p-3 rounded-xl bg-[#FB7185] border-2 border-black text-black font-body font-bold text-xs flex items-center gap-2 shadow-[2px_2px_0px_#000]">
                    <span class="material-symbols-outlined text-base shrink-0">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                <!-- Email Input -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="font-display font-black text-xs uppercase tracking-wider text-black flex items-center justify-between">
                        <span>Email Akun</span>
                        <span class="text-stone-400 font-mono text-[10px]">Contoh: admin@goxsgame.com</span>
                    </label>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center text-stone-500 dark:text-slate-400 z-10">
                            <span class="material-symbols-outlined text-xl">mail</span>
                        </div>
                        <input id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="neo-input w-full bg-[#FEF9EF] dark:bg-slate-900 pl-14 pr-5 py-3.5 text-sm font-body text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="font-display font-black text-xs uppercase tracking-wider text-black dark:text-white">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-mono font-bold text-[#2563EB] dark:text-[#60A5FA] hover:underline">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center text-stone-500 dark:text-slate-400 z-10">
                            <span class="material-symbols-outlined text-xl">lock</span>
                        </div>
                        <input id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="neo-input w-full bg-[#FEF9EF] dark:bg-slate-900 pl-14 pr-14 py-3.5 text-sm font-body text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-500 hover:text-black dark:text-slate-400 dark:hover:text-white flex items-center justify-center p-1 rounded-full focus:outline-none z-10" title="Tampilkan sandi">
                            <span class="material-symbols-outlined text-xl" id="pw-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Status -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember" 
                            class="w-4 h-4 rounded-md border-2 border-black text-[#2563EB] focus:ring-0 cursor-pointer">
                        <span class="text-xs font-mono font-bold text-stone-700 dark:text-slate-300">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="neo-btn w-full py-4 mt-2 bg-[#FFE600] text-black font-display font-black text-base uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>Masuk Sekarang</span>
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
            </form>

            {{-- Quick Login Helpers (Untuk kemudahan pengujian) --}}
            <div class="pt-2 border-t-2 border-stone-200 dark:border-white/10 flex flex-col gap-2">
                <span class="text-[11px] font-mono font-bold text-stone-500 dark:text-slate-400 text-center uppercase tracking-wider">
                    ⚡ Klik Akun Cepat (Demo):
                </span>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillCreds('admin@goxsgame.com', 'password')" class="px-2.5 py-1.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-700 hover:bg-[#FFE600] dark:hover:bg-[#FFE600] text-left transition flex flex-col">
                        <span class="font-display font-black text-[11px] text-black dark:text-white hover:text-black leading-tight">👑 Admin</span>
                        <span class="font-mono text-[9px] text-stone-500 dark:text-slate-300 truncate">admin@goxsgame.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('teacher@goxsgame.com', 'password')" class="px-2.5 py-1.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-700 hover:bg-[#4ADE80] dark:hover:bg-[#4ADE80] text-left transition flex flex-col">
                        <span class="font-display font-black text-[11px] text-black dark:text-white hover:text-black leading-tight">👨‍🏫 Guru Demo</span>
                        <span class="font-mono text-[9px] text-stone-500 dark:text-slate-300 truncate">teacher@goxsgame.com</span>
                    </button>
                </div>
            </div>

            {{-- Link Siswa / Register --}}
            <div class="bg-[#FEF9EF] dark:bg-slate-900 p-3.5 rounded-2xl border-2 border-black flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🎒</span>
                    <span class="font-body font-bold text-black dark:text-white">Kamu seorang Siswa?</span>
                </div>
                <a href="{{ route('student.join') }}" class="font-display font-black text-[#2563EB] dark:text-[#60A5FA] hover:underline uppercase text-[11px]">
                    Masuk PIN Game ➔
                </a>
            </div>

            @if (Route::has('register'))
            <div class="text-center text-xs font-mono font-bold text-stone-600 dark:text-slate-400">
                Belum punya akun guru? 
                <a href="{{ route('register') }}" class="text-[#2563EB] dark:text-[#60A5FA] underline font-black">Daftar Akun Baru</a>
            </div>
            @endif
        </div>

        {{-- Footer Credit --}}
        <div class="text-center text-xs font-mono font-bold text-white/80">
            &copy; {{ date('Y') }} GOXSGAME • Edu Game Learning Platform
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwInput = document.getElementById('password');
            const pwIcon = document.getElementById('pw-icon');
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                pwIcon.textContent = 'visibility_off';
            } else {
                pwInput.type = 'password';
                pwIcon.textContent = 'visibility';
            }
        }

        function fillCreds(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
