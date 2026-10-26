@extends('layouts.panel')
@section('title', $subject->exists ? 'Ubah Mapel' : 'Tambah Mapel')

@section('content')
    @php $selectedTeachers = collect(old('teachers', $subject->teachers->pluck('id')->all()))->map(fn ($v) => (int) $v)->all(); @endphp

    <a href="{{ route('admin.subjects.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Mata Pelajaran</a>
    <h1 class="t-page-title mb-6">{{ $subject->exists ? 'Ubah Mata Pelajaran' : 'Tambah Mata Pelajaran' }}</h1>

    <form method="POST" action="{{ $subject->exists ? route('admin.subjects.update', $subject) : route('admin.subjects.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        @if ($subject->exists) @method('PUT') @endif

        <div>
            <label for="name" class="t-label">Nama mapel</label>
            <input id="name" name="name" value="{{ old('name', $subject->name) }}" class="t-input" required>
            @error('name') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="type" class="t-label">Jenis</label>
                <select id="type" name="type" class="t-input">
                    <option value="umum" @selected(old('type', $subject->type) === 'umum')>Umum (semua jurusan)</option>
                    <option value="kejuruan" @selected(old('type', $subject->type) === 'kejuruan')>Kejuruan (satu jurusan)</option>
                </select>
                @error('type') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="major_id" class="t-label">Jurusan (untuk mapel kejuruan)</label>
                <select id="major_id" name="major_id" class="t-input">
                    <option value="">—</option>
                    @foreach ($majors as $m)
                        <option value="{{ $m->id }}" @selected((int) old('major_id', $subject->major_id) === $m->id)>{{ $m->code }} — {{ $m->name }}</option>
                    @endforeach
                </select>
                @error('major_id') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <p class="t-label">Ikon</p>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                @foreach (\App\Models\Subject::ICONS as $key => $label)
                    <label class="flex cursor-pointer flex-col items-center gap-1 rounded-xl border border-slate-200 p-2 text-center text-xs font-semibold text-mute has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                        <input type="radio" name="icon" value="{{ $key }}" class="sr-only" @checked(old('icon', $subject->icon) === $key)>
                        <img src="{{ asset('images/'.$key.'.webp') }}" alt="" class="size-12 object-contain" width="48" height="48" loading="lazy">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('icon') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <p class="t-label">Guru pengampu</p>
            @if ($teachers->isEmpty())
                <p class="t-hint">Belum ada akun guru. Tambahkan di menu Pengguna.</p>
            @else
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($teachers as $t)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold">
                            <input type="checkbox" name="teachers[]" value="{{ $t->id }}" class="size-4 accent-brand-500" @checked(in_array($t->id, $selectedTeachers, true))> {{ $t->name }}
                        </label>
                    @endforeach
                </div>
            @endif
            @error('teachers.*') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
