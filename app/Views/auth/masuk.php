<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card">
        <?= view('partials/auth_head', [
            'judul'    => 'Selamat Datang Kembali',
            'subjudul' => 'Masuk ke akun ND Tax and Law Anda',
        ]) ?>
        <?= view('partials/alerts') ?>
        <form method="post" action="<?= site_url('masuk') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="auth-label" for="email">Alamat Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= esc(old('email')) ?>" required autocomplete="username" placeholder="nama@email.com">
            </div>
            <?= view('partials/auth_password', [
                'id'           => 'password',
                'label'        => 'Kata Sandi',
                'autocomplete' => 'current-password',
            ]) ?>
            <div class="auth-row">
                <label class="auth-check" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1" <?= old('remember') ? 'checked' : '' ?>>
                    <span>Ingat saya</span>
                </label>
                <a class="auth-link" href="<?= site_url('lupa-password') ?>">Lupa Kata Sandi?</a>
            </div>
            <button class="btn btn-primary auth-submit w-100" type="submit">Masuk</button>
        </form>
        <?php if (! empty($google)): ?>
            <div class="auth-or"><span>atau</span></div>
            <a class="btn-google" href="<?= site_url('masuk/google') ?>">
                <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z"/>
                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 12 24 12c3.1 0 5.8 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/>
                    <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.6-5.2l-6.3-5.3C29.2 35.1 26.7 36 24 36c-5.3 0-9.7-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.2-3.5 5.7-6.6 7.1l6.3 5.3C37.4 38.3 44 34 44 24c0-1.2-.1-2.3-.4-3.5z"/>
                </svg>
                Masuk dengan Google
            </a>
        <?php endif ?>
        <p class="auth-foot">Belum punya akun? <a class="auth-link" href="<?= site_url('daftar') ?>">Daftar</a></p>
    </div>
</div>
<?= $this->endSection() ?>
