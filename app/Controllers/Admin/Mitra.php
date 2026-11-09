<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\MembershipService;
use App\Libraries\TableQuery;
use App\Models\KartuModel;
use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

class Mitra extends BaseController
{
    private const STATUSES = ['pending', 'active', 'rejected', 'suspended'];

    public function index(): string
    {
        $table   = TableQuery::current();
        $filters = [
            'q'        => $table->search(),
            'status'   => $table->oneOf('status', self::STATUSES),
            'dari'     => $table->day('dari'),
            'sampai'   => $table->day('sampai'),
            'sort'     => $table->sort(['nama' => '1', 'email' => '1', 'nomor' => '1', 'status' => '1', 'baru' => '1'], 'baru'),
            'dir'      => $table->direction(),
            'page'     => $table->page(),
            'per_page' => $table->perPage(),
        ];
        $hasil = model(MitraModel::class)->telusur($filters);
        unset($filters['page']);

        return view('admin/mitra/index', array_merge($hasil, [
            'title'   => 'Mitra',
            'filters' => $filters,
            'perPage' => $hasil['per_page'],
        ]));
    }

    public function show(int $id): string|RedirectResponse
    {
        $mitra = model(MitraModel::class)->withEmail($id);
        if (! $mitra) {
            return redirect()->to('/admin/mitra')->with('error', 'Mitra tidak ditemukan.');
        }

        return view('admin/mitra/show', [
            'title' => $mitra['full_name'],
            'mitra' => $mitra,
            'kartu' => model(KartuModel::class)->findByMitra($id),
        ]);
    }

    public function edit(int $id): string|RedirectResponse
    {
        $mitra = model(MitraModel::class)->withEmail($id);
        if (! $mitra) {
            return redirect()->to('/admin/mitra')->with('error', 'Mitra tidak ditemukan.');
        }

        return view('admin/mitra/edit', ['title' => 'Ubah data mitra', 'mitra' => $mitra]);
    }

    public function update(int $id): RedirectResponse
    {
        if (! model(MitraModel::class)->find($id)) {
            return redirect()->to('/admin/mitra')->with('error', 'Mitra tidak ditemukan.');
        }

        if (! $this->validate($this->profileRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('photo');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $this->validate([
                'photo' => 'uploaded[photo]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]|max_size[photo,2048]',
            ])) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        } else {
            $file = null;
        }

        try {
            (new MembershipService())->updateProfile($id, $this->profileData(), $file);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/admin/mitra/' . $id)->with('success', 'Data mitra diperbarui.');
    }

    public function setujui(int $id): RedirectResponse
    {
        try {
            (new MembershipService())->approve($id, (int) auth()->id());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Mitra sekarang aktif.');
    }

    public function tolak(int $id): RedirectResponse
    {
        $reason = trim((string) $this->request->getPost('reason'));
        if (mb_strlen($reason) < 3) {
            return redirect()->back()->with('error', 'Isi alasan penolakan.');
        }

        try {
            (new MembershipService())->reject($id, $reason);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pendaftaran ditolak.');
    }

    public function nonaktifkan(int $id): RedirectResponse
    {
        try {
            (new MembershipService())->suspend($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Mitra dinonaktifkan. Kartu tidak lagi lolos verifikasi.');
    }

    /**
     * @return array<string, string>
     */
    private function profileRules(): array
    {
        return [
            'full_name' => 'required|min_length[3]|max_length[150]',
            'phone'     => 'required|min_length[8]|max_length[20]|regex_match[/^[0-9+\-\s]+$/]',
            'address'   => 'required|min_length[5]|max_length[1000]',
        ];
    }

    /**
     * @return array{full_name: string, phone: string, address: string}
     */
    private function profileData(): array
    {
        return [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'phone'     => trim((string) $this->request->getPost('phone')),
            'address'   => trim((string) $this->request->getPost('address')),
        ];
    }
}
