@extends('layouts.panel')
@section('title', 'Statistik')

@section('content')
    <div class="mb-6">
        <h1 class="t-page-title">Statistikmu</h1>
        <p class="mt-1 text-sm text-mute">Kerjakan kuis untuk menaikkan hard skill. Soft skill dinilai oleh gurumu.</p>
    </div>
    @include('partials.stats')
@endsection
