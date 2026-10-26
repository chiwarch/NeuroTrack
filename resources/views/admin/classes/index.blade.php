@extends('layouts.panel')
@section('title', 'Kelas')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Kelas</h1>
        <a href="{{ route('admin.classes.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Tambah Kelas</a>
    </div>
    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Kelas</th><th class="t-th">Tahun ajaran</th><th class="t-th">Wali kelas</th><th class="t-th">Siswa</th><th class="t-th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($classes as $c)
                    <tr>
                        <td class="t-td font-bold">{{ $c->name }}</td>
                        <td class="t-td">{{ $c->academic_year }}</td>
                        <td class="t-td">{{ $c->homeroomTeacher?->name ?? '–' }}</td>
                        <td class="t-td">{{ $c->students_count }}</td>
                        <td class="t-td">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.classes.edit', $c) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $c) }}" onsubmit="return confirm('Hapus kelas ini?')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="t-td text-center text-mute">Belum ada kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
