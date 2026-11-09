<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<p class="kicker">Marketing</p>
<h1 class="page-title mb-3">Softcopy untuk mitra</h1>
<div class="data-card">
    <form class="data-filters" method="get" action="<?= site_url('agen/marketing') ?>">
        <input type="hidden" name="sort" value="<?= esc($filters['sort']) ?>">
        <input type="hidden" name="dir" value="<?= esc($filters['dir']) ?>">
        <div class="data-filters-grid">
            <div class="data-field">
                <label for="q">Cari</label>
                <input class="form-control" id="q" name="q" value="<?= esc($filters['q']) ?>" placeholder="Judul atau kategori">
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
        </div>
        <?= view('partials/data_actions', ['perPage' => $filters['per_page']]) ?>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Jenis</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($row['title']) ?><br><span class="text-secondary small"><?= esc($row['description']) ?></span></td>
                    <td><?= esc($row['category']) ?></td>
                    <td><?= esc(ndt_jenis_berkas((string) $row['mime'])) ?><br><span class="text-secondary small"><?= esc(ndt_ukuran((int) $row['file_size'])) ?></span></td>
                    <td>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('agen/marketing/' . $row['id'] . '/lihat') ?>" target="_blank" rel="noopener">Lihat</a>
                            <a class="btn btn-sm btn-primary" href="<?= site_url('agen/marketing/' . $row['id'] . '/unduh') ?>">Unduh</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($rows === []): ?>
            <p class="data-empty mb-0">Belum ada berkas yang ditayangkan.</p>
        <?php endif ?>
    </div>
    <?= view('partials/data_foot', ['from' => $from, 'to' => $to, 'total' => $total, 'perPage' => $perPage, 'pager' => $pager]) ?>
</div>
<?= $this->endSection() ?>
