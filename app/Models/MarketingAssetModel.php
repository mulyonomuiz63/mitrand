<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\TableQuery;
use CodeIgniter\Model;

class MarketingAssetModel extends Model
{
    protected $table         = 'marketing_assets';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'title',
        'description',
        'category',
        'file_path',
        'original_name',
        'mime',
        'file_size',
        'is_published',
        'created_by',
    ];
    protected $useTimestamps = true;

    /**
     * @return list<array<string, mixed>>
     */
    public function published(): array
    {
        return $this->where('is_published', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();
    }

    /**
     * @return list<string>
     */
    public function kategori(?bool $published = null): array
    {
        $builder = $this->db->table($this->table)->select('category')->distinct()->orderBy('category', 'ASC');
        if ($published === true) {
            $builder->where('is_published', 1);
        }

        return array_map(static fn (array $row): string => (string) $row['category'], $builder->get()->getResultArray());
    }

    /**
     * @param array{q: string, jenis: string, tayang: string, kategori: string, sort: string, dir: string, page: int, per_page: int, published_only: bool} $filters
     *
     * @return array{rows: list<array<string, mixed>>, pager: \CodeIgniter\Pager\Pager, total: int, from: int, to: int, page: int, per_page: int}
     */
    public function telusur(array $filters): array
    {
        $sorts = [
            'judul'    => 'title',
            'kategori' => 'category',
            'ukuran'   => 'file_size',
            'tayang'   => 'is_published',
            'baru'     => 'id',
        ];
        $builder = $this->db->table($this->table);
        if ($filters['published_only']) {
            $builder->where('is_published', 1);
        }
        if ($filters['tayang'] === '1' || $filters['tayang'] === '0') {
            $builder->where('is_published', (int) $filters['tayang']);
        }
        if ($filters['kategori'] !== '') {
            $builder->where('category', $filters['kategori']);
        }
        if ($filters['jenis'] === 'pdf') {
            $builder->where('mime', 'application/pdf');
        } elseif ($filters['jenis'] === 'gambar') {
            $builder->like('mime', 'image/', 'after');
        } elseif ($filters['jenis'] === 'video') {
            $builder->like('mime', 'video/', 'after');
        }
        if ($filters['q'] !== '') {
            $builder->groupStart()
                ->like('title', $filters['q'])
                ->orLike('category', $filters['q'])
                ->orLike('original_name', $filters['q'])
                ->orLike('description', $filters['q'])
                ->groupEnd();
        }

        return TableQuery::slice(
            $builder,
            $filters['page'],
            $filters['per_page'],
            $sorts[$filters['sort']] ?? 'id',
            $filters['dir'],
            ['q', 'jenis', 'tayang', 'kategori', 'sort', 'dir', 'per_page', 'page']
        );
    }
}
