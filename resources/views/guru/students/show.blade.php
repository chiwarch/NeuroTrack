@extends('layouts.panel')
@section('title', $student->name)

@section('content')
    <a href="{{ route('guru.students.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Penilaian Siswa</a>
    <div class="mb-6">
        <h1 class="t-page-title">{{ $student->name }}</h1>
        <p class="mt-1 text-sm text-mute">{{ $student->nis }} · {{ $student->classes->first()?->name ?? 'Belum punya kelas' }} · {{ $student->major?->code }}</p>
    </div>

    @include('partials.stats')

    <form method="POST" action="{{ route('guru.students.rate', $student) }}" class="t-card mt-6 space-y-5">
        @csrf
        <div>
            <h2 class="text-lg font-extrabold">Nilai soft skill</h2>
            <p class="text-xs text-mute">Skala 1 (perlu bimbingan) sampai 5 (sangat baik). Disimpan per bulan; menyimpan lagi di bulan ini memperbarui nilaimu. Nilai akhir siswa = rata-rata semua guru.</p>
        </div>
        @error('scores.*') <p class="t-error">{{ $message }}</p> @enderror
        @foreach ($skills as $skill)
            <fieldset>
                <legend class="text-sm font-bold">{{ $skill->name }}</legend>
                @if ($skill->description)<p class="text-xs text-mute">{{ $skill->description }}</p>@endif
                <div class="mt-2 flex gap-2">
                    @foreach (range(1, 5) as $n)
                        <label class="cursor-pointer">
                            <input type="radio" name="scores[{{ $skill->id }}]" value="{{ $n }}" class="peer sr-only" @checked((int) old("scores.{$skill->id}", $mine[$skill->id] ?? 0) === $n)>
                            <span class="grid size-11 place-items-center rounded-xl border border-slate-200 text-sm font-extrabold transition peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-200 hover:bg-slate-50">{{ $n }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endforeach
        <div>
            <label for="notes" class="t-label">Catatan (opsional)</label>
            <textarea id="notes" name="notes" rows="3" class="t-input" maxlength="1000">{{ old('notes', $notes) }}</textarea>
        </div>
        <button class="t-btn t-btn-primary">Simpan penilaian</button>
    </form>
@endsection
