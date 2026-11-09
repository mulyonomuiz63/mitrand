<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'NDTAXANDLAW') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/logo-nd.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>?v=5">
</head>
<body>
<?php
    $path  = uri_string();
    $admin = auth()->loggedIn() && auth()->user()->inGroup('admin');
    $home  = $admin ? 'admin' : 'agen';
    $email = auth()->loggedIn() ? (string) auth()->user()->email : '';
    $links = $admin
        ? [
            ['admin', 'Dasbor', $path === 'admin', 'bi-grid-1x2'],
            ['admin/mitra', 'Mitra', str_starts_with($path, 'admin/mitra'), 'bi-people'],
            ['admin/marketing', 'Marketing', str_starts_with($path, 'admin/marketing'), 'bi-collection'],
            ['admin/komisi', 'Komisi', str_starts_with($path, 'admin/komisi'), 'bi-cash-coin'],
            ['admin/masuk-google', 'Masuk Google', str_starts_with($path, 'admin/masuk-google'), 'bi-google'],
            ['akun/sandi', 'Kata sandi', $path === 'akun/sandi', 'bi-shield-lock'],
        ]
        : [
            ['agen', 'Dasbor', $path === 'agen', 'bi-grid-1x2'],
            ['agen/profil', 'Keanggotaan', str_starts_with($path, 'agen/profil'), 'bi-person-vcard'],
            ['agen/marketing', 'Marketing', str_starts_with($path, 'agen/marketing'), 'bi-collection'],
            ['agen/komisi', 'Komisi', str_starts_with($path, 'agen/komisi'), 'bi-cash-coin'],
            ['akun/sandi', 'Kata sandi', $path === 'akun/sandi', 'bi-shield-lock'],
        ];
?>
<div class="app-shell d-lg-flex">
    <aside class="offcanvas-lg offcanvas-start aside" tabindex="-1" id="sideNav" aria-label="Menu akun">
        <div class="offcanvas-header d-lg-none">
            <a class="brand" href="<?= site_url($home) ?>"><img class="brand-logo" src="<?= base_url('assets/logo-nd.png') ?>" alt="ND Tax and Law"></a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sideNav" aria-label="Tutup menu"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <a class="aside-brand d-none d-lg-flex" href="<?= site_url($home) ?>">
                <img class="brand-logo" src="<?= base_url('assets/logo-nd.png') ?>" alt="ND Tax and Law">
            </a>
            <div class="menu-label">Menu</div>
            <nav>
                <?php foreach ($links as [$href, $label, $on, $icon]): ?>
                    <a class="menu-link<?= $on ? ' active' : '' ?>" href="<?= site_url($href) ?>">
                        <i class="bi <?= esc($icon) ?>"></i>
                        <span><?= esc($label) ?></span>
                    </a>
                <?php endforeach ?>
            </nav>
            <div class="aside-foot mt-auto">
                <form method="post" action="<?= site_url('keluar') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-light w-100" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
                </form>
            </div>
        </div>
    </aside>
    <div class="app-main flex-grow-1">
        <header class="app-header">
            <button class="icon-btn" id="sidebarToggle" type="button" aria-controls="sideNav" aria-expanded="true" aria-label="Tutup sidebar">
                <i class="bi bi-layout-sidebar-inset fs-5"></i>
            </button>
            <div class="me-auto">
                <div class="header-kicker"><?= $admin ? 'Panel admin' : 'Ruang mitra' ?></div>
                <div class="header-title"><?= esc($title ?? 'NDTAXANDLAW') ?></div>
            </div>
            <?php if ($email !== ''): ?>
                <span class="d-none d-md-inline text-secondary small text-break"><?= esc($email) ?></span>
            <?php endif ?>
        </header>
        <div class="app-content">
            <?= $this->include('partials/alerts') ?>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var shell = document.querySelector('.app-shell');
    var button = document.getElementById('sidebarToggle');
    var nav = document.getElementById('sideNav');
    if (!shell || !button || !nav) return;
    var desktop = window.matchMedia('(min-width: 992px)');
    function paint(closed) {
        var hide = closed && desktop.matches;
        shell.classList.toggle('is-sidebar-closed', hide);
        button.setAttribute('aria-expanded', hide ? 'false' : 'true');
        button.setAttribute('aria-label', hide ? 'Buka sidebar' : 'Tutup sidebar');
        var icon = button.querySelector('i');
        if (icon) icon.className = (hide ? 'bi bi-layout-sidebar' : 'bi bi-layout-sidebar-inset') + ' fs-5';
    }
    paint(localStorage.getItem('ndt-sidebar') === 'closed');
    button.addEventListener('click', function () {
        if (!desktop.matches) {
            bootstrap.Offcanvas.getOrCreateInstance(nav).toggle();
            return;
        }
        var closed = !shell.classList.contains('is-sidebar-closed');
        localStorage.setItem('ndt-sidebar', closed ? 'closed' : 'open');
        paint(closed);
    });
    desktop.addEventListener('change', function () {
        paint(localStorage.getItem('ndt-sidebar') === 'closed');
    });
    document.querySelectorAll('.data-filters').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('input, select').forEach(function (field) {
                if (field.value === '') field.disabled = true;
            });
        });
    });
})();
</script>
</body>
</html>
