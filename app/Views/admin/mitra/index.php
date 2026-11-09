<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Admin</p>
<h1 class="page-title mb-3">Mitra</h1>
<div class="data-card">
    <form class="data-filters" method="get" action="<?= site_url('admin/mitra') ?>">
        <input type="hidden" name="sort" value="<?= esc($filters['sort']) ?>">
        <input type="hidden" name="dir" value="<?= esc($filters['dir']) ?>">
        <div class="data-filters-grid">
            <div class="data-field">
                <label for="q">Cari</label>
                <input class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>" placeholder="Nama, email, nomor, telepon">
            </div>
            <div class="data-field">
                <label for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua status</option>
                    <?php foreach (['pending' => 'Menunggu', 'active' => 'Aktif', 'rejected' => 'Ditolak', 'suspended' => 'Nonaktif'] as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="data-field">
                <label for="dari">Daftar dari</label>
                <input class="form-control" id="dari" name="dari" type="date" value="<?= esc($filters['dari']) ?>">
            </div>
            <div class="data-field">
                <label for="sampai">Daftar sampai</label>
                <input class="form-control" id="sampai" name="sampai" type="date" value="<?= esc($filters['sampai']) ?>">
            </div>
        </div>
        <?= view('partials/data_actions', ['perPage' => $filters['per_page']]) ?>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <?php
                        $keep = $filters;
                        unset($keep['page']);
                        $head = [
                            'nama' => 'Nama',
                            'email' => 'Email',
                            'nomor' => 'Nomor',
                            'status' => 'Status',
                        ];
                    ?>
                    <?php foreach ($head as $key => $label): ?>
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
                    <td class="fw-semibold"><?= esc($row['full_name']) ?></td>
                    <td class="text-break"><?= esc($row['email'] ?? '') ?></td>
                    <td><?= esc($row['member_code'] ?: '—') ?></td>
                    <td><span class="badge <?= ndt_badge($row['status']) ?>"><?= esc(ndt_status_mitra($row['status'])) ?></span></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/mitra/' . $row['id']) ?>">Detail</a></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($rows === []): ?>
            <p class="data-empty mb-0">Tidak ada mitra yang cocok dengan filter ini.</p>
        <?php endif ?>
    </div>
    <?= view('partials/data_foot', ['from' => $from, 'to' => $to, 'total' => $total, 'perPage' => $perPage, 'pager' => $pager]) ?>
</div>
<?= $this->endSection() ?>
