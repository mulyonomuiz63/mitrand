<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card auth-card-wide">
        <?= view('partials/auth_head', [
            'judul'    => 'Daftar Akun Mitra',
            'subjudul' => 'Status awal menunggu persetujuan. Kartu identitas terbit setelah admin menyetujui.',
        ]) ?>
        <?= view('partials/alerts') ?>
        <form method="post" action="<?= site_url('daftar') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="auth-label" for="full_name">Nama lengkap</label>
                    <input class="form-control" id="full_name" name="full_name" value="<?= esc(old('full_name')) ?>" required autocomplete="name">
                </div>
                <div class="col-md-6">
                    <label class="auth-label" for="email">Alamat Email</label>
                    <input class="form-control" id="email" name="email" type="email" value="<?= esc(old('email')) ?>" required autocomplete="email">
                </div>
                <div class="col-md-6">
                    <label class="auth-label" for="phone">Telepon</label>
                    <input class="form-control" id="phone" name="phone" value="<?= esc(old('phone')) ?>" required autocomplete="tel">
                </div>
                <div class="col-md-6">
                    <label class="auth-label" for="photo">Foto diri (JPG atau PNG, maks. 2 MB)</label>
                    <input class="form-control" id="photo" name="photo" type="file" accept="image/jpeg,image/png" required>
                </div>
                <div class="col-12">
                    <label class="auth-label" for="address">Alamat</label>
                    <textarea class="form-control" id="address" name="address" required><?= esc(old('address')) ?></textarea>
                </div>
                <div class="col-md-6">
                    <?= view('partials/auth_password', [
                        'id'           => 'password',
                        'label'        => 'Kata Sandi',
                        'minlength'    => 8,
                        'autocomplete' => 'new-password',
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= view('partials/auth_password', [
                        'id'           => 'password_confirm',
                        'name'         => 'password_confirm',
                        'label'        => 'Ulangi Kata Sandi',
                        'autocomplete' => 'new-password',
                    ]) ?>
                </div>
            </div>
            <button class="btn btn-primary auth-submit w-100" type="submit">Daftar</button>
        </form>
        <p class="auth-foot">Sudah punya akun? <a class="auth-link" href="<?= site_url('masuk') ?>">Masuk</a></p>
    </div>
</div>
<?= $this->endSection() ?>
