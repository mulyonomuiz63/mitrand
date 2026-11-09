<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MarketingAssetModel;
use App\Models\MitraModel;

class Dasbor extends BaseController
{
    public function index(): string
    {
        return view('admin/dasbor', [
            'title'     => 'Dasbor admin',
            'total'     => model(MitraModel::class)->countAllResults(),
            'pending'   => model(MitraModel::class)->where('status', 'pending')->countAllResults(),
            'active'    => model(MitraModel::class)->where('status', 'active')->countAllResults(),
            'marketing' => model(MarketingAssetModel::class)->where('is_published', 1)->countAllResults(),
            'komisi'    => (float) (db_connect()->query('SELECT COALESCE(SUM(nominal),0) AS total FROM komisi_bonus')->getRow()->total ?? 0),
        ]);
    }
}
