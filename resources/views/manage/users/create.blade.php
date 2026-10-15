@extends('layouts.neo')

@section('title', 'Tambah Pengguna — GOXSGAME')
@section('page-title', 'Tambah Pengguna')
@section('page-subtitle', 'Buat akun Admin, Guru, atau Siswa baru')

@section('content')
<div class="w-full flex flex-col gap-4">
    <div>
        <a href="{{ route('manage.users.index') }}" class="neo-back-btn px-3 py-2 rounded-xl font-display font-black text-xs uppercase inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Pengguna
        </a>
    </div>

    <form method="POST" action="{{ route('manage.users.store') }}" class="w-full neo-box rounded-2xl bg-white dark:bg-slate-800 p-5 sm:p-8 flex flex-col gap-5 sm:gap-6">
        @csrf

        <div class="flex flex-col gap-1.5">
            <label for="name" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                Nama Lengkap <span class="text-[#FB7185]">*</span>
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                placeholder="Contoh: Siti Rahmawati, S.Pd"
                class="neo-input rounded-xl px-4 py-3 bg-white dark:bg-slate-900 font-body text-black dark:text-white w-full">
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="email" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                Email Pengguna <span class="text-[#FB7185]">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                placeholder="nama@goxsgame.com"
                class="neo-input rounded-xl px-4 py-3 bg-white dark:bg-slate-900 font-body text-black dark:text-white w-full">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="password" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                    Password <span class="text-[#FB7185]">*</span>
                </label>
                <input id="password" type="password" name="password" required minlength="8"
                    placeholder="Minimal 8 karakter"
                    class="neo-input rounded-xl px-4 py-3 bg-white dark:bg-slate-900 font-body text-black dark:text-white w-full">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="role" class="font-display font-black text-xs sm:text-sm uppercase tracking-wide text-black dark:text-white">
                    Peran / Role <span class="text-[#FB7185]">*</span>
                </label>
                <select id="role" name="role" required class="neo-input rounded-xl px-4 py-3 bg-white dark:bg-slate-900 font-body text-black dark:text-white w-full">
                    <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>👨‍🏫 Guru (Buat & Mainkan Room)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>👑 Administrator (Akses Penuh)</option>
                    <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>🎒 Siswa</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-3">
            <button type="submit" class="neo-btn flex-1 py-3.5 rounded-xl bg-[#4ADE80] text-black font-display font-black text-sm uppercase shadow-[3px_3px_0px_#000]">
                💾 Simpan Pengguna
            </button>
            <a href="{{ route('manage.users.index') }}" class="neo-btn px-6 py-3.5 rounded-xl bg-white dark:bg-slate-700 text-black dark:text-white font-display font-black text-xs uppercase text-center shadow-[3px_3px_0px_#000]">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
