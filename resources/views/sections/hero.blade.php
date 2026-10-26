<section class="hero" id="tentang">
    <span class="blob blob--yellow"></span>
    <span class="blob blob--blue"></span>
    <span class="blob blob--pink"></span>

    <div class="container hero__inner">
        <div class="hero__copy">
            <span class="pill">Belajar Lebih Seru, Masa Depan Lebih Cerah <em>✨</em></span>
            <h1>Belajar Tanpa Batas, Dengan <span class="grad-text">Cara yang Seru</span></h1>
            <p>NeuroTrack adalah platform belajar sekolahmu: kerjakan kuis dari guru, pantau progres, dan kenali kemampuanmu secara real-time.</p>

            <div class="hero__cta">
                <a href="{{ route('login') }}" class="btn btn--primary btn--lg">Mulai Belajar <x-icon name="arrow-right" :size="18" /></a>
                <a href="#kuis" class="btn btn--white btn--lg"><x-icon name="play" :size="14" fill /> Lihat Katalog Kuis</a>
            </div>

            <ul class="stats">
                @foreach ($stats as $s)
                    <li>
                        <span class="stats__icon tone-{{ $s['tone'] }}"><x-icon :name="$s['icon']" :size="22" :fill="$s['icon'] === 'star'" /></span>
                        <span><strong>{{ number_format($s['value'], 0, ',', '.') }}</strong><small>{{ $s['label'] }}</small></span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="hero__visual">
            <div class="bubble bubble--blue"><strong>Belajar</strong><span>Kapan Saja</span></div>
            <div class="bubble bubble--yellow">Satu Langkah lebih dekat dengan masa depanmu <em>✨</em></div>
            <ul class="checklist-card">
                @foreach (['Kuis Interaktif', 'Nilai Langsung Keluar', 'Pantau Progres'] as $item)
                    <li><span class="tick"><x-icon name="check" :size="14" /></span>{{ $item }}</li>
                @endforeach
            </ul>
            <img class="hero__img" src="{{ asset('images/hero.webp') }}" alt="Siswa belajar dengan laptop" width="1100" height="910" fetchpriority="high">
        </div>
    </div>
</section>

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
