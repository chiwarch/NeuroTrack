@extends('layouts.panel')
@section('title', 'Penilaian Siswa')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="t-page-title">Penilaian Siswa</h1>
            <p class="mt-1 text-sm text-mute">Lihat statistik siswa dan nilai soft skill mereka.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" class="t-input !w-56" placeholder="Cari nama atau NIS" aria-label="Cari siswa">
            <button class="t-btn t-btn-line">Cari</button>
        </form>
    </div>

    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Siswa</th><th class="t-th">Kelas</th><th class="t-th">Jurusan</th><th class="t-th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $s)
                    <tr>
                        <td class="t-td"><p class="font-bold">{{ $s->name }}</p><p class="text-xs text-mute">{{ $s->nis }}</p></td>
                        <td class="t-td">{{ $s->classes->first()?->name ?? '–' }}</td>
                        <td class="t-td">{{ $s->major?->code ?? '–' }}</td>
                        <td class="t-td text-right"><a href="{{ route('guru.students.show', $s) }}" class="t-btn t-btn-soft t-btn-sm">Lihat & nilai <x-icon name="arrow-right" :size="14" /></a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="t-td text-center text-mute">Tidak ada siswa yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $students->links() }}</div>
@endsection
