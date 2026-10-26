@extends('layouts.panel')
@section('title', $assignment->exists ? 'Ubah Tugas' : 'Buat Tugas')

@section('content')
    <a href="{{ $assignment->exists ? route('guru.assignments.show', $assignment) : route('guru.assignments.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Kembali</a>
    <h1 class="t-page-title mb-6">{{ $assignment->exists ? 'Ubah Tugas' : 'Buat Tugas' }}</h1>

    <form method="POST" action="{{ $assignment->exists ? route('guru.assignments.update', $assignment) : route('guru.assignments.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        @if ($assignment->exists) @method('PUT') @endif

        @if ($assignment->exists)
            <p class="text-sm text-mute">{{ $assignment->classSubject->schoolClass->name }} · {{ $assignment->classSubject->subject->name }}</p>
        @else
            <div>
                <label for="class_subject_id" class="t-label">Kelas & mata pelajaran</label>
                <select id="class_subject_id" name="class_subject_id" class="t-input" required>
                    <option value="">— Pilih —</option>
                    @foreach ($classSubjects as $cs)
                        <option value="{{ $cs->id }}" @selected(old('class_subject_id') == $cs->id)>{{ $cs->label() }}</option>
                    @endforeach
                </select>
                @error('class_subject_id') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label for="title" class="t-label">Judul</label>
            <input id="title" name="title" value="{{ old('title', $assignment->title) }}" class="t-input" required>
            @error('title') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="description" class="t-label">Petunjuk (opsional)</label>
            <textarea id="description" name="description" rows="5" class="t-input">{{ old('description', $assignment->description) }}</textarea>
            @error('description') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="deadline" class="t-label">Tenggat (opsional)</label>
                <input id="deadline" type="datetime-local" name="deadline" value="{{ old('deadline', $assignment->deadline?->format('Y-m-d\TH:i')) }}" class="t-input">
                @error('deadline') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="max_score" class="t-label">Nilai maksimal</label>
                <input id="max_score" type="number" name="max_score" min="1" max="100" step="0.5" value="{{ old('max_score', $assignment->max_score ?? 100) }}" class="t-input" required>
                @error('max_score') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>
        <button class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
