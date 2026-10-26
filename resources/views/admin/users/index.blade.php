@extends('layouts.panel')
@section('title', 'Pengguna')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h1 class="t-page-title">Pengguna</h1>
        <a href="{{ route('admin.users.create') }}" class="t-btn t-btn-primary"><x-icon name="plus" :size="16" /> Tambah Pengguna</a>
    </div>

    <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau NIS…" class="t-input sm:max-w-xs">
        <select name="role" class="t-input sm:max-w-[10rem]">
            <option value="">Semua role</option>
            @foreach (\App\Models\User::ROLES as $k => $label)
                <option value="{{ $k }}" @selected(request('role') === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="t-btn t-btn-line" type="submit"><x-icon name="search" :size="16" /> Cari</button>
    </form>

    <div class="t-card overflow-x-auto p-0">
        <table class="w-full">
            <thead class="border-b border-slate-100"><tr><th class="t-th">Nama</th><th class="t-th">Role</th><th class="t-th">NIS</th><th class="t-th">Jurusan / Tingkat</th><th class="t-th"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $u)
                    <tr>
                        <td class="t-td font-bold">{{ $u->name }}<span class="block text-xs font-medium text-mute">{{ $u->email }}</span></td>
                        <td class="t-td"><span class="t-badge bg-brand-50 text-brand-600">{{ \App\Models\User::ROLES[$u->role] ?? $u->role }}</span></td>
                        <td class="t-td text-mute">{{ $u->nis ?: '–' }}</td>
                        <td class="t-td text-mute">{{ $u->major ? $u->major->code.' · kelas '.$u->grade_level : '–' }}</td>
                        <td class="t-td">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $u) }}" class="t-btn t-btn-line t-btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="t-btn t-btn-danger t-btn-sm" aria-label="Hapus"><x-icon name="trash" :size="14" /></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="t-td text-center text-mute">Tidak ada pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
@endsection
