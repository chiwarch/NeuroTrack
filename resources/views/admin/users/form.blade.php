@extends('layouts.panel')
@section('title', $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('content')
    <a href="{{ route('admin.users.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-mute hover:text-brand-600"><x-icon name="arrow-left" :size="16" /> Pengguna</a>
    <h1 class="t-page-title mb-6">{{ $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h1>

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="t-label">Nama lengkap</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="t-input" required>
                @error('name') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="t-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="t-input" required>
                @error('email') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="t-label">Kata sandi{{ $user->exists ? ' (kosongkan jika tidak diubah)' : '' }}</label>
                <input id="password" type="password" name="password" class="t-input" autocomplete="new-password" @required(! $user->exists)>
                @error('password') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="role" class="t-label">Role</label>
                <select id="role" name="role" class="t-input">
                    @foreach (\App\Models\User::ROLES as $k => $label)
                        <option value="{{ $k }}" @selected(old('role', $user->role) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <fieldset class="rounded-2xl bg-slate-50 p-4">
            <legend class="px-1 text-xs font-bold uppercase tracking-wide text-mute">Khusus siswa</legend>
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="nis" class="t-label">NIS</label>
                    <input id="nis" name="nis" value="{{ old('nis', $user->nis) }}" class="t-input" maxlength="20">
                    @error('nis') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="major_id" class="t-label">Jurusan</label>
                    <select id="major_id" name="major_id" class="t-input">
                        <option value="">—</option>
                        @foreach ($majors as $m)
                            <option value="{{ $m->id }}" @selected((int) old('major_id', $user->major_id) === $m->id)>{{ $m->code }}</option>
                        @endforeach
                    </select>
                    @error('major_id') <p class="t-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="grade_level" class="t-label">Tingkat</label>
                    <select id="grade_level" name="grade_level" class="t-input">
                        <option value="">—</option>
                        @foreach ([10, 11, 12] as $g)
                            <option value="{{ $g }}" @selected((int) old('grade_level', $user->grade_level) === $g)>Kelas {{ $g }}</option>
                        @endforeach
                    </select>
                    @error('grade_level') <p class="t-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </fieldset>

        <fieldset class="rounded-2xl bg-slate-50 p-4">
            <legend class="px-1 text-xs font-bold uppercase tracking-wide text-mute">Khusus orang tua</legend>
            @php $picked = collect(old('children', $linked))->map(fn ($v) => (int) $v)->all(); @endphp
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="relationship" class="t-label">Hubungan</label>
                    <select id="relationship" name="relationship" class="t-input">
                        <option value="">—</option>
                        @foreach (['Ayah', 'Ibu', 'Wali'] as $r)
                            <option value="{{ $r }}" @selected(old('relationship', $relationship) === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="child-filter" class="t-label">Anak (siswa)</label>
                    <input id="child-filter" type="search" placeholder="Cari nama atau NIS" class="t-input mb-2"
                           oninput="const q=this.value.toLowerCase();document.querySelectorAll('[data-child]').forEach(l=>l.hidden=!l.dataset.child.includes(q))">
                    <div class="max-h-56 space-y-1 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2">
                        @forelse ($students as $s)
                            <label data-child="{{ strtolower($s->name.' '.$s->nis) }}" class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50">
                                <input type="checkbox" name="children[]" value="{{ $s->id }}" @checked(in_array($s->id, $picked, true)) class="size-4 accent-brand-500">
                                <span class="font-semibold">{{ $s->name }}</span>
                                <span class="text-xs text-mute">{{ $s->nis }} · {{ $s->classes->first()?->name }}</span>
                            </label>
                        @empty
                            <p class="p-2 text-sm text-mute">Belum ada akun siswa.</p>
                        @endforelse
                    </div>
                    @error('children') <p class="t-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </fieldset>

        <button type="submit" class="t-btn t-btn-primary">Simpan</button>
    </form>
@endsection
