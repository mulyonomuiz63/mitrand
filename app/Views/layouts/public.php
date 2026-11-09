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
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>?v=4">
</head>
<body>
    <nav class="navbar navbar-expand-md public-nav sticky-top">
        <div class="container">
            <a class="navbar-brand brand d-flex align-items-center" href="<?= site_url('/') ?>">
                <img class="brand-logo" src="<?= base_url('assets/logo-nd.png') ?>" alt="ND Tax and Law">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPublik" aria-controls="menuPublik" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuPublik">
                <ul class="navbar-nav ms-auto align-items-md-center gap-md-2 py-3 py-md-0">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('daftar') ?>">Daftar mitra</a></li>
                    <li class="nav-item"><a class="btn btn-primary px-3" href="<?= site_url('masuk') ?>">Masuk</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <?= $this->renderSection('content') ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
