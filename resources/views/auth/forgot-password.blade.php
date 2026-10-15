<x-guest-layout>
    <div class="w-full max-w-md mx-auto flex flex-col items-center gap-5 sm:gap-6 pop-in">
        
        {{-- Brand / Title Logo --}}
        <div class="text-center flex flex-col items-center">
            <a href="/" class="group flex flex-col items-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#FFE600] rounded-2xl sm:rounded-3xl border-4 border-black shadow-[4px_4px_0px_#000] flex items-center justify-center text-3xl sm:text-4xl mb-3 group-hover:scale-105 transition transform">
                    🎮
                </div>
                <h1 class="font-display font-black text-white text-3xl sm:text-4xl tracking-widest uppercase leading-none"
                    style="text-shadow: 3px 3px 0px #000, -2px -2px 0px #000, 2px -2px 0px #000, -2px 2px 0px #000, 0px 6px 0px #000;">
                    GOXS GAME
                </h1>
            </a>
            <p class="font-mono text-xs sm:text-sm font-bold text-white/90 mt-2 bg-black/30 px-3 py-1 rounded-full border border-white/20">
                Pemulihan Kata Sandi
            </p>
        </div>

        {{-- Forgot Password Card Neo-Box --}}
        <div class="w-full neo-box p-6 sm:p-8 flex flex-col gap-5">
            <div class="flex items-center justify-between border-b-3 border-black pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🔑</span>
                    <h2 class="font-display font-black text-xl text-black uppercase tracking-tight">Lupa Password</h2>
                </div>
            </div>

            <p class="font-body text-xs sm:text-sm text-stone-600 dark:text-slate-300 leading-relaxed">
                Masukkan email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>

            <!-- Session Status Alert -->
            <x-auth-session-status class="p-3 rounded-xl bg-[#4ADE80] border-2 border-black text-black font-body font-bold text-xs" :status="session('status')" />

            @if($errors->any())
                <div class="p-3 rounded-xl bg-[#FB7185] border-2 border-black text-black font-body font-bold text-xs flex items-center gap-2 shadow-[2px_2px_0px_#000]">
                    <span class="material-symbols-outlined text-base shrink-0">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
                @csrf

                <!-- Email Input -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="font-display font-black text-xs uppercase tracking-wider text-black dark:text-white">
                        Email Terdaftar
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
                            placeholder="nama@email.com"
                            class="neo-input w-full bg-[#FEF9EF] dark:bg-slate-900 pl-14 pr-5 py-3.5 text-sm font-body text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="neo-btn w-full py-4 mt-2 bg-[#2563EB] text-white font-display font-black text-base uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>Kirim Link Reset</span>
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </form>

            <div class="text-center text-xs font-mono font-bold text-stone-600 dark:text-slate-400 border-t-2 border-stone-200 dark:border-white/10 pt-3">
                Ingat kembali kata sandi Anda? 
                <a href="{{ route('login') }}" class="text-[#2563EB] dark:text-[#60A5FA] underline font-black">Kembali ke Login</a>
            </div>
        </div>

        {{-- Footer Credit --}}
        <div class="text-center text-xs font-mono font-bold text-white/80">
            &copy; {{ date('Y') }} GOXSGAME • Edu Game Learning Platform
        </div>
    </div>
</x-guest-layout>
