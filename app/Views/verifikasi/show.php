<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <p class="kicker">Verifikasi publik</p>
                    <h1 class="h3">Kartu mitra</h1>
                    <?php if (! $card): ?>
                        <p class="mb-0">Data kartu tidak ditemukan.</p>
                    <?php else: ?>
                        <p class="fs-5 mb-2"><strong><?= esc($card['full_name']) ?></strong></p>
                        <p class="mb-2">Nomor anggota: <?= esc($card['member_code']) ?></p>
                        <p class="mb-0">Status: <span class="badge <?= ndt_badge($aktif ? 'active' : 'suspended') ?>"><?= $aktif ? 'Aktif' : 'Nonaktif' ?></span></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
