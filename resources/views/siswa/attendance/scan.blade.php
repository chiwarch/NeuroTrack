@extends('layouts.panel')
@section('title', 'Konfirmasi Absen')

@section('content')
    <div class="mx-auto max-w-md pt-6 text-center">
        <div class="t-card space-y-4 p-8">
            @if ($state === 'ready')
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-brand-100 text-brand-600"><x-icon name="check-circle" :size="28" /></span>
                <h1 class="text-xl font-extrabold">Absen kelas {{ $session->schoolClass->name }}</h1>
                <p class="text-sm text-mute">{{ $session->attendance_date->translatedFormat('l, d F Y') }} · sekarang {{ now()->format('H:i') }}</p>
                <form method="POST" action="{{ route('siswa.attendance.checkin') }}">@csrf<button class="t-btn t-btn-primary w-full">Saya hadir</button></form>
            @elseif ($state === 'done')
                <h1 class="text-xl font-extrabold">Kamu sudah tercatat hari ini</h1>
                <a href="{{ route('siswa.attendance.index') }}" class="t-btn t-btn-soft">Lihat absensiku</a>
            @elseif ($state === 'closed')
                <h1 class="text-xl font-extrabold">Absensi sudah ditutup</h1>
                <p class="text-sm text-mute">Hubungi wali kelasmu jika ini keliru.</p>
            @elseif ($state === 'not_member')
                <h1 class="text-xl font-extrabold">Kamu bukan siswa kelas ini</h1>
                <p class="text-sm text-mute">Pastikan kamu masuk dengan akunmu sendiri.</p>
            @else
                <h1 class="text-xl font-extrabold">Kode QR kedaluwarsa</h1>
                <p class="text-sm text-mute">Kode berganti tiap 20 detik. Pindai lagi QR yang tampil di layar kelas.</p>
            @endif
        </div>
    </div>
@endsection
