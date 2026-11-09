<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card">
        <?= view('partials/auth_head', [
            'judul'    => 'Lupa Kata Sandi',
            'subjudul' => 'Masukkan email akun ND Tax and Law Anda',
        ]) ?>
        <?= view('partials/alerts') ?>
        <form method="post" action="<?= site_url('lupa-password') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="auth-label" for="email">Alamat Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= esc(old('email')) ?>" required autocomplete="email" placeholder="nama@email.com">
            </div>
            <button class="btn btn-primary auth-submit w-100" type="submit">Kirim tautan</button>
        </form>
        <p class="auth-foot"><a class="auth-link" href="<?= site_url('masuk') ?>">Kembali ke masuk</a></p>
    </div>
</div>
<?= $this->endSection() ?>
