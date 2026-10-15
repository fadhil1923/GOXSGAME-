<x-guest-layout>
    <div class="w-full max-w-md mx-auto flex flex-col items-center gap-5 sm:gap-6 pop-in">
        
        <div class="text-center flex flex-col items-center">
            <a href="/" class="group flex flex-col items-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#FFE600] rounded-2xl sm:rounded-3xl border-4 border-black shadow-[4px_4px_0px_#000] flex items-center justify-center text-3xl sm:text-4xl mb-3 group-hover:scale-105 transition transform">
                    🔒
                </div>
                <h1 class="font-display font-black text-white text-3xl sm:text-4xl tracking-widest uppercase leading-none"
                    style="text-shadow: 3px 3px 0px #000, -2px -2px 0px #000, 2px -2px 0px #000, -2px 2px 0px #000, 0px 6px 0px #000;">
                    GOXS GAME
                </h1>
            </a>
            <p class="font-mono text-xs sm:text-sm font-bold text-white/90 mt-2 bg-black/30 px-3 py-1 rounded-full border border-white/20">
                Konfirmasi Keamanan
            </p>
        </div>

        <div class="w-full neo-box p-6 sm:p-8 flex flex-col gap-5">
            <div class="flex items-center justify-between border-b-3 border-black pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🛡️</span>
                    <h2 class="font-display font-black text-xl text-black dark:text-white uppercase tracking-tight">Konfirmasi Sandi</h2>
                </div>
            </div>

            <p class="font-body text-xs sm:text-sm text-stone-600 dark:text-slate-300 leading-relaxed">
                Ini adalah area aplikasi yang aman. Harap konfirmasikan kata sandi Anda sebelum melanjutkan.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-4">
                @csrf

                <!-- Password -->
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="font-display font-black text-xs uppercase tracking-wider text-black dark:text-white">
                        Password
                    </label>
                    <input id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="neo-input w-full bg-[#FEF9EF] dark:bg-slate-900 px-4 py-3.5 text-sm font-body text-black dark:text-white placeholder:text-stone-400 dark:placeholder:text-slate-500">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <button type="submit" class="neo-btn w-full py-4 mt-2 bg-[#FFE600] text-black font-display font-black text-base uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>Konfirmasi</span>
                    <span class="material-symbols-outlined text-lg">check</span>
                </button>
            </form>
        </div>

        <div class="text-center text-xs font-mono font-bold text-white/80">
            &copy; {{ date('Y') }} GOXSGAME • Edu Game Learning Platform
        </div>
    </div>
</x-guest-layout>
