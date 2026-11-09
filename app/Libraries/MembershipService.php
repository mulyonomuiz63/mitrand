<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\KartuModel;
use App\Models\MitraModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use RuntimeException;

class MembershipService
{
    public function __construct(
        private readonly MitraModel $mitra = new MitraModel(),
        private readonly UserModel $users = new UserModel(),
        private readonly CardService $cards = new CardService(),
    ) {
    }

    /**
     * @param array{full_name: string, email: string, phone: string, address: string, password: string} $data
     */
    public function register(array $data, UploadedFile $photo): int
    {
        if ($this->users->findByCredentials(['email' => $data['email']])) {
            throw new RuntimeException('Email sudah terdaftar.');
        }

        $photoName = $this->storePhoto($photo);
        $db        = db_connect();
        $db->transException(true)->transStart();

        try {
            $user = new User([
                'username' => $this->uniqueUsername($data['email']),
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            if (! $this->users->save($user)) {
                throw new RuntimeException(implode(' ', $this->users->errors()));
            }

            $user = $this->users->findById($this->users->getInsertID());
            $user->activate();
            $user->addGroup('agen');

            $this->mitra->insert([
                'user_id'   => $user->id,
                'full_name' => $data['full_name'],
                'phone'     => $data['phone'],
                'address'   => $data['address'],
                'photo'     => $photoName,
                'status'    => 'pending',
            ]);

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            $this->deletePhoto($photoName);

            if ($e instanceof RuntimeException) {
                throw $e;
            }

            throw new RuntimeException('Pendaftaran gagal disimpan.', 0, $e);
        }

        return (int) $this->mitra->getInsertID();
    }

    public function approve(int $mitraId, int $adminId): void
    {
        $mitra = $this->must($mitraId);
        if (! in_array($mitra['status'], ['pending', 'rejected', 'suspended'], true)) {
            throw new RuntimeException('Mitra ini sudah aktif.');
        }

        $db = db_connect();
        $db->transException(true)->transStart();

        $code = $mitra['member_code'] ?: $this->nextCode($db);
        $this->mitra->update($mitraId, [
            'status'          => 'active',
            'member_code'     => $code,
            'approved_by'     => $adminId,
            'approved_at'     => date('Y-m-d H:i:s'),
            'rejected_reason' => null,
        ]);

        $fresh = $this->must($mitraId);
        $card  = model(KartuModel::class)->findByMitra($mitraId);
        if ($card === null) {
            $this->cards->issue($fresh);
        } else {
            model(KartuModel::class)->update($card['id'], ['status' => 'active', 'card_number' => $code]);
        }

        $db->transComplete();
    }

    public function reject(int $mitraId, string $reason): void
    {
        $mitra = $this->must($mitraId);
        if ($mitra['status'] !== 'pending') {
            throw new RuntimeException('Hanya pendaftaran yang menunggu yang dapat ditolak.');
        }

        $this->mitra->update($mitraId, [
            'status'          => 'rejected',
            'rejected_reason' => $reason,
        ]);
    }

    public function suspend(int $mitraId): void
    {
        $mitra = $this->must($mitraId);
        if ($mitra['status'] !== 'active') {
            throw new RuntimeException('Hanya mitra aktif yang dapat dinonaktifkan.');
        }

        $this->mitra->update($mitraId, ['status' => 'suspended']);
        $card = model(KartuModel::class)->findByMitra($mitraId);
        if ($card) {
            model(KartuModel::class)->update($card['id'], ['status' => 'revoked']);
        }
    }

    /**
     * @param array{full_name: string, phone: string, address: string} $data
     */
    public function updateProfile(int $mitraId, array $data, ?UploadedFile $photo): void
    {
        $mitra  = $this->must($mitraId);
        $update = [
            'full_name' => $data['full_name'],
            'phone'     => $data['phone'],
            'address'   => $data['address'],
        ];

        if ($photo instanceof UploadedFile && $photo->isValid() && ! $photo->hasMoved()) {
            $update['photo'] = $this->storePhoto($photo);
            $this->deletePhoto((string) ($mitra['photo'] ?? ''));
        }

        $this->mitra->update($mitraId, $update);
    }

    public function nextCode(?BaseConnection $db = null): string
    {
        $db     = $db ?? db_connect();
        $prefix = 'NDT-' . date('Y') . '-';
        $row    = $db->query(
            'SELECT member_code FROM mitra_profiles WHERE member_code LIKE ? ORDER BY member_code DESC LIMIT 1',
            [$prefix . '%']
        )->getRow();

        $next = 1;
        if ($row && isset($row->member_code)) {
            $next = ((int) substr((string) $row->member_code, -5)) + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, mixed>
     */
    private function must(int $id): array
    {
        $row = $this->mitra->find($id);
        if (! $row) {
            throw new RuntimeException('Data mitra tidak ditemukan.');
        }

        return $row;
    }

    private function uniqueUsername(string $email): string
    {
        $local = strstr($email, '@', true) ?: 'agen';
        $base  = preg_replace('/[^a-zA-Z0-9.]/', '', $local) ?: 'agen';
        $base  = substr($base, 0, 18);

        do {
            $username = $base . random_int(100, 999);
        } while ($this->users->where('username', $username)->first());

        return $username;
    }

    private function storePhoto(UploadedFile $photo): string
    {
        $dir = WRITEPATH . 'uploads/photos';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('Folder foto tidak dapat dibuat.');
        }

        $name = $photo->getRandomName();
        $photo->move($dir, $name);

        return 'photos/' . $name;
    }

    private function deletePhoto(string $relative): void
    {
        if ($relative === '') {
            return;
        }

        $path = WRITEPATH . 'uploads/' . ltrim($relative, '/');
        if (is_file($path)) {
            unlink($path);
        }
    }
}
