@extends('layouts.panel')
@section('title', 'Kuis Saya')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Kuis Saya</h1>
        <a href="{{ route('guru.quizzes.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Buat Kuis</a>
    </div>

    @if ($quizzes->isEmpty())
        <div class="t-card text-center text-sm text-mute">Belum ada kuis. Klik “Buat Kuis” untuk memulai.</div>
    @else
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Kuis</th><th class="t-th">Soal</th><th class="t-th">Dikerjakan</th><th class="t-th">Status</th><th class="t-th"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($quizzes as $q)
                        <tr>
                            <td class="t-td font-bold">{{ $q->title }}<span class="block text-xs font-medium text-mute">{{ $q->subject->name }}</span></td>
                            <td class="t-td">{{ $q->questions_count }}</td>
                            <td class="t-td">{{ $q->attempts_count }}×</td>
                            <td class="t-td"><span class="t-badge {{ $q->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $q->is_published ? 'Terbit' : 'Draf' }}</span></td>
                            <td class="t-td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('guru.quizzes.results', $q) }}" class="t-btn t-btn-line t-btn-sm">Hasil</a>
                                    <a href="{{ route('guru.quizzes.edit', $q) }}" class="t-btn t-btn-soft t-btn-sm"><x-icon name="edit" :size="14" /> Kelola</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $quizzes->links() }}</div>
    @endif
@endsection
