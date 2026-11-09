<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\TableQuery;
use CodeIgniter\Model;

class KomisiModel extends Model
{
    protected $table         = 'komisi_bonus';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'mitra_id',
        'jenis',
        'nominal',
        'periode',
        'keterangan',
        'status',
        'created_by',
    ];
    protected $useTimestamps = true;

    /**
     * @return array{komisi: array<string, float>, bonus: array<string, float>}
     */
    public function ringkasan(int $mitraId): array
    {
        $out = [
            'komisi' => ['pending' => 0.0, 'disetujui' => 0.0, 'dibayar' => 0.0],
            'bonus'  => ['pending' => 0.0, 'disetujui' => 0.0, 'dibayar' => 0.0],
        ];

        foreach ($this->where('mitra_id', $mitraId)->findAll() as $row) {
            $jenis  = (string) $row['jenis'];
            $status = (string) $row['status'];
            if (isset($out[$jenis][$status])) {
                $out[$jenis][$status] += (float) $row['nominal'];
            }
        }

        return $out;
    }

    /**
     * @return array{komisi: array<string, float>, bonus: array<string, float>}
     */
    public function ringkasanSemua(): array
    {
        $out = [
            'komisi' => ['pending' => 0.0, 'disetujui' => 0.0, 'dibayar' => 0.0],
            'bonus'  => ['pending' => 0.0, 'disetujui' => 0.0, 'dibayar' => 0.0],
        ];

        $rows = $this->builder()
            ->select('jenis, status, SUM(nominal) AS total')
            ->groupBy('jenis, status')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $jenis  = (string) $row['jenis'];
            $status = (string) $row['status'];
            if (isset($out[$jenis][$status])) {
                $out[$jenis][$status] += (float) $row['total'];
            }
        }

        return $out;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function riwayatMitra(int $mitraId): array
    {
        return $this->where('mitra_id', $mitraId)
            ->orderBy('periode', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * @param array{q: string, jenis: string, status: string, dari: string, sampai: string, sort: string, dir: string, page: int, per_page: int, mitra_id: int} $filters
     *
     * @return array{rows: list<array<string, mixed>>, pager: \CodeIgniter\Pager\Pager, total: int, from: int, to: int, page: int, per_page: int}
     */
    public function telusur(array $filters): array
    {
        $sorts = [
            'mitra'   => 'mitra_profiles.full_name',
            'jenis'   => 'komisi_bonus.jenis',
            'periode' => 'komisi_bonus.periode',
            'nominal' => 'komisi_bonus.nominal',
            'status'  => 'komisi_bonus.status',
            'baru'    => 'komisi_bonus.id',
        ];
        $builder = $this->db->table($this->table)
            ->select('komisi_bonus.*, mitra_profiles.full_name, mitra_profiles.member_code')
            ->join('mitra_profiles', 'mitra_profiles.id = komisi_bonus.mitra_id');

        if ($filters['mitra_id'] > 0) {
            $builder->where('komisi_bonus.mitra_id', $filters['mitra_id']);
        }
        if ($filters['jenis'] !== '') {
            $builder->where('komisi_bonus.jenis', $filters['jenis']);
        }
        if ($filters['status'] !== '') {
            $builder->where('komisi_bonus.status', $filters['status']);
        }
        if ($filters['dari'] !== '') {
            $builder->where('komisi_bonus.periode >=', $filters['dari']);
        }
        if ($filters['sampai'] !== '') {
            $builder->where('komisi_bonus.periode <=', $filters['sampai']);
        }
        if ($filters['q'] !== '') {
            $builder->groupStart()
                ->like('mitra_profiles.full_name', $filters['q'])
                ->orLike('mitra_profiles.member_code', $filters['q'])
                ->orLike('komisi_bonus.keterangan', $filters['q'])
                ->groupEnd();
        }

        return TableQuery::slice(
            $builder,
            $filters['page'],
            $filters['per_page'],
            $sorts[$filters['sort']] ?? 'komisi_bonus.id',
            $filters['dir'],
            ['q', 'jenis', 'status', 'dari', 'sampai', 'sort', 'dir', 'per_page', 'page']
        );
    }
}
