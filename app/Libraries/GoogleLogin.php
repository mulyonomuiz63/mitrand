<?php

declare(strict_types=1);

namespace App\Libraries;

use Config\Services;
use RuntimeException;

class GoogleLogin
{
    /**
     * @param callable(string, string, array<string, mixed>): array<string, mixed>|null $transport
     */
    public function __construct(private $transport = null)
    {
    }

    public function authorizationUrl(string $state, string $clientId): string
    {
        $query = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $this->redirectUri(),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
            'access_type'   => 'online',
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $query;
    }

    public function redirectUri(): string
    {
        return site_url('masuk/google/kembali');
    }

    /**
     * @return array{email: string, name: string}
     */
    public function profileFromCode(string $code, string $clientId, string $secret): array
    {
        $token = $this->request('POST', 'https://oauth2.googleapis.com/token', [
            'form_params' => [
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $secret,
                'redirect_uri'  => $this->redirectUri(),
                'grant_type'    => 'authorization_code',
            ],
        ]);

        $access = $token['access_token'] ?? '';
        if (! is_string($access) || $access === '') {
            throw new RuntimeException('Google menolak permintaan masuk.');
        }

        $info = $this->request('GET', 'https://openidconnect.googleapis.com/v1/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access,
                'Accept'        => 'application/json',
            ],
        ]);

        $email    = strtolower(trim((string) ($info['email'] ?? '')));
        $verified = $info['email_verified'] ?? false;
        $isVerified = $verified === true || $verified === 1 || $verified === 'true';
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $isVerified) {
            throw new RuntimeException('Email Google belum terverifikasi.');
        }

        return [
            'email' => $email,
            'name'  => trim((string) ($info['name'] ?? '')),
        ];
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function request(string $method, string $url, array $options): array
    {
        if ($this->transport !== null) {
            $body = ($this->transport)($method, $url, $options);
            if (! is_array($body)) {
                throw new RuntimeException('Jawaban Google tidak dapat dibaca.');
            }

            return $body;
        }

        try {
            $client   = Services::curlrequest(['http_errors' => false, 'timeout' => 20]);
            $response = $method === 'POST' ? $client->post($url, $options) : $client->get($url, $options);
        } catch (\Throwable $e) {
            log_message('error', 'Masuk Google gagal dihubungi: {message}', ['message' => $e->getMessage()]);

            throw new RuntimeException('Google tidak dapat dihubungi. Coba lagi.');
        }

        $status = $response->getStatusCode();
        $body   = json_decode($response->getBody(), true);
        if ($status < 200 || $status >= 300 || ! is_array($body)) {
            log_message('error', 'Masuk Google ditolak dengan HTTP {status}', ['status' => $status]);

            throw new RuntimeException('Google menolak permintaan masuk.');
        }

        return $body;
    }
}
