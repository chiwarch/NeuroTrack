@extends('layouts.panel')
@section('title', $announcement->exists ? 'Ubah Pengumuman' : 'Buat Pengumuman')

@section('content')
    @php
        $target = old('target_type', $announcement->target_type ?: ($isAdmin ? 'all' : 'class'));
        $picked = collect(old('classes', $chosen))->map(fn ($v) => (int) $v)->all();
        $publish = old('publish', $announcement->exists ? ($announcement->status() === 'draft' ? 'draft' : ($announcement->status() === 'scheduled' ? 'schedule' : 'now')) : 'now');
    @endphp
    <a href="{{ route($prefix.'.announcements.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Pengumuman</a>
    <h1 class="t-page-title mb-6">{{ $announcement->exists ? 'Ubah Pengumuman' : 'Buat Pengumuman' }}</h1>

    <form method="POST" action="{{ $announcement->exists ? route($prefix.'.announcements.update', $announcement) : route($prefix.'.announcements.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        @if ($announcement->exists) @method('PUT') @endif

        <div>
            <label for="title" class="t-label">Judul</label>
            <input id="title" name="title" value="{{ old('title', $announcement->title) }}" class="t-input" maxlength="200" required>
            @error('title') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="content" class="t-label">Isi pengumuman</label>
            <textarea id="content" name="content" rows="6" class="t-input" maxlength="5000" required>{{ old('content', $announcement->content) }}</textarea>
            @error('content') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="target_type" class="t-label">Ditujukan kepada</label>
            @if ($isAdmin)
                <select id="target_type" name="target_type" class="t-input" onchange="document.getElementById('class-box').hidden = this.value !== 'class'; document.getElementById('major-box').hidden = this.value !== 'major'">
                    @foreach (\App\Models\Announcement::TARGETS as $key => $label)
                        <option value="{{ $key }}" @selected($target === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="target_type" value="class">
                <p class="text-sm text-mute">Kelas yang kamu ajar atau walikan.</p>
            @endif
            @error('target_type') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div id="major-box" @if ($target !== 'major') hidden @endif>
            <span class="t-label">Pilih jurusan</span>
            @php $pickedMajors = collect(old('majors', $chosenMajors))->map(fn ($v) => (int) $v)->all(); @endphp
            <div class="grid max-h-56 gap-1 overflow-y-auto rounded-xl border border-slate-200 p-2 sm:grid-cols-2">
                @foreach ($majors as $m)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                        <input type="checkbox" name="majors[]" value="{{ $m->id }}" @checked(in_array($m->id, $pickedMajors, true)) class="size-4 accent-brand-500">
                        <span class="font-semibold">{{ $m->code }}</span><span class="truncate text-xs text-mute">{{ $m->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('majors') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <div id="class-box" @if ($target !== 'class') hidden @endif>
            <span class="t-label">Pilih kelas</span>
            <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2">
                @forelse ($classes as $c)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                        <input type="checkbox" name="classes[]" value="{{ $c->id }}" @checked(in_array($c->id, $picked, true)) class="size-4 accent-brand-500">
                        <span class="font-semibold">{{ $c->name }}</span><span class="text-xs text-mute">{{ $c->academic_year }}</span>
                    </label>
                @empty
                    <p class="p-2 text-sm text-mute">Belum ada kelas yang bisa dipilih.</p>
                @endforelse
            </div>
            @error('classes') <p class="t-error">{{ $message }}</p> @enderror
        </div>

        <fieldset>
            <legend class="t-label">Penerbitan</legend>
            <div class="space-y-2 text-sm">
                <label class="flex items-center gap-2"><input type="radio" name="publish" value="now" @checked($publish === 'now') class="accent-brand-500" onchange="document.getElementById('publish-at').hidden = true"> {{ $announcement->exists && $announcement->status() === 'published' ? 'Tetap terbit' : 'Terbitkan sekarang' }}</label>
                <label class="flex items-center gap-2"><input type="radio" name="publish" value="schedule" @checked($publish === 'schedule') class="accent-brand-500" onchange="document.getElementById('publish-at').hidden = false"> Jadwalkan</label>
                <label class="flex items-center gap-2"><input type="radio" name="publish" value="draft" @checked($publish === 'draft') class="accent-brand-500" onchange="document.getElementById('publish-at').hidden = true"> Simpan sebagai draf (belum terlihat siapa pun)</label>
            </div>
            <div id="publish-at" class="mt-3" @if ($publish !== 'schedule') hidden @endif>
                <input type="datetime-local" name="publish_at" value="{{ old('publish_at', $announcement->status() === 'scheduled' ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}" class="t-input !w-auto" aria-label="Waktu terbit">
                @error('publish_at') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            @error('publish') <p class="t-error">{{ $message }}</p> @enderror
        </fieldset>

        <button class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
