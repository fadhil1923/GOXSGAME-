@extends('layouts.neo')

@section('title', 'Detail Room')
@section('page-title', 'Detail Room')
@section('page-subtitle', $room->game->title)

@section('content')
<a href="{{ route('teacher.rooms.index') }}" class="neo-back-btn inline-flex items-center gap-1.5 mb-6">
    <span class="material-symbols-outlined text-base">arrow_back</span>
    Kembali ke Daftar Room
</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- PIN Card (BESAR) --}}
    <div class="lg:col-span-1">
        <div class="neo-box rounded-2xl bg-[#FFE600] p-8 flex flex-col items-center gap-4 text-center sticky top-24">
            <span class="neo-tag px-3 py-1 rounded-lg bg-black text-[#FFE600] font-mono text-xs font-black uppercase tracking-widest">
                PIN ROOM
            </span>
            <div class="font-mono font-black text-6xl text-black tracking-[0.25em] py-4 px-6 bg-white rounded-xl border-3 border-black shadow-[4px_4px_0px_#000000]">
                {{ $room->room_code }}
            </div>
            <p class="font-body text-sm font-bold text-black">Bagikan kode ini ke siswa agar bisa bergabung!</p>
            <span class="neo-tag px-3 py-1.5 rounded-lg {{ $room->status === 'active' ? 'bg-[#4ADE80]' : 'bg-stone-300' }} text-black font-mono text-xs font-black uppercase">
                {{ $room->status === 'active' ? '🟢 Room Aktif' : '🔴 Room Ditutup' }}
            </span>
        </div>
    </div>

    {{-- Game Info + Students --}}
    <div class="lg:col-span-2 flex flex-col gap-6">
        {{-- Game Info --}}
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 p-6 flex gap-4">
            <div class="w-20 h-20 rounded-xl bg-[#2563EB] border-2 border-black overflow-hidden shrink-0">
                <img src="{{ $room->game->auto_thumbnail }}" alt="{{ $room->game->title }}" referrerpolicy="no-referrer" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';">
            </div>
            <div class="flex flex-col gap-2 min-w-0">
                <h3 class="font-display font-black text-lg text-black dark:text-white">{{ $room->game->title }}</h3>
                <p class="text-sm font-body text-stone-600 dark:text-slate-300 leading-relaxed line-clamp-2">{{ $room->game->description }}</p>
                <div class="flex gap-2 flex-wrap">
                    <span class="neo-tag px-2 py-0.5 rounded bg-[#4ADE80] text-black text-[10px] font-mono font-bold">+{{ $room->game->xp_reward }} XP</span>
                    <a href="{{ $room->game->game_url }}" target="_blank" class="neo-tag px-2 py-0.5 rounded bg-[#2563EB] text-white text-[10px] font-mono font-bold hover:bg-blue-700 transition">
                        🔗 Buka Game
                    </a>
                </div>
            </div>
        </div>

        {{-- Students List --}}
        <div class="neo-box rounded-2xl bg-white dark:bg-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b-3 border-black bg-[#FEF9EF] dark:bg-slate-900 flex items-center justify-between">
                <h3 class="font-display font-black text-base text-black dark:text-white">Daftar Siswa</h3>
                <div class="flex items-center gap-2">
                    <span id="online-dot" class="w-2 h-2 rounded-full bg-[#4ADE80] border border-black animate-ping"></span>
                    <span id="student-count" class="neo-tag px-2 py-0.5 rounded bg-[#2563EB] text-white text-xs font-mono font-bold">
                        {{ $room->students->count() }} Siswa
                    </span>
                </div>
            </div>
            <div id="student-list" class="divide-y-2 divide-black/10 dark:divide-slate-700 max-h-[500px] overflow-y-auto">
                @if($room->students->isEmpty())
                    <div id="empty-state" class="py-12 text-center">
                        <p class="text-3xl mb-2">👩‍🎓</p>
                        <p class="font-display font-black text-stone-400 dark:text-slate-500">Belum ada siswa yang bergabung.</p>
                    </div>
                @else
                    @foreach($room->students as $index => $student)
                    <div class="student-item px-6 py-3 flex items-center gap-3" data-id="{{ $student->id }}">
                        <div class="w-12 h-12 rounded-full border-2 border-black overflow-hidden bg-[#FEF9EF] dark:bg-slate-700 flex items-center justify-center shrink-0">
                            <x-avatar :code="$student->avatar" size="md" />
                        </div>
                        <div class="flex flex-col flex-1 min-w-0">
                            <span class="font-display font-bold text-sm text-black dark:text-white">{{ $student->name }}</span>
                            <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400">{{ $student->email }}</span>
                        </div>
                        <span class="student-index text-xs font-mono font-black text-stone-400 dark:text-slate-500 shrink-0">#{{ $index + 1 }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ===== POLLING: Daftar Siswa Real-time =====
    const studentsUrl = "{{ route('teacher.rooms.students', $room) }}";
    const countEl = document.getElementById('student-count');
    const listEl = document.getElementById('student-list');
    let knownStudentIds = new Set([
        @foreach($room->students as $s) {{ $s->id }}, @endforeach
    ]);
    let currentStudentCount = knownStudentIds.size;

    async function pollStudents() {
        try {
            const res = await fetch(studentsUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (countEl) countEl.textContent = data.count + ' Siswa';

            data.students.forEach(student => {
                if (!knownStudentIds.has(student.id)) {
                    knownStudentIds.add(student.id);
                    currentStudentCount++;

                    // Hapus empty-state jika ada
                    const emptyState = document.getElementById('empty-state');
                    if (emptyState) emptyState.remove();

                    // Tambah item siswa baru dengan animasi
                    const div = document.createElement('div');
                    div.className = 'student-item px-6 py-3 flex items-center gap-3 bg-[#4ADE80]/20';
                    div.dataset.id = student.id;
                    
                    div.innerHTML = `
                        <div class="w-12 h-12 rounded-full border-2 border-black overflow-hidden bg-[#FEF9EF] dark:bg-slate-700 flex items-center justify-center shrink-0">
                            ${student.avatar_html}
                        </div>
                        <div class="flex flex-col flex-1 min-w-0">
                            <span class="font-display font-bold text-sm text-black dark:text-white">${student.name}</span>
                            <span class="text-[11px] font-mono text-stone-500 dark:text-slate-400">${student.email}</span>
                        </div>
                        <span class="student-index text-xs font-mono font-black text-stone-400 dark:text-slate-500 shrink-0">#${currentStudentCount}</span>
                        <span class="ml-2 neo-tag text-[9px] px-1.5 py-0.5 rounded bg-[#4ADE80] text-black font-mono font-black shrink-0">BARU!</span>
                    `;
                    listEl.appendChild(div);
                    listEl.scrollTop = listEl.scrollHeight;

                    // Hapus highlight BARU setelah 4 detik
                    setTimeout(() => {
                        div.classList.remove('bg-[#4ADE80]/20');
                        const badge = div.querySelector('span.bg-\\[\\#4ADE80\\]');
                        if (badge) badge.remove();
                    }, 4000);
                }
            });
        } catch(e) { /* silent */ }
    }

    setInterval(pollStudents, 3000);
</script>
@endpush
@endsection
