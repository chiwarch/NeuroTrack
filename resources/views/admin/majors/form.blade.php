@extends('layouts.panel')
@section('title', $major->exists ? 'Ubah Jurusan' : 'Tambah Jurusan')

@section('content')
    <a href="{{ route('admin.majors.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Jurusan</a>
    <h1 class="t-page-title mb-6">{{ $major->exists ? 'Ubah Jurusan' : 'Tambah Jurusan' }}</h1>

    <form method="POST" action="{{ $major->exists ? route('admin.majors.update', $major) : route('admin.majors.store') }}" class="t-card max-w-xl space-y-5">
        @csrf
        @if ($major->exists) @method('PUT') @endif
        <div>
            <label for="name" class="t-label">Nama jurusan</label>
            <input id="name" name="name" value="{{ old('name', $major->name) }}" class="t-input" required>
            @error('name') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="code" class="t-label">Kode singkat</label>
            <input id="code" name="code" value="{{ old('code', $major->code) }}" class="t-input" maxlength="12" placeholder="mis. RPL" required>
            @error('code') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
