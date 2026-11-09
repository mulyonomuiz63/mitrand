<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table         = 'app_settings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'setting_key',
        'setting_value',
    ];
    protected $useTimestamps = true;

    public function value(string $key): ?string
    {
        $row = $this->where('setting_key', $key)->first();
        if (! $row) {
            return null;
        }

        return $row['setting_value'] === null ? null : (string) $row['setting_value'];
    }

    public function put(string $key, string $value): void
    {
        $row = $this->where('setting_key', $key)->first();
        if ($row) {
            $this->update($row['id'], ['setting_value' => $value]);

            return;
        }

        $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
        ]);
    }
}
