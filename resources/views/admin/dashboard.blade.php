@extends('layouts.panel')
@section('title', 'Beranda Admin')

@section('content')
    <h1 class="t-page-title mb-6">Beranda Admin</h1>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        @foreach ([['Siswa', $students, 'users', 'bg-brand-100 text-brand-600'], ['Guru', $teachers, 'user', 'bg-sky-100 text-sky-600'], ['Jurusan', $majors, 'award', 'bg-orange-100 text-orange-600'], ['Mata Pelajaran', $subjects, 'book', 'bg-emerald-100 text-emerald-600'], ['Kuis Terbit', $quizzes, 'check-square', 'bg-pink-100 text-pink-600'], ['Pengerjaan Selesai', $attempts, 'bar-chart', 'bg-amber-100 text-amber-600']] as [$label, $value, $icon, $tone])
            <div class="t-card flex items-center gap-4 p-4">
                <span class="grid size-11 place-items-center rounded-xl {{ $tone }}"><x-icon :name="$icon" :size="20" /></span>
                <div><p class="text-2xl font-extrabold leading-none">{{ $value }}</p><p class="mt-1 text-xs font-semibold text-mute">{{ $label }}</p></div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="t-card">
            <h2 class="mb-3 font-extrabold">Mulai dari sini</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.majors.create') }}" class="t-btn t-btn-soft t-btn-sm"><x-icon name="plus" :size="14" /> Jurusan</a>
                <a href="{{ route('admin.subjects.create') }}" class="t-btn t-btn-soft t-btn-sm"><x-icon name="plus" :size="14" /> Mata pelajaran</a>
                <a href="{{ route('admin.users.create') }}" class="t-btn t-btn-soft t-btn-sm"><x-icon name="plus" :size="14" /> Pengguna</a>
            </div>
        </section>

        <section class="t-card">
            <h2 class="mb-3 font-extrabold">Mapel tanpa guru</h2>
            @if ($unassigned->isEmpty())
                <p class="text-sm text-mute">Semua mata pelajaran sudah punya guru pengampu.</p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach ($unassigned as $s)
                        <li class="flex items-center justify-between"><span class="font-semibold">{{ $s->name }}</span><a href="{{ route('admin.subjects.edit', $s) }}" class="font-bold text-brand-600 hover:underline">Tugaskan</a></li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
