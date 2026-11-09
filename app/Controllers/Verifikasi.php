<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\KartuModel;
use App\Libraries\CardService;

class Verifikasi extends BaseController
{
    public function show(string $token): string
    {
        $card = model(KartuModel::class)->findByToken($token);
        $aktif = $card ? (new CardService())->isPubliclyActive($card) : false;

        return view('verifikasi/show', [
            'title' => 'Verifikasi kartu',
            'card'  => $card,
            'aktif' => $aktif,
        ]);
    }
}
