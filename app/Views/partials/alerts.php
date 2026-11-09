<?php if (session('success')): ?>
    <div class="alert alert-success" role="status"><?= esc(session('success')) ?></div>
<?php endif ?>
<?php if (session('error')): ?>
    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
<?php endif ?>
<?php if (is_array(session('errors'))): ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach (session('errors') as $error): ?>
            <div><?= esc($error) ?></div>
        <?php endforeach ?>
    </div>
<?php endif ?>
