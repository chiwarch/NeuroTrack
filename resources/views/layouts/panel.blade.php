<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — NeuroTrack</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-ink antialiased">
@php
    $u = auth()->user();
    $nav = match ($u->role) {
        'admin' => [
            ['admin.dashboard', 'home', 'Beranda', ['admin.dashboard']],
            ['admin.users.index', 'users', 'Pengguna', ['admin.users.*']],
            ['admin.majors.index', 'award', 'Jurusan', ['admin.majors.*']],
            ['admin.subjects.index', 'book', 'Mata Pelajaran', ['admin.subjects.*']],
            ['admin.classes.index', 'users', 'Kelas', ['admin.classes.*']],
            ['admin.announcements.index', 'send', 'Pengumuman', ['admin.announcements.*']],
            ['admin.awards.index', 'award', 'Penghargaan', ['admin.awards.*']],
            ['admin.badges.index', 'star', 'Lencana', ['admin.badges.*']],
            ['admin.leaderboard', 'bar-chart', 'Peringkat', ['admin.leaderboard']],
        ],
        'guru' => [
            ['guru.dashboard', 'home', 'Beranda', ['guru.dashboard']],
            ['guru.quizzes.index', 'check-square', 'Kuis Saya', ['guru.quizzes.*', 'guru.questions.*']],
            ['guru.assignments.index', 'book', 'Tugas', ['guru.assignments.*']],
            ['guru.announcements.index', 'send', 'Pengumuman', ['guru.announcements.*']],
            ['guru.awards.index', 'award', 'Penghargaan', ['guru.awards.*']],
            ['guru.leaderboard', 'star', 'Peringkat Kelas', ['guru.leaderboard']],
            ['guru.attendance.index', 'check-circle', 'Absensi', ['guru.attendance.*']],
            ['guru.students.index', 'users', 'Penilaian Siswa', ['guru.students.*']],
        ],
        'orang_tua' => [
            ['orang_tua.dashboard', 'home', 'Beranda', ['orang_tua.dashboard', 'orang_tua.children.*']],
            ['orang_tua.leaderboard', 'star', 'Peringkat Kelas', ['orang_tua.leaderboard']],
            ['orang_tua.announcements.index', 'send', 'Pengumuman', ['orang_tua.announcements.*']],
        ],
        default => [
            ['siswa.dashboard', 'home', 'Beranda', ['siswa.dashboard']],
            ['siswa.quizzes.index', 'book', 'Katalog Kuis', ['siswa.quizzes.*', 'siswa.attempts.*']],
            ['siswa.assignments.index', 'book', 'Tugas', ['siswa.assignments.*']],
            ['siswa.achievements', 'award', 'Prestasi', ['siswa.achievements']],
            ['siswa.leaderboard', 'star', 'Peringkat', ['siswa.leaderboard']],
            ['siswa.announcements.index', 'send', 'Pengumuman', ['siswa.announcements.*']],
            ['siswa.stats', 'bar-chart', 'Statistik', ['siswa.stats']],
            ['siswa.attendance.index', 'check-circle', 'Absensi', ['siswa.attendance.*']],
            ['siswa.history', 'clock', 'Riwayat', ['siswa.history']],
        ],
    };
@endphp
<div class="flex min-h-screen">
    <div id="side-overlay" class="fixed inset-0 z-30 hidden bg-ink/40 lg:hidden"></div>

    <aside id="side" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-100 bg-white p-4 transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
        <a href="{{ route('home') }}" class="logo mb-6 px-2">
            <img src="{{ asset('images/grad-cap.webp') }}" alt="" width="32" height="32"><span>Neuro<b>Track</b></span>
        </a>

        <nav class="flex-1 space-y-1">
            @foreach ($nav as [$route, $icon, $label, $patterns])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs(...$patterns) ? 'bg-brand-50 text-brand-600' : 'text-mute hover:bg-slate-50' }}">
                    <x-icon :name="$icon" :size="18" /> {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="mt-4 flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-600">{{ $u->initials() }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-bold">{{ $u->name }}</p>
                <p class="text-xs text-mute">{{ \App\Models\User::ROLES[$u->role] }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cursor-pointer rounded-lg p-2 text-mute hover:bg-white hover:text-rose-600" title="Keluar" aria-label="Keluar"><x-icon name="logout" :size="18" /></button>
            </form>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-slate-100 bg-white/80 px-4 py-3 backdrop-blur lg:hidden">
            <button id="side-toggle" type="button" class="cursor-pointer rounded-lg p-2 hover:bg-slate-100" aria-label="Menu"><x-icon name="menu" :size="22" /></button>
            <span class="font-extrabold">Neuro<span class="text-brand-500">Track</span></span>
        </header>

        <main class="mx-auto w-full max-w-6xl p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><x-icon name="check-circle" :size="18" /> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
