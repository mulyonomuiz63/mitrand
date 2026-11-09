<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Keuangan</p>
<h1 class="page-title mb-4">Komisi dan bonus saya</h1>
<section class="row g-3 mb-4">
    <?php foreach ([
        ['Komisi disetujui', $ringkas['komisi']['disetujui']],
        ['Komisi dibayar', $ringkas['komisi']['dibayar']],
        ['Bonus disetujui', $ringkas['bonus']['disetujui']],
        ['Bonus dibayar', $ringkas['bonus']['dibayar']],
    ] as [$label, $value]): ?>
        <div class="col-6 col-xl-3">
            <article class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-secondary small mb-1"><?= esc($label) ?></p>
                    <p class="stat-value mb-0"><?= esc(ndt_rupiah($value)) ?></p>
                </div>
            </article>
        </div>
    <?php endforeach ?>
</section>
<div class="data-card">
    <form class="data-filters" method="get" action="<?= site_url('agen/komisi') ?>">
        <input type="hidden" name="sort" value="<?= esc($filters['sort']) ?>">
        <input type="hidden" name="dir" value="<?= esc($filters['dir']) ?>">
        <div class="data-filters-grid">
            <div class="data-field">
                <label for="q">Cari keterangan</label>
                <input class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>" placeholder="Kata dalam keterangan">
            </div>
            <div class="data-field">
                <label for="jenis">Jenis</label>
                <select class="form-select" id="jenis" name="jenis">
                    <option value="">Semua jenis</option>
                    <option value="komisi" <?= $filters['jenis'] === 'komisi' ? 'selected' : '' ?>>Komisi</option>
                    <option value="bonus" <?= $filters['jenis'] === 'bonus' ? 'selected' : '' ?>>Bonus</option>
                </select>
            </div>
            <div class="data-field">
                <label for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua status</option>
                    <?php foreach (['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'dibayar' => 'Dibayar'] as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="data-field">
                <label for="dari">Periode dari</label>
                <input class="form-control" id="dari" name="dari" type="month" value="<?= esc($filters['dari']) ?>">
            </div>
            <div class="data-field">
                <label for="sampai">Periode sampai</label>
                <input class="form-control" id="sampai" name="sampai" type="month" value="<?= esc($filters['sampai']) ?>">
            </div>
        </div>
        <?= view('partials/data_actions', ['perPage' => $filters['per_page']]) ?>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Periode</th><th>Jenis</th><th>Nominal</th><th>Status</th><th>Keterangan</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= esc($row['periode']) ?></td>
                    <td><?= esc(ndt_jenis($row['jenis'])) ?></td>
                    <td><?= esc(ndt_rupiah($row['nominal'])) ?></td>
                    <td><span class="badge <?= ndt_badge($row['status']) ?>"><?= esc(ndt_status_komisi($row['status'])) ?></span></td>
                    <td><?= esc($row['keterangan']) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($rows === []): ?>
            <p class="data-empty mb-0">Belum ada catatan untuk Anda.</p>
        <?php endif ?>
    </div>
    <?= view('partials/data_foot', ['from' => $from, 'to' => $to, 'total' => $total, 'perPage' => $perPage, 'pager' => $pager]) ?>
</div>
<?= $this->endSection() ?>
