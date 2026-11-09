<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\TableQuery;
use App\Models\KomisiModel;
use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;

class Komisi extends BaseController
{
    public function index(): string
    {
        $table   = TableQuery::current();
        $filters = [
            'q'        => $table->search(),
            'jenis'    => $table->oneOf('jenis', ['komisi', 'bonus']),
            'status'   => $table->oneOf('status', ['pending', 'disetujui', 'dibayar']),
            'dari'     => $table->month('dari'),
            'sampai'   => $table->month('sampai'),
            'sort'     => $table->sort(['mitra' => '1', 'jenis' => '1', 'periode' => '1', 'nominal' => '1', 'status' => '1', 'baru' => '1'], 'baru'),
            'dir'      => $table->direction(),
            'page'     => $table->page(),
            'per_page' => $table->perPage(),
            'mitra_id' => 0,
        ];
        $hasil = model(KomisiModel::class)->telusur($filters);
        unset($filters['page'], $filters['mitra_id']);

        return view('admin/komisi/index', array_merge($hasil, [
            'title'   => 'Komisi dan bonus',
            'filters' => $filters,
            'perPage' => $hasil['per_page'],
        ]));
    }

    public function create(): string
    {
        return view('admin/komisi/form', [
            'title' => 'Catat komisi atau bonus',
            'row'   => null,
            'mitra' => model(MitraModel::class)->where('status', 'active')->orderBy('full_name')->findAll(),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->validate($this->rules(true))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mitra = model(MitraModel::class)->find((int) $this->request->getPost('mitra_id'));
        if (! $mitra || $mitra['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Pilih mitra yang aktif.');
        }

        model(KomisiModel::class)->insert($this->payload((int) $mitra['id']));

        return redirect()->to('/admin/komisi')->with('success', 'Catatan komisi atau bonus tersimpan.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $row = model(KomisiModel::class)->find($id);
        if (! $row) {
            return redirect()->to('/admin/komisi')->with('error', 'Catatan tidak ditemukan.');
        }

        return view('admin/komisi/form', [
            'title' => 'Ubah catatan',
            'row'   => $row,
            'mitra' => model(MitraModel::class)->orderBy('full_name')->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $model = model(KomisiModel::class);
        if (! $model->find($id)) {
            return redirect()->to('/admin/komisi')->with('error', 'Catatan tidak ditemukan.');
        }

        if (! $this->validate($this->rules(false))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mitraId = (int) $this->request->getPost('mitra_id');
        if (! model(MitraModel::class)->find($mitraId)) {
            return redirect()->back()->withInput()->with('error', 'Mitra tidak ditemukan.');
        }

        $payload = $this->payload($mitraId);
        unset($payload['created_by']);
        $model->update($id, $payload);

        return redirect()->to('/admin/komisi')->with('success', 'Catatan diperbarui.');
    }

    /**
     * @return array<string, string>
     */
    private function rules(bool $creating): array
    {
        return [
            'mitra_id'   => 'required|is_natural_no_zero',
            'jenis'      => 'required|in_list[komisi,bonus]',
            'nominal'    => 'required|decimal|greater_than[0]',
            'periode'    => 'required|regex_match[/^\d{4}-\d{2}$/]',
            'status'     => 'required|in_list[pending,disetujui,dibayar]',
            'keterangan' => 'permit_empty|max_length[2000]',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(int $mitraId): array
    {
        return [
            'mitra_id'   => $mitraId,
            'jenis'      => (string) $this->request->getPost('jenis'),
            'nominal'    => (float) $this->request->getPost('nominal'),
            'periode'    => (string) $this->request->getPost('periode'),
            'status'     => (string) $this->request->getPost('status'),
            'keterangan' => trim((string) $this->request->getPost('keterangan')),
            'created_by' => (int) auth()->id(),
        ];
    }
}
