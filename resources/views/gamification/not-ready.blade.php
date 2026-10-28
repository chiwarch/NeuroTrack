@extends('layouts.panel')
@section('title', 'Fitur belum aktif')

@section('content')
    <div class="mx-auto max-w-xl pt-6">
        <div class="t-card space-y-3 p-8">
            <h1 class="text-xl font-extrabold">Fitur gamifikasi belum aktif</h1>
            <p class="text-sm text-mute">Tabel XP, level, dan lencana belum dibuat di database. @if (auth()->user()->role === 'admin') Jalankan perintah ini di folder project, lalu muat ulang halaman: @else Hubungi admin sekolah. @endif</p>
            @if (auth()->user()->role === 'admin')
                <pre class="overflow-x-auto rounded-xl bg-slate-900 p-4 text-xs leading-relaxed text-slate-100">php artisan migrate
php artisan db:seed --class=GamificationSeeder
php artisan xp:backfill</pre>
            @endif
        </div>
    </div>
@endsection
