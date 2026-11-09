<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\SettingModel;
use Config\Services;

class GoogleSettings
{
    public function __construct(private readonly SettingModel $settings = new SettingModel())
    {
    }

    /**
     * @return array{enabled: bool, client_id: string, secret: string, ready: bool}
     */
    public function snapshot(): array
    {
        $clientId = trim((string) ($this->settings->value('google_client_id') ?? ''));
        $secret   = $this->decrypt((string) ($this->settings->value('google_client_secret') ?? ''));
        $enabled  = $this->settings->value('google_enabled') === '1';

        return [
            'enabled'   => $enabled,
            'client_id' => $clientId,
            'secret'    => $secret,
            'ready'     => $enabled && $clientId !== '' && $secret !== '',
        ];
    }

    public function ready(): bool
    {
        return $this->snapshot()['ready'];
    }

    public function hint(): string
    {
        $secret = $this->snapshot()['secret'];
        if ($secret === '') {
            return '';
        }

        return mb_substr($secret, -4);
    }

    public function save(string $clientId, string $newSecret, bool $enabled): void
    {
        $secret = trim($newSecret) !== '' ? trim($newSecret) : $this->snapshot()['secret'];

        $this->settings->put('google_client_id', trim($clientId));
        $this->settings->put('google_enabled', $enabled ? '1' : '0');
        if ($secret !== '') {
            $this->settings->put('google_client_secret', $this->encrypt($secret));
        }
    }

    private function encrypt(string $plain): string
    {
        return base64_encode(Services::encrypter()->encrypt($plain));
    }

    private function decrypt(string $stored): string
    {
        if ($stored === '') {
            return '';
        }

        try {
            $plain = Services::encrypter()->decrypt(base64_decode($stored, true) ?: '');

            return is_string($plain) ? $plain : '';
        } catch (\Throwable) {
            return '';
        }
    }
}
