@props(['id' => 'password'])
<div style="position:relative">
    <input id="{{ $id }}" type="password" {{ $attributes->merge(['class' => 't-input']) }} style="padding-right: 3rem">
    <button type="button" data-toggle-password="{{ $id }}" aria-label="Tampilkan kata sandi" aria-pressed="false" class="text-mute transition hover:text-brand-600" style="position:absolute;top:0;bottom:0;right:0;width:3rem;display:grid;place-items:center;background:transparent;border:0;cursor:pointer">
        <svg data-eye xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        <svg data-eye-off style="display:none" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><path d="m2 2 20 20"/></svg>
    </button>
</div>
@once
    <script>
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-toggle-password]');
            if (!btn) return;
            var input = document.getElementById(btn.dataset.togglePassword);
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            btn.querySelector('[data-eye]').style.display = show ? 'none' : 'block';
            btn.querySelector('[data-eye-off]').style.display = show ? 'block' : 'none';
        });
    </script>
@endonce
