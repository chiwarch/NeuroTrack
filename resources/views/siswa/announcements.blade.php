@extends('layouts.panel')
@section('title', 'Pengumuman')

@section('content')
    <div class="mb-6"><h1 class="t-page-title">Pengumuman</h1></div>
    @forelse ($announcements as $a)
        <div class="mb-3">@include('announcements._card', ['a' => $a])</div>
    @empty
        <div class="t-card text-center text-sm text-mute">Belum ada pengumuman.</div>
    @endforelse
    <div class="mt-4">{{ $announcements->links() }}</div>
@endsection
