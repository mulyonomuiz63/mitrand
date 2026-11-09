<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Marketing</p>
<h1 class="page-title">Unggah softcopy</h1>
<form class="form-panel" method="post" action="<?= site_url('admin/marketing/baru') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="title">Judul</label>
        <input class="form-control" id="title" name="title" value="<?= esc(old('title')) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="category">Kategori</label>
        <input class="form-control" id="category" name="category" list="kategori" value="<?= esc(old('category')) ?>" required>
        <datalist id="kategori">
            <option value="Brosur">
            <option value="Presentasi">
            <option value="Skrip">
            <option value="Logo">
            <option value="Video">
        </datalist>
    </div>
    <div class="mb-3">
        <label class="form-label" for="description">Keterangan</label>
        <textarea class="form-control" id="description" name="description"><?= esc(old('description')) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label" for="berkas">Berkas PDF, JPG, PNG, atau video MP4, WEBM, MOV (maks. 32 MB)</label>
        <input class="form-control" id="berkas" name="berkas" type="file" accept=".pdf,.jpg,.jpeg,.png,.mp4,.webm,.mov,application/pdf,image/jpeg,image/png,video/mp4,video/webm,video/quicktime" required>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" checked>
        <label class="form-check-label" for="is_published">Tayangkan untuk mitra aktif</label>
    </div>
    <button class="btn btn-primary" type="submit">Simpan</button>
</form>
<?= $this->endSection() ?>
