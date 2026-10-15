@extends('layouts.neo')

@section('title', 'Pesan Pengembangan')
@section('page-title', 'Pesan Pengembangan')
@section('page-subtitle', 'Kirim ide fitur, kendala, atau saran untuk pengembangan GOXSGAME')

@push('styles')
<style>
    .category-chip input:checked + div {
        background-color: #FFE600 !important;
        color: #000000 !important;
        border-color: #000000 !important;
        box-shadow: 3px 3px 0px #000000 !important;
        transform: translateY(-2px);
    }
    .category-chip input:checked + div * {
        color: #000000 !important;
    }
</style>
@endpush

@section('content')
<div class="w-full flex flex-col gap-6">

    {{-- Breadcrumb / Header Banner --}}
    <div class="neo-box rounded-2xl bg-gradient-to-r from-[#2563EB] to-[#7C3AED] p-5 sm:p-7 border-3 border-black shadow-[5px_5px_0px_#000] text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FFE600] text-black border-3 border-black shadow-[3px_3px_0px_#000] flex items-center justify-center text-2xl sm:text-3xl shrink-0">
                💡
            </div>
            <div>
                <h1 class="font-display font-black text-xl sm:text-2xl uppercase tracking-tight text-white drop-shadow-sm">
                    Kotak Pesan Pengembangan
                </h1>
                <p class="text-xs sm:text-sm font-mono text-white/90 mt-0.5">
                    Suara Anda sangat berarti! Setiap masukan dari guru & siswa langsung dibaca oleh Tim Administrator.
                </p>
            </div>
        </div>

        @auth
            <span class="neo-tag text-xs font-mono font-black bg-black text-[#FFE600] px-3 py-1.5 rounded-xl border-2 border-black shrink-0">
                ROLE: {{ strtoupper(auth()->user()->role) }}
            </span>
        @endauth
    </div>

    {{-- Grid Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Kolom Kiri: Form Input Pesan (7 Cols) --}}
        <div class="lg:col-span-7 flex flex-col gap-6">
            <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-7 border-3 border-black shadow-[5px_5px_0px_#000] flex flex-col gap-5">
                
                <div class="flex items-center justify-between border-b-2 sm:border-b-3 border-black/10 dark:border-white/10 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-2xl text-[#2563EB] dark:text-blue-400">edit_note</span>
                        <h2 class="font-display font-black text-lg sm:text-xl text-black dark:text-white uppercase tracking-tight">
                            Formulir Pesan Baru
                        </h2>
                    </div>
                    <span class="neo-tag text-[10px] sm:text-xs font-mono font-bold bg-[#4ADE80] text-black px-2 py-0.5 rounded">
                        KIRIM KE ADMIN
                    </span>
                </div>

                <form method="POST" action="{{ route('feedback.store') }}" class="flex flex-col gap-5">
                    @csrf

                    {{-- Info Pengirim (Otomatis jika Login) --}}
                    @auth
                        <div class="p-3.5 rounded-xl bg-[#FEF9EF] dark:bg-slate-900 border-2 border-black flex items-center justify-between gap-3 shadow-[2px_2px_0px_#000]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-black text-[#FFE600] font-display font-black text-lg flex items-center justify-center border-2 border-black shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex flex-col leading-tight">
                                    <span class="font-display font-black text-xs sm:text-sm text-black dark:text-white">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400">
                                        {{ auth()->user()->email }}
                                    </span>
                                </div>
                            </div>
                            <span class="neo-tag text-[10px] font-mono font-black px-2 py-0.5 rounded {{ auth()->user()->role === 'teacher' ? 'bg-[#4ADE80] text-black' : (auth()->user()->role === 'student' ? 'bg-[#2563EB] text-white' : 'bg-[#FB7185] text-black') }}">
                                {{ strtoupper(auth()->user()->role) }}
                            </span>
                        </div>
                    @else
                        {{-- Input Manual untuk Tamu --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <label for="name" class="font-display font-black text-xs uppercase text-black dark:text-white">Nama Kamu <span class="text-[#FB7185]">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                                    class="neo-input w-full px-4 py-2.5 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="email" class="font-display font-black text-xs uppercase text-black dark:text-white">Email <span class="text-[#FB7185]">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                                    class="neo-input w-full px-4 py-2.5 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm">
                            </div>
                        </div>
                    @endauth

                    {{-- Pilihan Kategori --}}
                    <div class="flex flex-col gap-2">
                        <label class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                            Kategori Masukan <span class="text-[#FB7185]">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <label class="category-chip cursor-pointer">
                                <input type="radio" name="category" value="feature_request" class="sr-only" {{ old('category', 'feature_request') == 'feature_request' ? 'checked' : '' }}>
                                <div class="p-2.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-900 text-black dark:text-white text-xs font-display font-black text-center flex flex-col items-center gap-1 transition">
                                    <span class="text-base">💡</span>
                                    <span>Saran Fitur</span>
                                </div>
                            </label>

                            <label class="category-chip cursor-pointer">
                                <input type="radio" name="category" value="bug_report" class="sr-only" {{ old('category') == 'bug_report' ? 'checked' : '' }}>
                                <div class="p-2.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-900 text-black dark:text-white text-xs font-display font-black text-center flex flex-col items-center gap-1 transition">
                                    <span class="text-base">🐛</span>
                                    <span>Lapor Bug</span>
                                </div>
                            </label>

                            <label class="category-chip cursor-pointer">
                                <input type="radio" name="category" value="ui_ux" class="sr-only" {{ old('category') == 'ui_ux' ? 'checked' : '' }}>
                                <div class="p-2.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-900 text-black dark:text-white text-xs font-display font-black text-center flex flex-col items-center gap-1 transition">
                                    <span class="text-base">🎨</span>
                                    <span>Tampilan / UI</span>
                                </div>
                            </label>

                            <label class="category-chip cursor-pointer">
                                <input type="radio" name="category" value="question" class="sr-only" {{ old('category') == 'question' ? 'checked' : '' }}>
                                <div class="p-2.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-900 text-black dark:text-white text-xs font-display font-black text-center flex flex-col items-center gap-1 transition">
                                    <span class="text-base">❓</span>
                                    <span>Pertanyaan</span>
                                </div>
                            </label>

                            <label class="category-chip cursor-pointer col-span-2 sm:col-span-2">
                                <input type="radio" name="category" value="other" class="sr-only" {{ old('category') == 'other' ? 'checked' : '' }}>
                                <div class="p-2.5 rounded-xl border-2 border-black bg-stone-100 dark:bg-slate-900 text-black dark:text-white text-xs font-display font-black text-center flex flex-row items-center justify-center gap-2 transition">
                                    <span class="text-base">💬</span>
                                    <span>Kritik & Masukan Lainnya</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Judul Pesan --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="subject" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                            Subjek / Judul Pesan <span class="text-[#FB7185]">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                            placeholder="Contoh: Tambahkan mode game kuis beregu per kelompok"
                            class="neo-input w-full px-4 py-3 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm placeholder:text-stone-400 dark:placeholder:text-slate-500">
                    </div>

                    {{-- Isi Pesan --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="message" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white flex items-center justify-between">
                            <span>Isi Pesan / Penjelasan Detail <span class="text-[#FB7185]">*</span></span>
                            <span class="text-stone-400 dark:text-slate-500 font-mono text-[10px] font-normal">Maksimal 5000 karakter</span>
                        </label>
                        <textarea name="message" id="message" rows="5" required
                            placeholder="Jelaskan ide atau masalah yang kamu temukan secara rinci..."
                            class="neo-input w-full p-4 bg-[#FEF9EF] dark:bg-slate-900 text-black dark:text-white text-xs sm:text-sm placeholder:text-stone-400 dark:placeholder:text-slate-500 rounded-2xl resize-y">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="neo-btn w-full py-4 rounded-xl bg-[#4ADE80] hover:bg-green-400 !text-black font-display font-black text-sm sm:text-base uppercase tracking-wider shadow-[4px_4px_0px_#000] flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-xl">send</span>
                        <span>Kirim Pesan</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Panduan & Riwayat Pesan Saya (5 Cols) --}}
        <div class="lg:col-span-5 flex flex-col gap-6">
            
            {{-- Kartu Info Tips --}}
            <div class="neo-box rounded-2xl bg-[#FFE600] p-5 sm:p-6 border-3 border-black shadow-[4px_4px_0px_#000] text-black">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl">🎯</span>
                    <h3 class="font-display font-black text-base uppercase">Kenapa Masukan Anda Berharga?</h3>
                </div>
                <p class="text-xs font-mono font-bold leading-relaxed">
                    Setiap saran fitur dan laporan bug langsung masuk ke dasbor Administrator GOXSGAME untuk dipertimbangkan pada update versi berikutnya.
                </p>
                <div class="mt-3 flex flex-col gap-1.5 text-[11px] font-mono">
                    <div class="flex items-center gap-1.5">
                        <span>⚡</span>
                        <span>Dipantau langsung oleh tim pengembang</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span>🔒</span>
                        <span>Pesan bersifat privat antara Anda dan Admin</span>
                    </div>
                </div>
            </div>

            {{-- Kartu Riwayat Pesan Pengguna --}}
            <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-6 border-3 border-black shadow-[4px_4px_0px_#000] flex flex-col gap-4">
                <div class="flex items-center justify-between border-b-2 border-black/10 dark:border-white/10 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl text-black dark:text-white">history</span>
                        <h3 class="font-display font-black text-sm uppercase text-black dark:text-white">
                            Riwayat Pesan Anda
                        </h3>
                    </div>
                    <span class="neo-tag text-[10px] font-mono font-black bg-stone-200 dark:bg-slate-700 text-black dark:text-white px-2 py-0.5 rounded">
                        {{ $myMessages->count() }} Terkirim
                    </span>
                </div>

                @if($myMessages->isEmpty())
                    <div class="py-8 text-center flex flex-col items-center justify-center gap-2">
                        <span class="text-4xl opacity-50">📬</span>
                        <p class="font-display font-bold text-xs text-stone-500 dark:text-slate-400">
                            Belum ada riwayat pesan pengembangan.
                        </p>
                        <p class="text-[11px] font-mono text-stone-400 dark:text-slate-500">
                            Pesan yang Anda kirim akan muncul di sini beserta status tanggapannya.
                        </p>
                    </div>
                @else
                    <div class="flex flex-col gap-3 max-h-[420px] overflow-y-auto pr-1">
                        @foreach($myMessages as $item)
                            <div class="p-3.5 rounded-xl border-2 border-black bg-[#FEF9EF] dark:bg-slate-900 shadow-[2px_2px_0px_#000] flex flex-col gap-2">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-display font-black text-xs text-black dark:text-white line-clamp-1">
                                        {{ $item->subject }}
                                    </span>
                                    <span class="neo-tag text-[9px] font-mono font-bold px-2 py-0.5 rounded shrink-0 {{ $item->status_badge_class }}">
                                        {{ $item->status_label }}
                                    </span>
                                </div>
                                <p class="text-[11px] font-mono text-stone-600 dark:text-slate-400 line-clamp-2">
                                    {{ $item->message }}
                                </p>

                                @if($item->admin_notes)
                                    <div class="p-2 rounded-lg bg-[#4ADE80]/20 border border-black text-[10px] font-mono text-black dark:text-emerald-300">
                                        💬 <strong>Tanggapan Admin:</strong> {{ $item->admin_notes }}
                                    </div>
                                @endif

                                <div class="flex items-center justify-between text-[10px] font-mono text-stone-400 dark:text-slate-500 pt-1 border-t border-black/10 dark:border-white/10">
                                    <span>{{ $item->category_label }}</span>
                                    <span>{{ $item->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
