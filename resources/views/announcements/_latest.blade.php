{{-- Tiga pengumuman terbaru untuk pengguna yang sedang masuk (dipakai di beranda). --}}
@php $latestAnnouncements = \App\Models\Announcement::visibleTo(auth()->user())->with('creator:id,name', 'classes:id,name', 'majors:id,code')->latest('published_at')->take(3)->get(); @endphp
@if ($latestAnnouncements->isNotEmpty())
    <section class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-extrabold">Pengumuman</h2>
            <a href="{{ route(auth()->user()->role.'.announcements.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-3">
            @foreach ($latestAnnouncements as $a) @include('announcements._card', ['a' => $a]) @endforeach
        </div>
    </section>
@endif
