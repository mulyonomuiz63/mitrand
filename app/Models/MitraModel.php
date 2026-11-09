<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\TableQuery;
use CodeIgniter\Model;

class MitraModel extends Model
{
    protected $table            = 'mitra_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'member_code',
        'full_name',
        'phone',
        'address',
        'photo',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];
    protected $useTimestamps = true;

    public function findByUserId(int $userId): ?array
    {
        $row = $this->where('user_id', $userId)->first();

        return $row ?: null;
    }

    public function withEmail(int $id): ?array
    {
        $row = $this->select('mitra_profiles.*, auth_identities.secret as email')
            ->join(
                'auth_identities',
                "auth_identities.user_id = mitra_profiles.user_id AND auth_identities.type = 'email_password'",
                'left'
            )
            ->where('mitra_profiles.id', $id)
            ->first();

        return $row ?: null;
    }

    /**
     * @return array{rows: list<array<string, mixed>>, pager: \CodeIgniter\Pager\Pager}
     */
    public function daftar(?string $status): array
    {
        $this->select('mitra_profiles.*, auth_identities.secret as email')
            ->join(
                'auth_identities',
                "auth_identities.user_id = mitra_profiles.user_id AND auth_identities.type = 'email_password'",
                'left'
            );

        if ($status !== null && $status !== '') {
            $this->where('mitra_profiles.status', $status);
        }

        $rows = $this->orderBy('mitra_profiles.id', 'DESC')->paginate(12);

        return ['rows' => $rows, 'pager' => $this->pager];
    }

    /**
     * @param array{q: string, status: string, dari: string, sampai: string, sort: string, dir: string, page: int, per_page: int} $filters
     *
     * @return array{rows: list<array<string, mixed>>, pager: \CodeIgniter\Pager\Pager, total: int, from: int, to: int, page: int, per_page: int}
     */
    public function telusur(array $filters): array
    {
        $sorts = [
            'nama'   => 'mitra_profiles.full_name',
            'email'  => 'auth_identities.secret',
            'nomor'  => 'mitra_profiles.member_code',
            'status' => 'mitra_profiles.status',
            'baru'   => 'mitra_profiles.id',
        ];
        $builder = $this->db->table($this->table)
            ->select('mitra_profiles.*, auth_identities.secret as email')
            ->join(
                'auth_identities',
                "auth_identities.user_id = mitra_profiles.user_id AND auth_identities.type = 'email_password'",
                'left'
            );

        if ($filters['status'] !== '') {
            $builder->where('mitra_profiles.status', $filters['status']);
        }
        if ($filters['dari'] !== '') {
            $builder->where('mitra_profiles.created_at >=', $filters['dari'] . ' 00:00:00');
        }
        if ($filters['sampai'] !== '') {
            $builder->where('mitra_profiles.created_at <=', $filters['sampai'] . ' 23:59:59');
        }
        if ($filters['q'] !== '') {
            $builder->groupStart()
                ->like('mitra_profiles.full_name', $filters['q'])
                ->orLike('mitra_profiles.phone', $filters['q'])
                ->orLike('mitra_profiles.member_code', $filters['q'])
                ->orLike('auth_identities.secret', $filters['q'])
                ->groupEnd();
        }

        return TableQuery::slice(
            $builder,
            $filters['page'],
            $filters['per_page'],
            $sorts[$filters['sort']] ?? 'mitra_profiles.id',
            $filters['dir'],
            ['q', 'status', 'dari', 'sampai', 'sort', 'dir', 'per_page', 'page']
        );
    }
}
