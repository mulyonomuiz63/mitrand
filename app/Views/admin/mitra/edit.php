<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Mitra</p>
<h1 class="page-title">Ubah data</h1>
<p class="text-secondary">Nomor anggota dan status diubah lewat tindakan persetujuan, bukan lewat formulir ini.</p>
<form class="form-panel" method="post" action="<?= site_url('admin/mitra/' . $mitra['id'] . '/edit') ?>" enctype="multipart/form-data">
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
