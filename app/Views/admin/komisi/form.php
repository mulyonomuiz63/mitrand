<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<?php $editing = $row !== null; ?>
<p class="kicker">Keuangan mitra</p>
<h1 class="page-title"><?= esc($title) ?></h1>
<?php if ($mitra === [] && ! $editing): ?>
    <p>Belum ada mitra aktif. Setujui pendaftaran terlebih dahulu.</p>
<?php else: ?>
<form class="form-panel" method="post" action="<?= site_url($editing ? 'admin/komisi/' . $row['id'] . '/edit' : 'admin/komisi/baru') ?>">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="mitra_id">Mitra</label>
            <select class="form-select" id="mitra_id" name="mitra_id" required>
                <?php foreach ($mitra as $item): ?>
                    <option value="<?= esc((string) $item['id']) ?>" <?= (string) old('mitra_id', $row['mitra_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>>
                        <?= esc($item['full_name']) ?> <?= $item['member_code'] ? '(' . esc($item['member_code']) . ')' : '' ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="jenis">Jenis</label>
            <select class="form-select" id="jenis" name="jenis">
                <?php foreach (['komisi' => 'Komisi', 'bonus' => 'Bonus'] as $key => $label): ?>
                    <option value="<?= $key ?>" <?= old('jenis', $row['jenis'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="nominal">Nominal</label>
            <input class="form-control" id="nominal" name="nominal" inputmode="decimal" value="<?= esc((string) old('nominal', $row['nominal'] ?? '')) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="periode">Periode (YYYY-MM)</label>
            <input class="form-control" id="periode" name="periode" placeholder="2026-11" value="<?= esc((string) old('periode', $row['periode'] ?? date('Y-m'))) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
                <?php foreach (['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'dibayar' => 'Dibayar'] as $key => $label): ?>
                    <option value="<?= $key ?>" <?= old('status', $row['status'] ?? 'pending') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" for="keterangan">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan"><?= esc((string) old('keterangan', $row['keterangan'] ?? '')) ?></textarea>
        </div>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Simpan</button>
</form>
<?php endif ?>
<?= $this->endSection() ?>
