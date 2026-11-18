@extends('layouts.panel')
@section('title', 'Beranda Guru')

@section('content')
    @php
        $unfinalized = \App\Models\AttendanceSession::where('teacher_id', auth()->id())
            ->whereNull('closed_at')
            ->whereDate('attendance_date', '<', today())
            ->exists();
        $homeroomClasses = auth()->user()->homeroomClasses()->withCount('students')->get();
        $todaySessions = \App\Models\AttendanceSession::whereIn('class_id', $homeroomClasses->pluck('id'))
            ->whereDate('attendance_date', today())->get()->keyBy('class_id');
    @endphp

    @if ($unfinalized)
        <div data-testid="unfinalized-warning" class="mb-5 flex items-center gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
            <x-icon name="alert-triangle" :size="18" /> Ada sesi absensi yang belum difinalisasi.
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="t-page-title">Halo, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-mute">Kelola kuis untuk mata pelajaran yang kamu ampu.</p>
        </div>
        @if ($subjects->isNotEmpty())
            <a href="{{ route('guru.quizzes.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Buat Kuis</a>
        @endif
    </div>

    @if ($homeroomClasses->isNotEmpty())
        <section class="mb-8">
            <h2 class="mb-3 text-lg font-extrabold">Absensi Kelas Binaan</h2>
            <div class="space-y-3">
                @foreach ($homeroomClasses as $c)
                    @php
                        $s = $todaySessions->get($c->id);
                        $slug = \Illuminate\Support\Str::slug($c->name);
                    @endphp
                    <div class="t-card flex flex-wrap items-center gap-4" @if($s) data-testid="sessions-today-{{ $slug }}" @endif>
                        <div class="min-w-0 flex-1">
                            <p class="font-extrabold">{{ $c->name }}</p>
                            <p class="text-xs text-mute">{{ $c->students_count }} siswa · {{ $c->academic_year }}</p>
                        </div>
                        @if ($s)
                            <span class="t-badge {{ $s->isOpen() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $s->isOpen() ? 'Sesi terbuka' : 'Sesi ditutup' }}</span>
                            <a href="{{ route('guru.attendance.show', $s) }}" data-testid="start-attendance-{{ $slug }}" class="t-btn t-btn-primary">
                                {{ $s->isOpen() ? 'Buka' : 'Buka kembali' }} <x-icon name="arrow-right" :size="16" />
                            </a>
                        @else
                            <form method="POST" action="{{ route('guru.attendance.open', $c) }}">
                                @csrf
                                <button data-testid="start-attendance-{{ $slug }}" class="t-btn t-btn-primary">Mulai absensi hari ini</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([['Kuis', $quizCount, 'check-square', 'bg-brand-100 text-brand-600'], ['Diterbitkan', $publishedCount, 'check-circle', 'bg-emerald-100 text-emerald-600'], ['Total Soal', $questionCount, 'book', 'bg-sky-100 text-sky-600'], ['Pengerjaan Selesai', $attemptCount, 'users', 'bg-orange-100 text-orange-600']] as [$label, $value, $icon, $tone])
            <div class="t-card flex flex-col items-start gap-2 p-4">
                <span class="grid size-9 place-items-center rounded-xl {{ $tone }}"><x-icon :name="$icon" :size="18" /></span>
                <p class="text-2xl font-extrabold leading-none">{{ $value }}</p>
                <p class="text-xs font-semibold text-mute">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section>
            <h2 class="mb-3 text-lg font-extrabold">Mapel yang Diampu</h2>
            @if ($subjects->isEmpty())
                <div class="t-card text-sm text-mute">Kamu belum ditugaskan ke mata pelajaran mana pun. Hubungi admin.</div>
            @else
                <div class="space-y-3">
                    @foreach ($subjects as $s)
                        <div class="t-card flex items-center gap-3 p-4">
                            <img src="{{ $s->iconUrl() }}" alt="" class="size-10 object-contain" width="40" height="40">
                            <div><p class="font-bold">{{ $s->name }}</p><p class="text-xs text-mute">{{ $s->typeLabel() }}</p></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="lg:col-span-2">
            <h2 class="mb-3 text-lg font-extrabold">Pengerjaan Terbaru</h2>
            <div class="t-card overflow-x-auto p-0">
                @if ($recent->isEmpty())
                    <p class="p-5 text-sm text-mute">Belum ada siswa yang menyelesaikan kuismu.</p>
                @else
                    <table class="w-full"><tbody class="divide-y divide-slate-100">
                        @foreach ($recent as $a)
                            <tr>
                                <td class="t-td font-bold">{{ $a->user->name }}</td>
                                <td class="t-td text-mute">{{ $a->quiz->title }}</td>
                                <td class="t-td text-right"><span class="t-badge {{ $a->score >= $a->quiz->passing_score ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $a->score + 0 }}</span></td>
                            </tr>
                        @endforeach
                    </tbody></table>
                @endif
            </div>
        </section>
    </div>
    @include('announcements._latest')
@endsection
