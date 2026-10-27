@extends('layouts.panel')
@section('title', 'Beranda')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Halo, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-mute">Pantau kehadiran, kemampuan, dan tugas anakmu.</p>
    </div>

    @if ($children->isEmpty())
        <div class="t-card text-center text-sm text-mute">Akunmu belum dihubungkan dengan data anak. Hubungi admin sekolah.</div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($children as $child)
                <a href="{{ route('orang_tua.children.show', $child) }}" class="t-card flex items-center gap-5 transition hover:-translate-y-0.5">
                    <div class="ring" style="--p: {{ $child->attendance['rate'] }}"><span>{{ $child->attendance['rate'] + 0 }}%</span></div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-extrabold">{{ $child->name }}</p>
                        <p class="text-xs text-mute">{{ $child->classes->first()?->name ?? 'Belum punya kelas' }} · {{ $child->major?->code }}{{ $child->pivot->relationship ? ' · '.$child->pivot->relationship : '' }}</p>
                        <p class="mt-1 text-xs font-semibold text-brand-600">Rate kehadiran · lihat perkembangan →</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @include('announcements._latest')
@endsection
