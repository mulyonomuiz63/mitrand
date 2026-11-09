<div class="data-actions">
    <button class="btn btn-primary" type="submit">Terapkan</button>
    <a class="btn btn-outline-secondary" href="<?= site_url(uri_string()) ?>">Reset</a>
    <label class="data-field mb-0 ms-sm-auto">
        <span class="visually-hidden">Baris per halaman</span>
        <select class="form-select" name="per_page" onchange="this.form.requestSubmit()">
            <?php foreach ([10, 25, 50] as $size): ?>
                <option value="<?= $size ?>" <?= (int) $perPage === $size ? 'selected' : '' ?>><?= $size ?> / halaman</option>
            <?php endforeach ?>
        </select>
    </label>
</div>
