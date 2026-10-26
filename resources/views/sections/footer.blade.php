<footer class="footer" id="kontak">
    <div class="container footer__grid">
        <div class="footer__brand">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('images/grad-cap.webp') }}" alt="" width="34" height="34"><span>Neuro<b>Track</b></span>
            </a>
            <p>Belajar hari ini, untuk masa depan lebih baik.</p>
            <div class="socials">
                @foreach (['youtube', 'instagram', 'twitter', 'linkedin'] as $s)
                    <a href="#" aria-label="{{ ucfirst($s) }}"><x-icon :name="$s" :size="16" /></a>
                @endforeach
            </div>
        </div>

        <div>
            <h5>Menu</h5>
            <ul>
                <li><a href="#top">Beranda</a></li><li><a href="#kuis">Kuis</a></li>
                <li><a href="#progres">Progres</a></li><li><a href="#mapel">Mapel</a></li><li><a href="#kontak">Kontak</a></li>
            </ul>
        </div>

        <div>
            <h5>Bantuan</h5>
            <ul>
                <li><a href="#">FAQ</a></li><li><a href="#">Pusat Bantuan</a></li>
                <li><a href="#">Kebijakan Privasi</a></li><li><a href="#">Syarat &amp; Ketentuan</a></li>
            </ul>
        </div>

        <div id="newsletter">
            <h5>Newsletter</h5>
            <p class="muted">Dapatkan update kursus terbaru!</p>
            <form method="POST" action="{{ route('newsletter.store') }}" class="news">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email kamu" required aria-label="Email">
                <button type="submit" aria-label="Berlangganan"><x-icon name="send" :size="18" /></button>
            </form>
            @error('email') <p class="form-msg form-msg--error">{{ $message }}</p> @enderror
            @if (session('newsletter_success')) <p class="form-msg form-msg--ok">{{ session('newsletter_success') }}</p> @endif
        </div>
    </div>
</footer>
