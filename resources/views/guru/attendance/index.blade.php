@extends('layouts.panel')
@section('title', 'Absensi')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Absensi</h1>
        <p class="mt-1 text-sm text-mute">Buka sesi harian, tampilkan QR di layar kelas, siswa memindainya dari HP masing-masing.</p>
    </div>

    @if ($classes->isEmpty())
        <div class="t-card text-sm text-mute">Kamu belum menjadi wali kelas mana pun. Hubungi admin.</div>
    @else
        <div class="space-y-3">
            @foreach ($classes as $c)
                @php $s = $sessions->get($c->id); @endphp
                <div class="t-card flex flex-wrap items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="font-extrabold">{{ $c->name }}</p>
                        <p class="text-xs text-mute">{{ $c->students_count }} siswa · {{ $c->academic_year }}</p>
                    </div>
                    @if ($s)
                        <span class="t-badge {{ $s->isOpen() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $s->isOpen() ? 'Sesi terbuka' : 'Sesi ditutup' }}</span>
                        <a href="{{ route('guru.attendance.show', $s) }}" class="t-btn t-btn-primary">Buka <x-icon name="arrow-right" :size="16" /></a>
                    @else
                        <form method="POST" action="{{ route('guru.attendance.open', $c) }}">@csrf<button class="t-btn t-btn-primary">Mulai absensi hari ini</button></form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
