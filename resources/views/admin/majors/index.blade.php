@extends('layouts.panel')
@section('title', 'Jurusan')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Jurusan</h1>
        <a href="{{ route('admin.majors.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Tambah Jurusan</a>
    </div>
    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Kode</th><th class="t-th">Nama</th><th class="t-th">Siswa</th><th class="t-th">Mapel</th><th class="t-th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($majors as $m)
                    <tr>
                        <td class="t-td"><span class="t-badge bg-brand-50 text-brand-600">{{ $m->code }}</span></td>
                        <td class="t-td font-bold">{{ $m->name }}</td>
                        <td class="t-td">{{ $m->students_count }}</td>
                        <td class="t-td">{{ $m->subjects_count }}</td>
                        <td class="t-td">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.majors.edit', $m) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                <form method="POST" action="{{ route('admin.majors.destroy', $m) }}" onsubmit="return confirm('Hapus jurusan ini?')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="t-td text-center text-mute">Belum ada jurusan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
