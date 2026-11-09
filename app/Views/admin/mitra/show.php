<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Mitra</p>
<h1 class="page-title"><?= esc($mitra['full_name']) ?></h1>
<div class="card border-0 shadow-sm my-3">
    <div class="card-body">
        <div class="d-flex flex-column flex-sm-row gap-3">
            <?php if (! empty($mitra['photo'])): ?>
                <img class="portrait" src="<?= site_url('berkas/foto/' . $mitra['id']) ?>" alt="Foto <?= esc($mitra['full_name']) ?>">
            <?php endif ?>
            <div>
                <p class="mb-1">Email: <?= esc($mitra['email'] ?? '') ?></p>
                <p class="mb-1">Telepon: <?= esc($mitra['phone']) ?></p>
                <p class="mb-1">Alamat: <?= esc($mitra['address']) ?></p>
                <p class="mb-1">Nomor anggota: <strong><?= esc($mitra['member_code'] ?: 'belum terbit') ?></strong></p>
                <p class="mb-1">Status: <span class="badge <?= ndt_badge($mitra['status']) ?>"><?= esc(ndt_status_mitra($mitra['status'])) ?></span></p>
                <?php if ($mitra['rejected_reason']): ?>
                    <p class="mb-1">Alasan tolak: <?= esc($mitra['rejected_reason']) ?></p>
                <?php endif ?>
                <?php if ($kartu): ?>
                    <p class="mb-2">Kartu <?= esc($kartu['status']) ?>, berlaku sampai <?= esc($kartu['valid_until']) ?></p>
                <?php endif ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/mitra/' . $mitra['id'] . '/edit') ?>">Ubah data</a>
            </div>
        </div>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (in_array($mitra['status'], ['pending', 'rejected', 'suspended'], true)): ?>
            <form method="post" action="<?= site_url('admin/mitra/' . $mitra['id'] . '/setujui') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-primary" type="submit">Setujui dan aktifkan</button>
            </form>
        <?php endif ?>
        <?php if ($mitra['status'] === 'pending'): ?>
            <form class="mt-3" method="post" action="<?= site_url('admin/mitra/' . $mitra['id'] . '/tolak') ?>">
                <?= csrf_field() ?>
                <label class="form-label" for="reason">Alasan penolakan</label>
                <input class="form-control mb-2" id="reason" name="reason" required>
                <button class="btn btn-danger" type="submit">Tolak</button>
            </form>
        <?php endif ?>
        <?php if ($mitra['status'] === 'active'): ?>
            <form method="post" action="<?= site_url('admin/mitra/' . $mitra['id'] . '/nonaktifkan') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-danger" type="submit">Nonaktifkan</button>
            </form>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
