<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; }
        .card { width: 540px; height: 320px; background: #10243d; color: #f6edd9; position: relative; }
        .gold { position: absolute; left: 0; top: 0; bottom: 0; width: 10px; background: #c6a36a; }
        .body { padding: 28px 24px 24px 34px; }
        .mark { letter-spacing: 2px; font-size: 13px; color: #c6a36a; }
        h1 { margin: 18px 0 4px; font-size: 26px; }
        .meta { font-size: 13px; line-height: 1.5; }
        .photo { position: absolute; right: 150px; top: 78px; width: 86px; height: 108px; object-fit: cover; border: 2px solid #c6a36a; }
        .qr { position: absolute; right: 22px; top: 78px; width: 110px; height: 110px; background: #fff; }
        .foot { position: absolute; left: 34px; bottom: 22px; font-size: 11px; color: #d9c7a2; }
    </style>
</head>
<body>
    <div class="card">
        <div class="gold"></div>
        <div class="body">
            <div class="mark">NDTAXANDLAW</div>
            <h1><?= esc($nama) ?></h1>
            <div class="meta">
                Kartu identitas mitra<br>
                <?= esc($nomor) ?><br>
                Berlaku sampai <?= esc($berlaku) ?>
            </div>
        </div>
        <?php if ($photo): ?>
            <img class="photo" src="<?= $photo ?>" alt="">
        <?php endif ?>
        <img class="qr" src="<?= esc($qr, 'attr') ?>" alt="">
        <div class="foot">Pindai QR untuk memeriksa status keanggotaan.</div>
    </div>
</body>
</html>
