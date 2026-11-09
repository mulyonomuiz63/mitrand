<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Keanggotaan</p>
<h1 class="page-title"><?= esc($mitra['full_name']) ?></h1>
<div class="card border-0 shadow-sm my-3">
    <div class="card-body d-flex flex-column flex-sm-row gap-3">
        <?php if (! empty($mitra['photo'])): ?>
            <img class="portrait" src="<?= site_url('berkas/foto/' . $mitra['id']) ?>" alt="Foto <?= esc($mitra['full_name']) ?>">
        <?php endif ?>
        <div>
            <p class="mb-1">Nomor anggota: <strong><?= esc($mitra['member_code'] ?: 'belum terbit') ?></strong></p>
            <p class="mb-1">Status: <span class="badge <?= ndt_badge($mitra['status']) ?>"><?= esc(ndt_status_mitra($mitra['status'])) ?></span></p>
            <p class="mb-0 text-break">Email: <?= esc($email) ?></p>
        </div>
    </div>
</div>
<h2 class="h4">Ubah data diri</h2>
<form class="form-panel" method="post" action="<?= site_url('agen/profil') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="full_name">Nama</label>
        <input class="form-control" id="full_name" name="full_name" value="<?= esc(old('full_name', $mitra['full_name'])) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="phone">Telepon</label>
        <input class="form-control" id="phone" name="phone" value="<?= esc(old('phone', $mitra['phone'])) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="address">Alamat</label>
        <textarea class="form-control" id="address" name="address" required><?= esc(old('address', $mitra['address'])) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label" for="photo">Ganti foto</label>
        <input class="form-control" id="photo" name="photo" type="file" accept="image/jpeg,image/png">
    </div>
    <button class="btn btn-primary" type="submit">Simpan</button>
</form>
<?= $this->endSection() ?>
