@extends('layouts.panel')
@section('title', 'Log Aktivitas')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="t-page-title">Log Aktivitas</h1>
            <p class="mt-1 text-sm text-mute">Jejak masuk/keluar, penggantian kata sandi, pengelolaan pengguna, dan penilaian.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ $q }}" class="t-input !w-56" placeholder="Cari kejadian, nama, atau IP" aria-label="Cari log">
            <button class="t-btn t-btn-line">Cari</button>
        </form>
    </div>
    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Waktu</th><th class="t-th">Pengguna</th><th class="t-th">Kejadian</th><th class="t-th">Rincian</th><th class="t-th">IP</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $l)
                    <tr data-testid="audit-row">
                        <td class="t-td whitespace-nowrap text-mute">{{ $l->created_at->translatedFormat('d M Y, H:i:s') }}</td>
                        <td class="t-td font-semibold">{{ $l->user?->name ?? '–' }}</td>
                        <td class="t-td"><span class="t-badge {{ str_contains($l->event, 'failed') || str_contains($l->event, 'locked') ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">{{ $l->event }}</span></td>
                        <td class="t-td text-sm">{{ $l->description }}</td>
                        <td class="t-td text-xs text-mute">{{ $l->ip }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="t-td text-center text-mute">Belum ada catatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
