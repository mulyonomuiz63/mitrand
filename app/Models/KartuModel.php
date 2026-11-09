<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class KartuModel extends Model
{
    protected $table            = 'membership_cards';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mitra_id',
        'card_number',
        'qr_token',
        'issued_at',
        'valid_until',
        'file_path',
        'status',
    ];
    protected $useTimestamps = true;

    public function findByMitra(int $mitraId): ?array
    {
        $row = $this->where('mitra_id', $mitraId)->first();

        return $row ?: null;
    }

    public function findByToken(string $token): ?array
    {
        $row = $this->select('membership_cards.*, mitra_profiles.full_name, mitra_profiles.member_code, mitra_profiles.status as mitra_status')
            ->join('mitra_profiles', 'mitra_profiles.id = membership_cards.mitra_id')
            ->where('membership_cards.qr_token', $token)
            ->first();

        return $row ?: null;
    }
}
