@extends('layouts.panel')
@section('title', 'Mata Pelajaran')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Mata Pelajaran</h1>
        <a href="{{ route('admin.subjects.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Tambah Mapel</a>
    </div>
    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Mapel</th><th class="t-th">Jenis</th><th class="t-th">Guru</th><th class="t-th">Kuis</th><th class="t-th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($subjects as $s)
                    <tr>
                        <td class="t-td"><div class="flex items-center gap-3"><img src="{{ $s->iconUrl() }}" alt="" class="size-10 object-contain" width="40" height="40" loading="lazy"><span class="font-bold">{{ $s->name }}</span></div></td>
                        <td class="t-td"><span class="t-badge {{ $s->type === 'kejuruan' ? 'bg-orange-100 text-orange-700' : 'bg-brand-50 text-brand-600' }}">{{ $s->typeLabel() }}{{ $s->major ? ' · '.$s->major->code : '' }}</span></td>
                        <td class="t-td">{{ $s->teachers_count ?: '–' }}</td>
                        <td class="t-td">{{ $s->quizzes_count }}</td>
                        <td class="t-td">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.subjects.edit', $s) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $s) }}" onsubmit="return confirm('Hapus mapel ini?')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="t-td text-center text-mute">Belum ada mata pelajaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
