<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<?php if (! $mitra): ?>
    <div class="card"><div class="card-body">Profil keanggotaan tidak ditemukan.</div></div>
<?php else: ?>
    <section class="row g-3 mb-4">
        <?php foreach ([
            ['Status', ndt_status_mitra($mitra['status']), 'bi-patch-check', 'green'],
            ['Nomor anggota', $mitra['member_code'] ?: '—', 'bi-person-vcard', 'blue'],
            ['Komisi disetujui', ndt_rupiah($ringkas['komisi']['disetujui'] ?? 0), 'bi-cash-coin', 'violet'],
            ['Bonus disetujui', ndt_rupiah($ringkas['bonus']['disetujui'] ?? 0), 'bi-gift', 'amber'],
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
    <?php if ($mitra['status'] === 'pending'): ?>
        <div class="alert alert-warning">Pendaftaran menunggu persetujuan admin. Kartu, marketing tools, dan komisi belum dibuka.</div>
    <?php elseif ($mitra['status'] === 'rejected'): ?>
        <div class="alert alert-danger">Pendaftaran ditolak<?= $mitra['rejected_reason'] ? ': ' . esc($mitra['rejected_reason']) : '' ?>.</div>
    <?php elseif ($mitra['status'] === 'suspended'): ?>
        <div class="alert alert-danger">Keanggotaan nonaktif. Kartu tidak lolos verifikasi publik.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <p class="mb-1 fw-semibold">Kartu identitas siap diunduh</p>
                    <?php if ($kartu): ?>
                        <p class="text-secondary mb-0">Berlaku sampai <?= esc($kartu['valid_until']) ?>.</p>
                    <?php endif ?>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a class="btn btn-primary" href="<?= site_url('agen/kartu') ?>">Unduh kartu</a>
                    <a class="btn btn-outline-secondary" href="<?= site_url('agen/marketing') ?>">Marketing</a>
                    <a class="btn btn-outline-secondary" href="<?= site_url('agen/komisi') ?>">Komisi</a>
                </div>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>
<?= $this->endSection() ?>
