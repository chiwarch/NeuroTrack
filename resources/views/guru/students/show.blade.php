@extends('layouts.panel')
@section('title', $student->name)

@section('content')
    <a href="{{ route('guru.students.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Penilaian Siswa</a>
    <div class="mb-6">
        <h1 class="t-page-title">{{ $student->name }}</h1>
        <p class="mt-1 text-sm text-mute">{{ $student->nis }} · <span data-testid="student-class">{{ $student->classes->first()?->name ?? 'Belum punya kelas' }}</span> · {{ $student->major?->code }}</p>
    </div>

    @include('partials.stats')

    <form method="POST" action="{{ route('guru.students.rate', $student) }}" class="t-card mt-6 space-y-5">
        @csrf
        <div>
            <h2 class="text-lg font-extrabold">Nilai soft skill</h2>
            <p class="text-xs text-mute">Skala 1 (perlu bimbingan) sampai 5 (sangat baik). Disimpan per bulan; menyimpan lagi di bulan ini memperbarui nilaimu. Nilai akhir siswa = rata-rata semua guru.</p>
        </div>
        <style>
            /* CSS biasa (bukan utilitas Tailwind) agar tetap jalan walau aset belum di-build ulang */
            .score-pill{position:relative;display:grid;place-items:center;width:2.75rem;height:2.75rem;border:1px solid #e2e8f0;border-radius:.75rem;background:#fff;font-size:.875rem;font-weight:800;cursor:pointer;transition:background .15s,border-color .15s,color .15s}
            .score-pill:hover{background:#f8fafc}
            .score-pill input{position:absolute;inset:0;width:100%;height:100%;margin:0;opacity:0;cursor:pointer}
            .score-pill:has(input:checked){background:var(--color-brand-500,#6d3df0);border-color:var(--color-brand-500,#6d3df0);color:#fff}
            .score-pill:has(input:focus-visible){outline:3px solid #c9b8ff;outline-offset:2px}
        </style>
        @error('scores.*') <p class="t-error">{{ $message }}</p> @enderror
        @foreach ($skills as $skill)
            <fieldset>
                <legend class="text-sm font-bold">{{ $skill->name }}</legend>
                @if ($skill->description)<p class="text-xs text-mute">{{ $skill->description }}</p>@endif
                <div class="mt-2 flex gap-2">
                    @foreach (range(1, 5) as $n)
                        <label class="score-pill">
                            <input type="radio" name="scores[{{ $skill->id }}]" value="{{ $n }}" @checked((int) old("scores.{$skill->id}", $mine[$skill->id] ?? 0) === $n)>
                            <span>{{ $n }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endforeach
        <div>
            <label for="notes" class="t-label">Catatan (opsional)</label>
            <textarea id="notes" name="notes" rows="3" class="t-input" maxlength="1000">{{ old('notes', $notes) }}</textarea>
        </div>
        <button data-testid="soft-save" class="t-btn t-btn-primary">Simpan penilaian</button>
    </form>
@endsection
