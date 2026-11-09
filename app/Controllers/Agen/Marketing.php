<?php

declare(strict_types=1);

namespace App\Controllers\Agen;

use App\Controllers\BaseController;
use App\Libraries\TableQuery;
use App\Models\MarketingAssetModel;
use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Marketing extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $assets   = model(MarketingAssetModel::class);
        $kategori = $assets->kategori(true);
        $table    = TableQuery::current();
        $filters  = [
            'q'              => $table->search(),
            'jenis'          => $table->oneOf('jenis', ['pdf', 'gambar', 'video']),
            'tayang'         => '',
            'kategori'       => $table->oneOf('kategori', $kategori),
            'sort'           => $table->sort(['judul' => '1', 'kategori' => '1', 'ukuran' => '1', 'baru' => '1'], 'judul'),
            'dir'            => $table->direction('ASC'),
            'page'           => $table->page(),
            'per_page'       => $table->perPage(),
            'published_only' => true,
        ];
        $hasil = $assets->telusur($filters);
        unset($filters['page'], $filters['published_only'], $filters['tayang']);

        return view('agen/marketing', array_merge($hasil, [
            'title'    => 'Marketing tools',
            'filters'  => $filters,
            'kategori' => $kategori,
            'perPage'  => $hasil['per_page'],
        ]));
    }

    public function unduh(int $id): ResponseInterface|RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $row = model(MarketingAssetModel::class)->find($id);
        if (! $row || ! $row['is_published']) {
            return redirect()->to('/agen/marketing')->with('error', 'Berkas tidak tersedia.');
        }

        $path = WRITEPATH . 'uploads/' . $row['file_path'];
        if (! is_file($path)) {
            return redirect()->to('/agen/marketing')->with('error', 'Berkas tidak ditemukan.');
        }

        $name = basename(str_replace('\\', '/', (string) $row['original_name']));
        if ($name === '' || $name === '.' || $name === '..') {
            $name = 'berkas';
        }

        return $this->response->download($path, null)->setFileName($name);
    }

    public function lihat(int $id): ResponseInterface|RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $row = model(MarketingAssetModel::class)->find($id);
        if (! $row || ! $row['is_published']) {
            return redirect()->to('/agen/marketing')->with('error', 'Berkas tidak tersedia.');
        }

        $path = WRITEPATH . 'uploads/' . $row['file_path'];
        if (! is_file($path)) {
            return redirect()->to('/agen/marketing')->with('error', 'Berkas tidak ditemukan.');
        }

        $name = basename(str_replace('\\', '/', (string) $row['original_name']));
        if ($name === '' || $name === '.' || $name === '..') {
            $name = 'berkas';
        }

        return $this->response->download($path, null)->setFileName($name)->inline();
    }

    private function guard(): ?RedirectResponse
    {
        $mitra = model(MitraModel::class)->findByUserId((int) auth()->id());
        if (! $mitra || $mitra['status'] !== 'active') {
            return redirect()->to('/agen')->with('error', 'Marketing tools terbuka setelah keanggotaan aktif.');
        }

        return null;
    }
}
