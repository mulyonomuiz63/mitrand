<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Akun</p>
<h1 class="page-title">Ganti kata sandi</h1>
<form class="form-panel" method="post" action="<?= site_url('agen/password') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="current_password">Kata sandi sekarang</label>
        <input class="form-control" id="current_password" name="current_password" type="password" required autocomplete="current-password">
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Kata sandi baru</label>
        <input class="form-control" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
    </div>
    <div class="mb-3">
        <label class="form-label" for="password_confirm">Ulangi kata sandi baru</label>
        <input class="form-control" id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password">
    </div>
    <button class="btn btn-primary" type="submit">Perbarui</button>
</form>
<?= $this->endSection() ?>
