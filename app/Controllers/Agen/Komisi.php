<?php

declare(strict_types=1);

namespace App\Controllers\Agen;

use App\Controllers\BaseController;
use App\Libraries\TableQuery;
use App\Models\KomisiModel;
use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;

class Komisi extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $mitra = model(MitraModel::class)->findByUserId((int) auth()->id());
        if (! $mitra || $mitra['status'] !== 'active') {
            return redirect()->to('/agen')->with('error', 'Komisi dan bonus dapat dilihat setelah keanggotaan aktif.');
        }

        $id      = (int) $mitra['id'];
        $table   = TableQuery::current();
        $filters = [
            'q'        => $table->search(),
            'jenis'    => $table->oneOf('jenis', ['komisi', 'bonus']),
            'status'   => $table->oneOf('status', ['pending', 'disetujui', 'dibayar']),
            'dari'     => $table->month('dari'),
            'sampai'   => $table->month('sampai'),
            'sort'     => $table->sort(['jenis' => '1', 'periode' => '1', 'nominal' => '1', 'status' => '1', 'baru' => '1'], 'periode'),
            'dir'      => $table->direction(),
            'page'     => $table->page(),
            'per_page' => $table->perPage(),
            'mitra_id' => $id,
        ];
        $hasil = model(KomisiModel::class)->telusur($filters);
        unset($filters['page'], $filters['mitra_id']);

        return view('agen/komisi', array_merge($hasil, [
            'title'   => 'Komisi dan bonus',
            'ringkas' => model(KomisiModel::class)->ringkasan($id),
            'filters' => $filters,
            'perPage' => $hasil['per_page'],
        ]));
    }
}
