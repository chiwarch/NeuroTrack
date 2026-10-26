@extends('layouts.panel')
@section('title', 'Katalog Kuis')

@section('content')
    <h1 class="t-page-title">Katalog Kuis</h1>
    <p class="mt-1 mb-6 text-sm text-mute">Kuis mata pelajaran umum dan jurusanmu.</p>

    <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul kuis…" class="t-input sm:max-w-xs">
        @if (request('mapel')) <input type="hidden" name="mapel" value="{{ request('mapel') }}"> @endif
        <button class="t-btn t-btn-line" type="submit"><x-icon name="search" :size="16" /> Cari</button>
    </form>

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('siswa.quizzes.index', array_filter(['q' => request('q')])) }}" class="t-badge px-4 py-1.5 {{ request('mapel') ? 'bg-white text-mute ring-1 ring-slate-200' : 'bg-brand-500 text-white' }}">Semua</a>
        @foreach ($subjects as $s)
            <a href="{{ route('siswa.quizzes.index', array_filter(['mapel' => $s->id, 'q' => request('q')])) }}"
               class="t-badge px-4 py-1.5 {{ (int) request('mapel') === $s->id ? 'bg-brand-500 text-white' : 'bg-white text-mute ring-1 ring-slate-200 hover:bg-brand-50' }}">{{ $s->name }}</a>
        @endforeach
    </div>

    @if ($quizzes->isEmpty())
        <div class="t-card text-center text-sm text-mute">Tidak ada kuis yang cocok.</div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($quizzes as $q) @include('siswa._quiz-card', ['quiz' => $q, 'best' => $best[$q->id] ?? null]) @endforeach
        </div>
        <div class="mt-6">{{ $quizzes->links() }}</div>
    @endif
@endsection
