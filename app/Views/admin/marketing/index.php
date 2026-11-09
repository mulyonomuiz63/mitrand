<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
    <div>
        <p class="kicker">Marketing</p>
        <h1 class="page-title mb-0">Peralatan marketing</h1>
    </div>
    <a class="btn btn-primary" href="<?= site_url('admin/marketing/baru') ?>">Unggah berkas</a>
</div>
<div class="data-card">
    <form class="data-filters" method="get" action="<?= site_url('admin/marketing') ?>">
        <input type="hidden" name="sort" value="<?= esc($filters['sort']) ?>">
        <input type="hidden" name="dir" value="<?= esc($filters['dir']) ?>">
        <div class="data-filters-grid">
            <div class="data-field">
                <label for="q">Cari</label>
                <input class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>" placeholder="Judul, kategori, nama berkas">
            </div>
            <div class="data-field">
                <label for="kategori">Kategori</label>
                <select class="form-select" id="kategori" name="kategori">
                    <option value="">Semua kategori</option>
                    <?php foreach ($kategori as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['kategori'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="data-field">
                <label for="jenis">Jenis berkas</label>
                <select class="form-select" id="jenis" name="jenis">
                    <option value="">Semua jenis</option>
                    <?php foreach (['pdf' => 'PDF', 'gambar' => 'Gambar', 'video' => 'Video'] as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= $filters['jenis'] === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="data-field">
                <label for="tayang">Tayang</label>
                <select class="form-select" id="tayang" name="tayang">
                    <option value="">Semua</option>
                    <option value="1" <?= $filters['tayang'] === '1' ? 'selected' : '' ?>>Tayang</option>
                    <option value="0" <?= $filters['tayang'] === '0' ? 'selected' : '' ?>>Disembunyikan</option>
                </select>
            </div>
        </div>
        <?= view('partials/data_actions', ['perPage' => $filters['per_page']]) ?>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <?php $keep = $filters; unset($keep['page']); ?>
                    <?php foreach (['judul' => 'Judul', 'kategori' => 'Kategori', 'ukuran' => 'Ukuran', 'tayang' => 'Tayang'] as $key => $label): ?>
                        <?php $next = $filters['sort'] === $key && $filters['dir'] === 'ASC' ? 'DESC' : 'ASC'; ?>
                        <th>
                            <a class="sort-link<?= $filters['sort'] === $key ? ' is-on' : '' ?>" href="<?= esc(ndt_query_url(array_merge($keep, ['sort' => $key, 'dir' => $next]))) ?>">
                                <?= esc($label) ?>
                                <?php if ($filters['sort'] === $key): ?><i class="bi bi-chevron-<?= $filters['dir'] === 'ASC' ? 'up' : 'down' ?>"></i><?php endif ?>
                            </a>
                        </th>
                    <?php endforeach ?>
                    <th>Jenis</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($row['title']) ?><br><span class="text-secondary small"><?= esc($row['original_name']) ?></span></td>
                    <td><?= esc($row['category']) ?></td>
                    <td><?= esc(ndt_ukuran((int) $row['file_size'])) ?></td>
                    <td><?= $row['is_published'] ? 'Ya' : 'Tidak' ?></td>
                    <td><?= esc(ndt_jenis_berkas((string) $row['mime'])) ?></td>
                    <td>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/marketing/' . $row['id'] . '/lihat') ?>" target="_blank" rel="noopener">Lihat</a>
                            <form method="post" action="<?= site_url('admin/marketing/' . $row['id'] . '/tayang') ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-secondary" type="submit"><?= $row['is_published'] ? 'Sembunyikan' : 'Tayangkan' ?></button>
                            </form>
                            <form method="post" action="<?= site_url('admin/marketing/' . $row['id'] . '/hapus') ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($rows === []): ?>
            <p class="data-empty mb-0">Tidak ada berkas yang cocok dengan filter ini.</p>
        <?php endif ?>
    </div>
    <?= view('partials/data_foot', ['from' => $from, 'to' => $to, 'total' => $total, 'perPage' => $perPage, 'pager' => $pager]) ?>
</div>
<?= $this->endSection() ?>
