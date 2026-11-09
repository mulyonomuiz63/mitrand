<div class="auth-logo">
    <img src="<?= base_url('assets/logo-nd.png') ?>" alt="ND Tax and Law">
</div>
<h1><?= esc($judul) ?></h1>
<?php if (($subjudul ?? '') !== ''): ?>
    <p class="auth-sub"><?= esc($subjudul) ?></p>
<?php endif ?>
