<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card">
        <?= view('partials/auth_head', [
            'judul'    => 'Atur Ulang Kata Sandi',
            'subjudul' => 'Buat kata sandi baru untuk akun Anda',
        ]) ?>
        <?= view('partials/alerts') ?>
        <form method="post" action="<?= site_url('reset-password/' . $token) ?>">
            <?= csrf_field() ?>
            <?= view('partials/auth_password', [
                'id'           => 'password',
                'label'        => 'Kata Sandi Baru',
                'minlength'    => 8,
                'autocomplete' => 'new-password',
            ]) ?>
            <?= view('partials/auth_password', [
                'id'           => 'password_confirm',
                'name'         => 'password_confirm',
                'label'        => 'Ulangi Kata Sandi',
                'autocomplete' => 'new-password',
            ]) ?>
            <button class="btn btn-primary auth-submit w-100" type="submit">Simpan kata sandi</button>
        </form>
        <p class="auth-foot"><a class="auth-link" href="<?= site_url('masuk') ?>">Kembali ke masuk</a></p>
    </div>
</div>
<?= $this->endSection() ?>
