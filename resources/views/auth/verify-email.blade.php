<x-guest-layout>
    <div class="w-full max-w-md mx-auto flex flex-col items-center gap-5 sm:gap-6 pop-in">
        
        <div class="text-center flex flex-col items-center">
            <a href="/" class="group flex flex-col items-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#FFE600] rounded-2xl sm:rounded-3xl border-4 border-black shadow-[4px_4px_0px_#000] flex items-center justify-center text-3xl sm:text-4xl mb-3 group-hover:scale-105 transition transform">
                    📧
                </div>
                <h1 class="font-display font-black text-white text-3xl sm:text-4xl tracking-widest uppercase leading-none"
                    style="text-shadow: 3px 3px 0px #000, -2px -2px 0px #000, 2px -2px 0px #000, -2px 2px 0px #000, 0px 6px 0px #000;">
                    GOXS GAME
                </h1>
            </a>
            <p class="font-mono text-xs sm:text-sm font-bold text-white/90 mt-2 bg-black/30 px-3 py-1 rounded-full border border-white/20">
                Verifikasi Alamat Email
            </p>
        </div>

        <div class="w-full neo-box p-6 sm:p-8 flex flex-col gap-5">
            <div class="flex items-center justify-between border-b-3 border-black pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✉️</span>
                    <h2 class="font-display font-black text-xl text-black dark:text-white uppercase tracking-tight">Verifikasi Email</h2>
                </div>
            </div>

            <p class="font-body text-xs sm:text-sm text-stone-600 dark:text-slate-300 leading-relaxed">
                Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="p-3.5 rounded-xl bg-[#4ADE80] border-2 border-black font-body font-bold text-xs sm:text-sm text-black text-center shadow-[2px_2px_0px_#000]">
                    Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
                </div>
            @endif

            <div class="flex flex-col gap-3 pt-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="neo-btn w-full py-3.5 bg-[#FFE600] text-black font-display font-black text-sm uppercase tracking-wider flex items-center justify-center gap-2">
                        <span>Kirim Ulang Email Verifikasi</span>
                        <span class="material-symbols-outlined text-base">send</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="text-xs font-mono font-bold text-stone-600 dark:text-slate-400 hover:text-black dark:hover:text-white underline">
                        Keluar Akun (Log Out)
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center text-xs font-mono font-bold text-white/80">
            &copy; {{ date('Y') }} GOXSGAME • Edu Game Learning Platform
        </div>
    </div>
</x-guest-layout>
