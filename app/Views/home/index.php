<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="hero">
    <div class="container py-4 py-lg-5">
        <div class="hero-panel rounded-4 p-3 p-md-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <p class="kicker">Keanggotaan mitra</p>
                    <h1 class="page-title display-6 fw-bold">Kartu identitas dan ruang kerja mitra.</h1>
                    <p class="text-secondary mt-3">Daftarkan diri sebagai mitra. Setelah disetujui, Anda menerima nomor anggota, kartu identitas dengan QR, peralatan marketing, serta riwayat komisi dan bonus milik Anda sendiri.</p>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a class="btn btn-primary px-4" href="<?= site_url('daftar') ?>">Daftar sebagai mitra</a>
                        <a class="btn btn-outline-secondary px-4" href="<?= site_url('masuk') ?>">Sudah punya akun</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card-preview d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge text-bg-light text-primary">NDTAXANDLAW</span>
                            <p class="mb-1 mt-4 fs-5 fw-semibold">Kartu mitra</p>
                            <p class="mb-0 opacity-75">NDT-<?= date('Y') ?>-00001</p>
                        </div>
                        <p class="small mb-0 mt-4 opacity-75">QR hanya menampilkan nama, nomor, dan status.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <?php foreach ([
                ['bi-person-check', 'Keanggotaan', 'Admin meninjau pendaftaran. Nomor anggota terbit saat status menjadi aktif.'],
                ['bi-collection', 'Marketing tools', 'Mitra aktif mengunduh softcopy yang disiapkan kantor untuk mencari klien.'],
                ['bi-cash-coin', 'Komisi dan bonus', 'Setiap mitra hanya melihat catatan yang admin buat untuk dirinya.'],
            ] as [$icon, $title, $text]): ?>
                <div class="col-md-4">
                    <article class="card h-100">
                        <div class="card-body">
                            <div class="feature-icon"><i class="bi <?= esc($icon) ?>"></i></div>
                            <h2 class="h6 fw-bold"><?= esc($title) ?></h2>
                            <p class="mb-0 text-secondary"><?= esc($text) ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
