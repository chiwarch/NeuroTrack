@extends('layouts.panel')
@section('title', 'Absensi')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Absensi</h1>
        <p class="mt-1 text-sm text-mute">{{ $class ? 'Kelas '.$class->name.' · '.$class->academic_year : 'Kamu belum terdaftar di kelas mana pun. Hubungi admin.' }}</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="t-card flex items-center gap-5">
            <div class="ring" style="--p: {{ $all['rate'] }}"><span>{{ $all['rate'] + 0 }}%</span></div>
            <div>
                <p class="font-extrabold">Rate Kehadiran</p>
                <p class="mt-1 text-sm text-mute">Bulan ini {{ $month['rate'] + 0 }}% dari {{ $month['days'] }} hari.</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:col-span-2">
            @foreach ([['present', 'Hadir'], ['late', 'Terlambat'], ['sick', 'Sakit'], ['permission', 'Izin'], ['absent', 'Alfa'], ['dispensation', 'Dispensasi']] as [$key, $label])
                <div class="t-card p-4">
                    <p class="text-2xl font-extrabold leading-none">{{ $all[$key] }}</p>
                    <p class="mt-2 text-xs font-semibold text-mute">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <p class="t-hint mt-2">Rate dihitung dari hadir + terlambat dibanding semua hari tercatat. Dispensasi tidak dihitung.</p>

    <section class="mt-8">
        <h2 class="mb-3 text-lg font-extrabold">Riwayat</h2>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Tanggal</th><th class="t-th">Status</th><th class="t-th">Jam</th></tr></thead>
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
        <div class="mt-4">{{ $history->links() }}</div>
    </section>
@endsection
