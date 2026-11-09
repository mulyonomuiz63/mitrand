<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Akun</p>
<h1 class="page-title">Masuk dengan Google</h1>
<p class="text-secondary">Tombol Google di halaman masuk mengikuti pengaturan ini. Hanya email yang sudah terdaftar yang dapat masuk. Pendaftaran mitra tetap lewat formulir, karena foto dan data keanggotaan masih diperlukan.</p>
<?php if ($siap): ?>
    <div class="alert alert-success">Masuk dengan Google aktif<?= $hint !== '' ? ' · rahasia berakhiran ' . esc($hint) : '' ?>.</div>
<?php else: ?>
    <div class="alert alert-warning">Masuk dengan Google nonaktif. Tombol tidak ditampilkan di halaman masuk.</div>
<?php endif ?>
<form class="form-panel" method="post" action="<?= site_url('admin/masuk-google') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="redirect_uri">URI pengalihan</label>
        <input class="form-control" id="redirect_uri" value="<?= esc($redirect) ?>" readonly>
        <div class="form-text">Daftarkan alamat ini persis di Google Cloud, pada OAuth Client jenis Web application.</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="client_id">Client ID</label>
        <input class="form-control" id="client_id" name="client_id" value="<?= esc(old('client_id', $clientId)) ?>" placeholder="xxxx.apps.googleusercontent.com" autocomplete="off">
    </div>
    <div class="mb-3">
        <label class="form-label" for="client_secret">Client Secret</label>
        <input class="form-control" id="client_secret" name="client_secret" type="password" autocomplete="off" placeholder="<?= $hint !== '' ? 'Kosongkan jika rahasia tidak diganti' : 'Tempel Client Secret' ?>">
        <?php if ($hint !== ''): ?>
            <div class="form-text">Rahasia tersimpan, berakhiran <?= esc($hint) ?>.</div>
        <?php endif ?>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled" <?= old('enabled', $enabled ? '1' : '') ? 'checked' : '' ?>>
        <label class="form-check-label" for="enabled">Aktifkan masuk dengan Google</label>
    </div>
    <button class="btn btn-primary" type="submit">Simpan</button>
</form>
<?= $this->endSection() ?>
