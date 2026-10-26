<header class="nav" id="top">
    <div class="container nav__inner">
        <a href="{{ route('home') }}" class="logo" aria-label="NeuroTrack">
            <img src="{{ asset('images/grad-cap.webp') }}" alt="" width="34" height="34">
            <span>Neuro<b>Track</b></span>
        </a>

        <nav class="nav__links" id="nav-links" aria-label="Menu utama">
            <a href="#top" class="is-active">Beranda</a>
            <a href="#kuis">Kuis</a>
            <a href="#progres">Progres</a>
            <a href="#mapel">Mapel</a>
            <a href="#kontak">Kontak</a>
        </nav>

        <div class="nav__actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn--primary btn--sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn--primary btn--sm">Masuk</a>
            @endauth
            <button class="nav__toggle" type="button" id="nav-toggle" aria-label="Buka menu" aria-expanded="false">
                <x-icon name="menu" :size="22" />
            </button>
        </div>
    </div>
</header>
