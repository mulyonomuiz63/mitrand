<div class="data-foot">
    <p class="data-count">Menampilkan <?= (int) $from ?>–<?= (int) $to ?> dari <?= (int) $total ?></p>
    <?php if ($total > $perPage): ?>
        <?= $pager->links('default', 'bootstrap_full') ?>
    <?php endif ?>
</div>
