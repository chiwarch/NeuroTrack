<x-hero-banner id="tentang" :href="auth()->check() ? route('dashboard') : route('login')" />

<section class="features">
    <div class="container features__grid">
        @foreach ($features as $f)
            <article class="feature tone-bg-{{ $f['tone'] }}">
                <img src="{{ asset('images/'.$f['img'].'.webp') }}" alt="" width="84" height="84" loading="lazy">
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>
