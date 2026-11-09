<?php

declare(strict_types=1);

namespace App\Controllers\Agen;

use App\Controllers\BaseController;
use App\Models\KartuModel;
use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Kartu extends BaseController
{
    public function unduh(): ResponseInterface|RedirectResponse
    {
        $mitra = model(MitraModel::class)->findByUserId((int) auth()->id());
        if (! $mitra || $mitra['status'] !== 'active' || empty($mitra['member_code'])) {
            return redirect()->to('/agen')->with('error', 'Kartu identitas terbit setelah keanggotaan disetujui.');
        }

        $kartu = model(KartuModel::class)->findByMitra((int) $mitra['id']);
        if (! $kartu || $kartu['status'] !== 'active') {
            return redirect()->to('/agen')->with('error', 'Kartu belum tersedia.');
        }

        $path = WRITEPATH . 'uploads/' . $kartu['file_path'];
        if (! is_file($path)) {
            return redirect()->to('/agen')->with('error', 'Berkas kartu tidak ditemukan.');
        }

        return $this->response->download($path, null)->setFileName($mitra['member_code'] . '.pdf');
    }
}
