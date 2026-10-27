@extends('layouts.panel')
@section('title', 'Beranda')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}! 👋</h1>
        <p class="mt-1 text-sm text-mute">Ayo lanjutkan belajarmu hari ini.</p>
    </div>

    <div class="mb-4">@include('gamification._level-card')</div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="t-card flex items-center gap-5">
            <div class="ring" style="--p: {{ $progress }}"><span>{{ $progress }}%</span></div>
            <div>
                <p class="font-extrabold">Progres Keseluruhan</p>
                <p class="mt-1 text-sm text-mute">{{ $completed }} dari {{ $available }} kuis sudah kamu selesaikan.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:col-span-2 lg:grid-cols-4">
            @foreach ([
                ['check-circle', 'bg-emerald-100 text-emerald-600', $passed, 'Kuis Lulus'],
                ['bar-chart', 'bg-brand-100 text-brand-600', $average !== null ? $average + 0 : '–', 'Rata-rata Nilai'],
                ['clock', 'bg-sky-100 text-sky-600', $hours, 'Jam Belajar'],
                ['flame', 'bg-orange-100 text-orange-600', $streak, 'Hari Berturut-turut'],
            ] as [$icon, $tone, $value, $label])
                <div class="t-card flex flex-col items-start gap-2 p-4">
                    <span class="grid size-9 place-items-center rounded-xl {{ $tone }}"><x-icon :name="$icon" :size="18" /></span>
                    <p class="text-2xl font-extrabold leading-none">{{ $value }}</p>
                    <p class="text-xs font-semibold text-mute">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @if ($running->isNotEmpty())
        <section class="mt-8">
            <h2 class="mb-3 text-lg font-extrabold">Sedang Dikerjakan</h2>
            <div class="space-y-3">
                @foreach ($running as $a)
                    <div class="t-card flex flex-wrap items-center gap-4 !border-amber-200 bg-amber-50">
                        <img src="{{ $a->quiz->subject->iconUrl() }}" alt="" class="size-12 object-contain" width="48" height="48">
                        <div class="min-w-0 flex-1">
                            <p class="font-extrabold">{{ $a->quiz->title }}</p>
                            <p class="text-xs text-mute">Batas waktu: {{ $a->deadline()->translatedFormat('H:i') }}</p>
                        </div>
                        <a href="{{ route('siswa.attempts.show', $a) }}" class="t-btn t-btn-primary">Lanjutkan <x-icon name="arrow-right" :size="16" /></a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-extrabold">Kuis Terbaru</h2>
            <a href="{{ route('siswa.quizzes.index') }}" class="t-btn t-btn-soft t-btn-sm">Lihat Semua <x-icon name="arrow-right" :size="14" /></a>
        </div>
        @if ($latest->isEmpty())
            <div class="t-card text-center text-sm text-mute">Belum ada kuis untukmu. Tunggu guru menerbitkannya ya.</div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($latest as $q) @include('siswa._quiz-card', ['quiz' => $q]) @endforeach
            </div>
        @endif
    </section>

    @if ($recent->isNotEmpty())
        <section class="mt-8">
            <h2 class="mb-3 text-lg font-extrabold">Aktivitas Terakhir</h2>
            <div class="t-card overflow-x-auto p-0">
                <table class="w-full">
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recent as $a)
                            <tr>
                                <td class="t-td font-bold">{{ $a->quiz->title }}<span class="block text-xs font-medium text-mute">{{ $a->quiz->subject->name }}</span></td>
                                <td class="t-td text-mute">{{ $a->finished_at->translatedFormat('d M Y, H:i') }}</td>
                                <td class="t-td text-right"><span class="t-badge {{ $a->passed() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">Nilai {{ $a->score + 0 }}</span></td>
                                <td class="t-td text-right"><a href="{{ route('siswa.attempts.result', $a) }}" class="text-sm font-bold text-brand-600 hover:underline">Lihat</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
    @include('announcements._latest')
@endsection
