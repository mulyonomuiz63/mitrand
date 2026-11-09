<?php

declare(strict_types=1);

namespace App\Controllers\Agen;

use App\Controllers\BaseController;
use App\Models\KartuModel;
use App\Models\KomisiModel;
use App\Models\MitraModel;

class Dasbor extends BaseController
{
    public function index(): string
    {
        $mitra   = model(MitraModel::class)->findByUserId((int) auth()->id());
        $ringkas = $mitra && $mitra['status'] === 'active'
            ? model(KomisiModel::class)->ringkasan((int) $mitra['id'])
            : null;

        return view('agen/dasbor', [
            'title'   => 'Dasbor mitra',
            'mitra'   => $mitra,
            'kartu'   => $mitra ? model(KartuModel::class)->findByMitra((int) $mitra['id']) : null,
            'ringkas' => $ringkas,
        ]);
    }
}
