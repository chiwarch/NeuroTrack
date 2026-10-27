@extends('layouts.panel')
@section('title', $badge->exists ? 'Ubah Lencana' : 'Tambah Lencana')

@section('content')
    <a href="{{ route('admin.badges.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Lencana</a>
    <h1 class="t-page-title mb-6">{{ $badge->exists ? 'Ubah Lencana' : 'Tambah Lencana' }}</h1>

    @php $locked = $badge->exists && $badge->type === 'auto'; $type = old('type', $badge->type ?: 'manual'); @endphp
    <form method="POST" action="{{ $badge->exists ? route('admin.badges.update', $badge) : route('admin.badges.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        @if ($badge->exists) @method('PUT') @endif

        <div>
            <label for="name" class="t-label">Nama lencana</label>
            <input id="name" name="name" value="{{ old('name', $badge->name) }}" class="t-input" maxlength="150" placeholder="mis. The Prodigy" required>
            @error('name') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="description" class="t-label">Deskripsi</label>
            <input id="description" name="description" value="{{ old('description', $badge->description) }}" class="t-input" maxlength="500">
            @error('description') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="icon" class="t-label">Ikon</label>
                <select id="icon" name="icon" class="t-input">
                    @foreach (\App\Models\Badge::ICONS as $i)<option value="{{ $i }}" @selected(old('icon', $badge->icon) === $i)>{{ $i }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="tone" class="t-label">Warna</label>
                <select id="tone" name="tone" class="t-input">
                    @foreach (array_keys(\App\Models\Badge::TONES) as $t)<option value="{{ $t }}" @selected(old('tone', $badge->tone) === $t)>{{ $t }}</option>@endforeach
                </select>
            </div>
        </div>
        @if ($locked)
            <p class="rounded-xl bg-slate-50 p-3 text-sm text-mute">Lencana otomatis: syaratnya ditentukan sistem ({{ $badge->description }}). Kamu hanya bisa mengubah nama, tampilan, dan bonus XP.</p>
        @else
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="type" class="t-label">Cara mendapatkan</label>
                    <select id="type" name="type" class="t-input" onchange="document.getElementById('xp-box').hidden = this.value !== 'xp'">
                        <option value="manual" @selected($type === 'manual')>Diberikan guru/admin</option>
                        <option value="xp" @selected($type === 'xp')>Otomatis dari total XP</option>
                    </select>
                </div>
                <div id="xp-box" @if ($type !== 'xp') hidden @endif>
                    <label for="required_xp" class="t-label">XP yang dibutuhkan</label>
                    <input id="required_xp" type="number" name="required_xp" min="1" value="{{ old('required_xp', $badge->required_xp ?: '') }}" class="t-input">
                    @error('required_xp') <p class="t-error">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif
        <div>
            <label for="xp_reward" class="t-label">Bonus XP saat didapat</label>
            <input id="xp_reward" type="number" name="xp_reward" min="0" max="500" value="{{ old('xp_reward', $badge->xp_reward ?? 0) }}" class="t-input !w-32" required>
            @error('xp_reward') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <button class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
