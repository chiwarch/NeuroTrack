@extends('layouts.panel')
@section('title', 'Tugas')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Tugas</h1>
        <a href="{{ route('guru.assignments.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Buat Tugas</a>
    </div>

    @if ($assignments->isEmpty())
        <div class="t-card text-center text-sm text-mute">Belum ada tugas. Pastikan admin sudah menugaskanmu pada mapel di sebuah kelas, lalu klik “Buat Tugas”.</div>
    @else
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Tugas</th><th class="t-th">Tenggat</th><th class="t-th">Terkumpul</th><th class="t-th"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($assignments as $a)
                        <tr>
                            <td class="t-td font-bold">{{ $a->title }}<span class="block text-xs font-medium text-mute">{{ $a->classSubject->schoolClass->name }} · {{ $a->classSubject->subject->name }}</span></td>
                            <td class="t-td">{{ $a->deadline ? $a->deadline->translatedFormat('d M Y, H:i') : 'Tanpa tenggat' }}</td>
                            <td class="t-td">{{ $a->submissions_count }}</td>
                            <td class="t-td text-right"><a href="{{ route('guru.assignments.show', $a) }}" class="t-btn t-btn-soft t-btn-sm">Nilai <x-icon name="arrow-right" :size="14" /></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $assignments->links() }}</div>
    @endif
@endsection
