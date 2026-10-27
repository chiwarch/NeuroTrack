@extends('layouts.panel')
@section('title', 'Lencana')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="t-page-title">Lencana</h1>
            <p class="mt-1 text-sm text-mute">Katalog lencana dan syarat mendapatkannya.</p>
        </div>
        <a href="{{ route('admin.badges.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Tambah Lencana</a>
    </div>
    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Lencana</th><th class="t-th">Cara mendapatkan</th><th class="t-th">Bonus XP</th><th class="t-th">Dimiliki</th><th class="t-th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($badges as $b)
                    <tr>
                        <td class="t-td"><span class="inline-flex items-center gap-3"><x-badge-icon :badge="$b" :size="40" /><span><span class="block font-bold">{{ $b->name }}</span><span class="block text-xs text-mute">{{ $b->description }}</span></span></span></td>
                        <td class="t-td text-sm">{{ \App\Models\Badge::TYPES[$b->type] ?? $b->type }}@if ($b->type === 'xp') <span class="block text-xs text-mute">{{ $b->required_xp }} XP</span>@endif</td>
                        <td class="t-td">{{ $b->xp_reward ? '+'.$b->xp_reward : '–' }}</td>
                        <td class="t-td">{{ $b->user_badges_count }} siswa</td>
                        <td class="t-td">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.badges.edit', $b) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                @if ($b->type !== 'auto')
                                    <form method="POST" action="{{ route('admin.badges.destroy', $b) }}" onsubmit="return confirm('Hapus lencana ini? Lencana milik siswa ikut terhapus.')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
