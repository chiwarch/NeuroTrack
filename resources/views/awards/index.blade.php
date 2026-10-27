@extends('layouts.panel')
@section('title', 'Penghargaan')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Penghargaan</h1>
        <p class="mt-1 text-sm text-mute">Berikan lencana untuk lomba, juara kelas, atau sikap yang patut dibanggakan. XP bonus otomatis masuk ke siswa.</p>
    </div>

    <form method="POST" action="{{ route($prefix.'.awards.store') }}" class="t-card max-w-2xl space-y-5">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="student_id" class="t-label">Siswa</label>
                <select id="student_id" name="student_id" class="t-input" required>
                    <option value="">— Pilih siswa —</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}" @selected(old('student_id') == $s->id)>{{ $s->name }} · {{ $s->nis }}{{ $s->classes->first() ? ' · '.$s->classes->first()->name : '' }}</option>
                    @endforeach
                </select>
                @error('student_id') <p class="t-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="badge_id" class="t-label">Lencana</label>
                <select id="badge_id" name="badge_id" class="t-input" required>
                    <option value="">— Pilih lencana —</option>
                    @foreach ($badges as $b)
                        <option value="{{ $b->id }}" @selected(old('badge_id') == $b->id)>{{ $b->name }}{{ $b->xp_reward ? ' (+'.$b->xp_reward.' XP)' : '' }} — {{ $b->description }}</option>
                    @endforeach
                </select>
                @error('badge_id') <p class="t-error">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label for="note" class="t-label">Catatan (opsional)</label>
            <input id="note" name="note" value="{{ old('note') }}" maxlength="300" class="t-input" placeholder="mis. Juara 2 Lomba LKS tingkat kota">
            @error('note') <p class="t-error">{{ $message }}</p> @enderror
        </div>
        <button class="t-btn t-btn-primary">Berikan lencana</button>
    </form>

    <section class="mt-8">
        <h2 class="mb-3 text-lg font-extrabold">Terakhir diberikan</h2>
        <div class="t-card overflow-x-auto p-0">
            <table class="w-full">
                <thead class="border-b border-slate-100"><tr><th class="t-th">Siswa</th><th class="t-th">Lencana</th><th class="t-th">Oleh</th><th class="t-th"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recent as $r)
                        <tr>
                            <td class="t-td font-bold">{{ $r->user->name }}<span class="block text-xs font-medium text-mute">{{ $r->earned_at->translatedFormat('d M Y') }}@if ($r->note) · {{ $r->note }}@endif</span></td>
                            <td class="t-td"><span class="inline-flex items-center gap-2"><x-badge-icon :badge="$r->badge" :size="28" class="!rounded-lg" /> {{ $r->badge->name }}</span></td>
                            <td class="t-td">{{ $r->awardedBy?->name }}</td>
                            <td class="t-td text-right">
                                <form method="POST" action="{{ route($prefix.'.awards.destroy', $r) }}" onsubmit="return confirm('Cabut penghargaan ini? Bonus XP-nya ikut dicabut.')">@csrf @method('DELETE')<button class="t-btn t-btn-line t-btn-sm">Cabut</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="t-td text-center text-mute">Belum ada penghargaan yang diberikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
