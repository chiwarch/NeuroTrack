@extends('layouts.panel')
@section('title', $class->exists ? 'Ubah Kelas' : 'Tambah Kelas')

@section('content')
    <a href="{{ route('admin.classes.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Kelas</a>
    <h1 class="t-page-title mb-6">{{ $class->exists ? 'Ubah Kelas' : 'Tambah Kelas' }}</h1>

    @php $chosen = collect(old('students', $selected))->map(fn ($v) => (int) $v)->all(); @endphp
    <form method="POST" action="{{ $class->exists ? route('admin.classes.update', $class) : route('admin.classes.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        @if ($class->exists) @method('PUT') @endif
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="t-label">Nama kelas</label>
                <input id="name" name="name" value="{{ old('name', $class->name) }}" class="t-input" placeholder="mis. X RPL 1" required>
                @error('name') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="academic_year" class="t-label">Tahun ajaran</label>
                <input id="academic_year" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}" class="t-input" placeholder="2026/2027" required>
                @error('academic_year') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label for="homeroom_teacher_id" class="t-label">Wali kelas</label>
            <select id="homeroom_teacher_id" name="homeroom_teacher_id" class="t-input">
                <option value="">— Belum ditentukan —</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $t->id)>{{ $t->name }}</option>
                @endforeach
            </select>
            <p class="t-hint">Wali kelas yang membuka sesi absensi dan menampilkan QR.</p>
            @error('homeroom_teacher_id') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <span class="t-label">Siswa di kelas ini</span>
            <div class="max-h-72 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2">
                @forelse ($students as $s)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                        <input type="checkbox" name="students[]" value="{{ $s->id }}" @checked(in_array($s->id, $chosen, true)) class="size-4 accent-brand-500">
                        <span class="font-semibold">{{ $s->name }}</span>
                        <span class="text-xs text-mute">{{ $s->nis }} · {{ $s->major?->code }} · kelas {{ $s->grade_level }}</span>
                    </label>
                @empty
                    <p class="p-2 text-sm text-mute">Belum ada akun siswa.</p>
                @endforelse
            </div>
            <p class="t-hint">Siswa hanya bisa ada di satu kelas aktif; memilihnya di sini memindahkannya dari kelas lamanya.</p>
        </div>
        <div>
            <span class="t-label">Mata pelajaran & guru pengampu</span>
            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200">
                @foreach ($subjects as $sub)
                    <div class="flex flex-wrap items-center gap-3 px-3 py-2">
                        <label for="teacher-{{ $sub->id }}" class="min-w-0 flex-1 text-sm font-semibold">{{ $sub->name }}
                            <span class="block text-xs font-medium text-mute">{{ $sub->type === 'kejuruan' ? 'Kejuruan '.($sub->major?->code) : 'Umum' }}</span>
                        </label>
                        <select id="teacher-{{ $sub->id }}" name="teachers[{{ $sub->id }}]" class="t-input !w-auto min-w-44 !py-1.5">
                            <option value="">— Tidak diajarkan —</option>
                            @foreach ($teachers as $t)
                                <option value="{{ $t->id }}" @selected((int) old("teachers.{$sub->id}", $assigned[$sub->id] ?? 0) === $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
            <p class="t-hint">Guru hanya bisa membuat tugas dan menilai soft skill siswa di kelas yang mapelnya ia ampu.</p>
        </div>
        <button type="submit" class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
