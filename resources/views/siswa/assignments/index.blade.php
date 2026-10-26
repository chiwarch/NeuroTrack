@extends('layouts.panel')
@section('title', 'Tugas')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Tugas</h1>
        <p class="mt-1 text-sm text-mute">{{ $class ? 'Tugas untuk kelas '.$class->name : 'Kamu belum terdaftar di kelas mana pun. Hubungi admin.' }}</p>
    </div>

    @if ($assignments->isEmpty())
        <div class="t-card text-center text-sm text-mute">Belum ada tugas.</div>
    @else
        <div class="space-y-3">
            @foreach ($assignments as $a)
                @php
                    $sub = $submissions->get($a->id);
                    if ($sub && $sub->isGraded()) { [$label, $tone] = ['Dinilai: '.($sub->score + 0).'/'.($a->max_score + 0), 'bg-emerald-100 text-emerald-700']; }
                    elseif ($sub) { [$label, $tone] = ['Dikumpulkan', 'bg-sky-100 text-sky-700']; }
                    elseif ($a->isPastDeadline()) { [$label, $tone] = ['Lewat tenggat', 'bg-rose-100 text-rose-700']; }
                    else { [$label, $tone] = ['Belum dikumpulkan', 'bg-amber-100 text-amber-700']; }
                @endphp
                <a href="{{ route('siswa.assignments.show', $a) }}" class="t-card flex flex-wrap items-center gap-4 transition hover:-translate-y-0.5">
                    <div class="min-w-0 flex-1">
                        <p class="font-extrabold">{{ $a->title }}</p>
                        <p class="text-xs text-mute">{{ $a->classSubject->subject->name }} · {{ $a->deadline ? 'tenggat '.$a->deadline->translatedFormat('d M Y, H:i') : 'tanpa tenggat' }}</p>
                    </div>
                    <span class="t-badge {{ $tone }}">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection
