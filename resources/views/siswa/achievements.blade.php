@extends('layouts.panel')
@section('title', 'Prestasi')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Prestasimu</h1>
        <p class="mt-1 text-sm text-mute">Kumpulkan XP dengan hadir tepat waktu dan mengerjakan kuis. Raih lencana untuk setiap pencapaian.</p>
    </div>

    @include('gamification._level-card')

    <section class="mt-8">
        <h2 class="mb-3 text-lg font-extrabold">Lencana <span class="text-sm font-semibold text-mute">{{ $earned->count() }} dari {{ $badges->count() }}</span></h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($badges as $b)
                @php $own = $earned->get($b->id); @endphp
                <div class="t-card flex items-start gap-4 {{ $own ? '' : 'opacity-70' }}">
                    <x-badge-icon :badge="$b" :earned="(bool) $own" />
                    <div class="min-w-0">
                        <p class="font-extrabold leading-tight">{{ $b->name }}</p>
                        <p class="mt-0.5 text-xs text-mute">{{ $b->description }}</p>
                        <p class="mt-1.5 text-xs font-semibold {{ $own ? 'text-emerald-600' : 'text-slate-500' }}">
                            @if ($own) Didapat {{ $own->earned_at->translatedFormat('d M Y') }}@if ($own->note) · {{ $own->note }}@endif
                            @else {{ $b->hint() }}@endif
                            @if ($b->xp_reward) · +{{ $b->xp_reward }} XP @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-8">
        <h2 class="mb-3 text-lg font-extrabold">Riwayat XP</h2>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Kegiatan</th><th class="t-th">Tanggal</th><th class="t-th text-right">XP</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($history as $h)
                        <tr>
                            <td class="t-td font-semibold">{{ $h->description ?: $h->source }}</td>
                            <td class="t-td text-mute">{{ $h->created_at->translatedFormat('d M Y') }}</td>
                            <td class="t-td text-right font-extrabold text-emerald-600">+{{ $h->amount }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="t-td text-center text-mute">Belum ada XP. Hadir dan kerjakan kuis untuk memulai!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
