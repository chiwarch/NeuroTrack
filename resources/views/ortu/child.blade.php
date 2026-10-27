@extends('layouts.panel')
@section('title', $student->name)

@section('content')
    <a href="{{ route('orang_tua.dashboard') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Beranda</a>
    <div class="mb-6">
        <h1 class="t-page-title">{{ $student->name }}</h1>
        <p class="mt-1 text-sm text-mute">{{ $student->nis }} · {{ $class ? 'Kelas '.$class->name : 'Belum punya kelas' }} · {{ $student->major?->code }}</p>
    </div>

    {{-- Prestasi --}}
    <section class="mb-10">
        <h2 class="mb-3 text-lg font-extrabold">Prestasi</h2>
        @include('gamification._level-card', ['gUser' => $student])
        <div class="mt-4">@include('gamification._earned', ['gUser' => $student])</div>
    </section>

    {{-- Absensi --}}
    <section>
        <h2 class="mb-3 text-lg font-extrabold">Absensi</h2>
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="t-card flex items-center gap-5">
                <div class="ring" style="--p: {{ $all['rate'] }}"><span>{{ $all['rate'] + 0 }}%</span></div>
                <div>
                    <p class="font-extrabold">Rate Kehadiran</p>
                    <p class="mt-1 text-sm text-mute">Bulan ini {{ $month['rate'] + 0 }}% dari {{ $month['days'] }} hari.</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 sm:grid-cols-6 lg:col-span-2">
                @foreach ([['present', 'Hadir'], ['late', 'Terlambat'], ['sick', 'Sakit'], ['permission', 'Izin'], ['absent', 'Alfa'], ['dispensation', 'Dispensasi']] as [$key, $label])
                    <div class="t-card p-4">
                        <p class="text-2xl font-extrabold leading-none">{{ $all[$key] }}</p>
                        <p class="mt-2 text-xs font-semibold text-mute">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="t-card mt-4 overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">10 hari terakhir</th><th class="t-th">Status</th><th class="t-th">Jam</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($history as $a)
                        <tr>
                            <td class="t-td font-semibold">{{ $a->attendance_date->translatedFormat('l, d F Y') }}</td>
                            <td class="t-td"><span class="t-badge {{ $a->tone() }}">{{ $a->label() }}</span></td>
                            <td class="t-td text-mute">{{ $a->check_in_at?->format('H:i') ?? '–' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="t-td text-center text-mute">Belum ada catatan absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Radar kemampuan --}}
    <section class="mt-10">
        <h2 class="mb-3 text-lg font-extrabold">Kemampuan</h2>
        @include('partials.stats')
    </section>

    {{-- Tugas --}}
    <section class="mt-10">
        <h2 class="mb-3 text-lg font-extrabold">Tugas</h2>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Tugas</th><th class="t-th">Tenggat</th><th class="t-th">Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($assignments as $a)
                        @php
                            $sub = $submissions->get($a->id);
                            if ($sub && $sub->isGraded()) { [$label, $tone] = ['Dinilai: '.($sub->score + 0).'/'.($a->max_score + 0), 'bg-emerald-100 text-emerald-700']; }
                            elseif ($sub) { [$label, $tone] = [$sub->isLate() ? 'Dikumpulkan (terlambat)' : 'Dikumpulkan', 'bg-sky-100 text-sky-700']; }
                            elseif ($a->isPastDeadline()) { [$label, $tone] = ['Belum dikumpulkan, lewat tenggat', 'bg-rose-100 text-rose-700']; }
                            else { [$label, $tone] = ['Belum dikumpulkan', 'bg-amber-100 text-amber-700']; }
                        @endphp
                        <tr>
                            <td class="t-td font-bold">{{ $a->title }}<span class="block text-xs font-medium text-mute">{{ $a->classSubject->subject->name }}</span></td>
                            <td class="t-td">{{ $a->deadline ? $a->deadline->translatedFormat('d M Y, H:i') : '–' }}</td>
                            <td class="t-td"><span class="t-badge {{ $tone }}">{{ $label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="t-td text-center text-mute">Belum ada tugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
