@extends('layouts.panel')
@section('title', 'Pengumuman')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Pengumuman</h1>
        <a href="{{ route($prefix.'.announcements.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Buat Pengumuman</a>
    </div>

    @if ($forMe->isNotEmpty())
        <section class="mb-8">
            <h2 class="mb-3 text-lg font-extrabold">Untukmu</h2>
            <div class="space-y-3">@foreach ($forMe as $a) @include('announcements._card', ['a' => $a]) @endforeach</div>
        </section>
    @endif

    <section>
        <h2 class="mb-3 text-lg font-extrabold">{{ $prefix === 'admin' ? 'Semua pengumuman' : 'Buatanmu' }}</h2>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Judul</th><th class="t-th">Sasaran</th><th class="t-th">Status</th><th class="t-th"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($announcements as $a)
                        @php
                            [$label, $tone] = match ($a->status()) {
                                'published' => ['Terbit', 'bg-emerald-100 text-emerald-700'],
                                'scheduled' => ['Terjadwal '.$a->published_at->translatedFormat('d M, H:i'), 'bg-sky-100 text-sky-700'],
                                default => ['Draf', 'bg-slate-100 text-slate-600'],
                            };
                        @endphp
                        <tr>
                            <td class="t-td font-bold">{{ $a->title }}@if ($prefix === 'admin')<span class="block text-xs font-medium text-mute">oleh {{ $a->creator?->name }}</span>@endif</td>
                            <td class="t-td">{{ $a->targetLabel() }}</td>
                            <td class="t-td"><span class="t-badge {{ $tone }}">{{ $label }}</span></td>
                            <td class="t-td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route($prefix.'.announcements.edit', $a) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                    <form method="POST" action="{{ route($prefix.'.announcements.destroy', $a) }}" onsubmit="return confirm('Hapus pengumuman ini?')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="t-td text-center text-mute">Belum ada pengumuman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $announcements->links() }}</div>
    </section>
@endsection
