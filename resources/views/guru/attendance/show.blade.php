@extends('layouts.panel')
@section('title', 'Absensi '.$session->schoolClass->name)

@section('content')
    <a href="{{ route('guru.attendance.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Absensi</a>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="t-page-title">{{ $session->schoolClass->name }}</h1>
            <p class="mt-1 text-sm text-mute">{{ $session->attendance_date->translatedFormat('l, d F Y') }} · terlambat setelah {{ substr($session->late_after, 0, 5) }}</p>
        </div>
        @if ($session->isOpen())
            <form method="POST" action="{{ route('guru.attendance.close', $session) }}" onsubmit="return confirm('Tutup absensi? Siswa yang belum tercatat akan ditandai Alfa.')">@csrf<button class="t-btn t-btn-danger">Tutup absensi</button></form>
        @elseif ($session->attendance_date->isToday())
            <form method="POST" action="{{ route('guru.attendance.reopen', $session) }}">@csrf<button class="t-btn t-btn-line">Buka kembali</button></form>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        @if ($session->isOpen())
            <div class="t-card flex flex-col items-center gap-3 text-center lg:sticky lg:top-6 lg:self-start"
                 id="qr-box" data-live-url="{{ route('guru.attendance.live', $session) }}">
                <p class="font-extrabold">Pindai untuk absen</p>
                <canvas id="qr-canvas" class="size-64 max-w-full rounded-xl" aria-label="Kode QR absensi"></canvas>
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100"><div id="qr-bar" class="h-full w-full origin-left bg-brand-500"></div></div>
                <p class="text-xs text-mute">Kode berganti otomatis tiap 20 detik. Jangan difoto atau dibagikan.</p>
            </div>
        @else
            <div class="t-card text-sm text-mute lg:self-start" id="qr-box" data-live-url="{{ route('guru.attendance.live', $session) }}">
                Absensi {{ $session->attendance_date->isToday() ? 'sudah ditutup.' : 'tanggal ini sudah selesai.' }} Kamu masih bisa mengoreksi status di tabel.
            </div>
        @endif

        <form method="POST" action="{{ route('guru.attendance.update', $session) }}" class="lg:col-span-2">
            @csrf
            <div class="t-card overflow-x-auto p-0">
                <table class="w-full">
                    <thead class="border-b border-slate-100"><tr><th class="t-th">Siswa</th><th class="t-th">Status</th><th class="t-th">Jam</th><th class="t-th">Ubah</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($students as $s)
                            @php $r = $records->get($s->id); @endphp
                            <tr data-student="{{ $s->id }}">
                                <td class="t-td"><p class="font-bold">{{ $s->name }}</p><p class="text-xs text-mute">{{ $s->nis }}</p></td>
                                <td class="t-td"><span data-badge class="t-badge {{ $r ? $r->tone() : 'bg-slate-100 text-slate-500' }}">{{ $r ? $r->label() : 'Belum' }}</span></td>
                                <td class="t-td text-mute" data-time>{{ $r?->check_in_at?->format('H:i') ?? '–' }}</td>
                                <td class="t-td">
                                    <select name="status[{{ $s->id }}]" data-select class="t-input !w-auto !py-1.5">
                                        <option value="">—</option>
                                        @foreach (\App\Models\Attendance::LABELS as $key => $label)
                                            <option value="{{ $key }}" @selected($r?->status === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="t-td text-center text-mute">Kelas ini belum punya siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($students->isNotEmpty())
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-mute"><span id="count-in" class="font-extrabold text-ink">0</span> dari {{ $students->count() }} siswa sudah masuk</p>
                    <button class="t-btn t-btn-primary">Simpan perubahan</button>
                </div>
            @endif
        </form>
    </div>
@endsection
