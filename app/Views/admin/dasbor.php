<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<section class="row g-3 mb-4">
    <?php foreach ([
        ['Mitra', $total, 'bi-people', 'blue'],
        ['Menunggu', $pending, 'bi-hourglass-split', 'amber'],
        ['Aktif', $active, 'bi-patch-check', 'green'],
        ['Marketing tayang', $marketing, 'bi-collection', 'violet'],
    ] as [$label, $value, $icon, $tone]): ?>
        <div class="col-6 col-xl-3">
            <article class="card h-100">
                <div class="card-body widget">
                    <span class="widget-icon <?= esc($tone) ?>"><i class="bi <?= esc($icon) ?>"></i></span>
                    <div>
                        <p class="text-secondary small mb-1"><?= esc($label) ?></p>
                        <p class="stat-value mb-0"><?= esc((string) $value) ?></p>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach ?>
</section>
<div class="card">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <p class="text-secondary small mb-1">Total catatan komisi dan bonus</p>
            <p class="stat-value mb-0"><?= esc(ndt_rupiah($komisi)) ?></p>
        </div>
        <a class="btn btn-primary" href="<?= site_url('admin/mitra?status=pending') ?>">Tinjau pendaftaran</a>
    </div>
</div>
<?= $this->endSection() ?>
