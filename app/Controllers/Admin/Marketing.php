<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\TableQuery;
use App\Models\MarketingAssetModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Marketing extends BaseController
{
    public const BERKAS_RULE = 'uploaded[berkas]|ext_in[berkas,pdf,jpg,jpeg,png,mp4,webm,mov]|mime_in[berkas,application/pdf,image/jpeg,image/png,video/mp4,video/webm,video/quicktime]|max_size[berkas,32768]';

    public function index(): string
    {
        $assets   = model(MarketingAssetModel::class);
        $kategori = $assets->kategori();
        $table    = TableQuery::current();
        $filters  = [
            'q'              => $table->search(),
            'jenis'          => $table->oneOf('jenis', ['pdf', 'gambar', 'video']),
            'tayang'         => $table->oneOf('tayang', ['0', '1']),
            'kategori'       => $table->oneOf('kategori', $kategori),
            'sort'           => $table->sort(['judul' => '1', 'kategori' => '1', 'ukuran' => '1', 'tayang' => '1', 'baru' => '1'], 'baru'),
            'dir'            => $table->direction(),
            'page'           => $table->page(),
            'per_page'       => $table->perPage(),
            'published_only' => false,
        ];
        $hasil = $assets->telusur($filters);
        unset($filters['page'], $filters['published_only']);

        return view('admin/marketing/index', array_merge($hasil, [
            'title'    => 'Marketing tools',
            'filters'  => $filters,
            'kategori' => $kategori,
            'perPage'  => $hasil['per_page'],
        ]));
    }

    public function create(): string
    {
        return view('admin/marketing/form', ['title' => 'Unggah marketing tool']);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'title'       => 'required|min_length[3]|max_length[180]',
            'category'    => 'required|min_length[2]|max_length[80]',
            'description' => 'permit_empty|max_length[2000]',
            'berkas'      => self::BERKAS_RULE,
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('berkas');
        $dir  = WRITEPATH . 'uploads/marketing';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name   = $file->getRandomName();
        $size   = (int) ($file->getSize() ?: 0);
        $mime   = (string) $file->getMimeType();
        $client = $file->getClientName();
        $file->move($dir, $name);

        model(MarketingAssetModel::class)->insert([
            'title'         => trim((string) $this->request->getPost('title')),
            'category'      => trim((string) $this->request->getPost('category')),
            'description'   => trim((string) $this->request->getPost('description')),
            'file_path'     => 'marketing/' . $name,
            'original_name' => $client,
            'mime'          => $mime,
            'file_size'     => $size,
            'is_published'  => $this->request->getPost('is_published') ? 1 : 0,
            'created_by'    => (int) auth()->id(),
        ]);

        return redirect()->to('/admin/marketing')->with('success', 'Berkas marketing tersimpan.');
    }

    public function tayang(int $id): RedirectResponse
    {
        $model = model(MarketingAssetModel::class);
        $row   = $model->find($id);
        if (! $row) {
            return redirect()->to('/admin/marketing')->with('error', 'Berkas tidak ditemukan.');
        }

        $model->update($id, ['is_published' => $row['is_published'] ? 0 : 1]);

        return redirect()->back()->with('success', 'Status tayang diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $model = model(MarketingAssetModel::class);
        $row   = $model->find($id);
        if (! $row) {
            return redirect()->to('/admin/marketing')->with('error', 'Berkas tidak ditemukan.');
        }

        $path = WRITEPATH . 'uploads/' . $row['file_path'];
        if (is_file($path)) {
            unlink($path);
        }
        $model->delete($id);

        return redirect()->to('/admin/marketing')->with('success', 'Berkas dihapus.');
    }

    public function lihat(int $id): ResponseInterface|RedirectResponse
    {
        $row = model(MarketingAssetModel::class)->find($id);
        if (! $row) {
            return redirect()->to('/admin/marketing')->with('error', 'Berkas tidak ditemukan.');
        }

        return $this->tampilkan($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function tampilkan(array $row): ResponseInterface|RedirectResponse
    {
        $path = WRITEPATH . 'uploads/' . $row['file_path'];
        if (! is_file($path)) {
            return redirect()->to('/admin/marketing')->with('error', 'Berkas tidak ditemukan.');
        }

        $name = basename(str_replace('\\', '/', (string) $row['original_name']));
        if ($name === '' || $name === '.' || $name === '..') {
            $name = 'berkas';
        }

        return $this->response->download($path, null)->setFileName($name)->inline();
    }
}
