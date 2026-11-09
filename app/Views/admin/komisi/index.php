<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
    <div>
        <p class="kicker">Keuangan mitra</p>
        <h1 class="page-title mb-0">Komisi dan bonus</h1>
    </div>
    <a class="btn btn-primary" href="<?= site_url('admin/komisi/baru') ?>">Catat baru</a>
</div>
<div class="data-card">
    <form class="data-filters" method="get" action="<?= site_url('admin/komisi') ?>">
        <input type="hidden" name="sort" value="<?= esc($filters['sort']) ?>">
        <input type="hidden" name="dir" value="<?= esc($filters['dir']) ?>">
        <div class="data-filters-grid">
            <div class="data-field">
                <label for="q">Cari</label>
                <input class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>" placeholder="Mitra, nomor, keterangan">
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
            <thead>
                <tr>
                    <?php $keep = $filters; unset($keep['page']); ?>
                    <?php foreach (['mitra' => 'Mitra', 'jenis' => 'Jenis', 'periode' => 'Periode', 'nominal' => 'Nominal', 'status' => 'Status'] as $key => $label): ?>
                        <?php $next = $filters['sort'] === $key && $filters['dir'] === 'ASC' ? 'DESC' : 'ASC'; ?>
                        <th>
                            <a class="sort-link<?= $filters['sort'] === $key ? ' is-on' : '' ?>" href="<?= esc(ndt_query_url(array_merge($keep, ['sort' => $key, 'dir' => $next]))) ?>">
                                <?= esc($label) ?>
                                <?php if ($filters['sort'] === $key): ?><i class="bi bi-chevron-<?= $filters['dir'] === 'ASC' ? 'up' : 'down' ?>"></i><?php endif ?>
                            </a>
                        </th>
                    <?php endforeach ?>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($row['full_name']) ?><br><span class="text-secondary small"><?= esc($row['member_code'] ?: '') ?></span></td>
                    <td><?= esc(ndt_jenis($row['jenis'])) ?></td>
                    <td><?= esc($row['periode']) ?></td>
                    <td><?= esc(ndt_rupiah($row['nominal'])) ?></td>
                    <td><span class="badge <?= ndt_badge($row['status']) ?>"><?= esc(ndt_status_komisi($row['status'])) ?></span></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/komisi/' . $row['id'] . '/edit') ?>">Ubah</a></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($rows === []): ?>
            <p class="data-empty mb-0">Tidak ada catatan yang cocok dengan filter ini.</p>
        <?php endif ?>
    </div>
    <?= view('partials/data_foot', ['from' => $from, 'to' => $to, 'total' => $total, 'perPage' => $perPage, 'pager' => $pager]) ?>
</div>
<?= $this->endSection() ?>
