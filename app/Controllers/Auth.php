<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\GoogleSettings;
use App\Libraries\MembershipService;
use App\Models\PasswordResetModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Models\UserModel;
use LogicException;
use RuntimeException;

class Auth extends BaseController
{
    public function masuk(): string|RedirectResponse
    {
        if (auth()->loggedIn()) {
            return $this->homeForCurrentUser();
        }

        return view('auth/masuk', [
            'title'  => 'Masuk',
            'google' => (new GoogleSettings())->ready(),
        ]);
    }

    public function prosesMasuk(): RedirectResponse
    {
        $credentials = [
            'email'    => (string) $this->request->getPost('email'),
            'password' => (string) $this->request->getPost('password'),
        ];

        if ($this->request->getPost('remember')) {
            auth('session')->remember();
        }

        $result = auth('session')->attempt($credentials);
        if (! $result->isOK()) {
            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi tidak sesuai.');
        }

        return $this->homeForCurrentUser();
    }

    public function sandi(): string|RedirectResponse
    {
        if (! auth()->loggedIn()) {
            return redirect()->route('login');
        }

        return view('akun/sandi', ['title' => 'Ganti kata sandi']);
    }

    public function perbaruiSandi(): RedirectResponse
    {
        if (! auth()->loggedIn()) {
            return redirect()->route('login');
        }

        $rules = [
            'current_password' => 'required',
            'password'         => 'required|min_length[8]|max_length[128]',
            'password_confirm' => 'required|matches[password]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $email  = (string) auth()->user()->email;
        $result = auth()->check([
            'email'    => $email,
            'password' => (string) $this->request->getPost('current_password'),
        ]);
        if (! $result->isOK()) {
            return redirect()->back()->with('error', 'Kata sandi sekarang tidak sesuai.');
        }

        $users          = model(UserModel::class);
        $user           = $users->findById(auth()->id());
        $user->password = (string) $this->request->getPost('password');
        if (! $users->save($user)) {
            return redirect()->back()->with('error', implode(' ', $users->errors()));
        }

        return redirect()->to('/akun/sandi')->with('success', 'Kata sandi diperbarui.');
    }

    public function keluar(): RedirectResponse
    {
        auth()->logout();

        return redirect()->route('login')->with('success', 'Anda sudah keluar.');
    }

    public function google(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            return $this->homeForCurrentUser();
        }

        $snap = (new GoogleSettings())->snapshot();
        if (! $snap['ready']) {
            return redirect()->route('login')->with('error', 'Masuk dengan Google sedang nonaktif.');
        }

        $state = bin2hex(random_bytes(16));
        session()->set('google_oauth_state', $state);

        return redirect()->to(service('googleLogin')->authorizationUrl($state, $snap['client_id']));
    }

    public function googleKembali(): RedirectResponse
    {
        $saved = (string) session('google_oauth_state');
        session()->remove('google_oauth_state');
        $state = (string) $this->request->getGet('state');

        if (auth()->loggedIn()) {
            return $this->homeForCurrentUser();
        }

        if ($saved === '' || $state === '' || ! hash_equals($saved, $state)) {
            return redirect()->route('login')->with('error', 'Sesi masuk Google tidak valid. Coba lagi.');
        }

        if ((string) $this->request->getGet('error') !== '') {
            return redirect()->route('login')->with('error', 'Masuk dengan Google dibatalkan.');
        }

        $snap = (new GoogleSettings())->snapshot();
        if (! $snap['ready']) {
            return redirect()->route('login')->with('error', 'Masuk dengan Google sedang nonaktif.');
        }

        $code = (string) $this->request->getGet('code');
        if ($code === '' || strlen($code) > 2048) {
            return redirect()->route('login')->with('error', 'Kode Google tidak lengkap.');
        }

        try {
            $profile = service('googleLogin')->profileFromCode($code, $snap['client_id'], $snap['secret']);
        } catch (RuntimeException $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }

        $user = model(UserModel::class)->findByCredentials(['email' => $profile['email']]);
        if ($user === null) {
            return redirect()->route('login')->with('error', 'Email Google belum terdaftar. Daftar sebagai mitra terlebih dahulu.');
        }

        if ($user->isNotActivated() || $user->isBanned()) {
            return redirect()->route('login')->with('error', 'Akun ini tidak dapat masuk.');
        }

        try {
            auth('session')->login($user);
        } catch (LogicException $e) {
            log_message('error', 'Masuk Google tidak selesai: {message}', ['message' => $e->getMessage()]);

            return redirect()->route('login')->with('error', 'Akun ini belum dapat masuk.');
        }

        return $this->homeForCurrentUser();
    }

    public function daftar(): string|RedirectResponse
    {
        if (auth()->loggedIn()) {
            return $this->homeForCurrentUser();
        }

        return view('auth/daftar', ['title' => 'Daftar mitra']);
    }

    public function prosesDaftar(): RedirectResponse
    {
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[150]',
            'email'     => 'required|valid_email|max_length[254]',
            'phone'     => 'required|min_length[8]|max_length[20]|regex_match[/^[0-9+\-\s]+$/]',
            'address'   => 'required|min_length[5]|max_length[1000]',
            'password'  => 'required|min_length[8]|max_length[128]',
            'password_confirm' => 'required|matches[password]',
            'photo'     => 'uploaded[photo]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]|max_size[photo,2048]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            (new MembershipService())->register([
                'full_name' => (string) $this->request->getPost('full_name'),
                'email'     => strtolower(trim((string) $this->request->getPost('email'))),
                'phone'     => trim((string) $this->request->getPost('phone')),
                'address'   => trim((string) $this->request->getPost('address')),
                'password'  => (string) $this->request->getPost('password'),
            ], $this->request->getFile('photo'));
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Pendaftaran tercatat. Masuk untuk melihat status keanggotaan. Kartu terbit setelah disetujui admin.');
    }

    public function lupa(): string
    {
        return view('auth/lupa', ['title' => 'Lupa kata sandi']);
    }

    public function prosesLupa(): RedirectResponse
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Isi email yang valid.');
        }

        $message = 'Jika email terdaftar, tautan reset disiapkan.';
        $user    = model(UserModel::class)->findByCredentials(['email' => $email]);

        if ($user) {
            $raw  = bin2hex(random_bytes(32));
            $hash = hash('sha256', $raw);
            model(PasswordResetModel::class)->where('email', $email)->delete();
            model(PasswordResetModel::class)->insert([
                'email'      => $email,
                'token_hash' => $hash,
                'expires_at' => date('Y-m-d H:i:s', time() + 3600),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $link = site_url('reset-password/' . $raw);
            $mail = service('email');
            $mail->setTo($email);
            $mail->setSubject('Reset kata sandi NDTAXANDLAW');
            $mail->setMessage("Buka tautan ini dalam 1 jam untuk mengganti kata sandi:\n{$link}");
            $mail->send(false);

            if (ENVIRONMENT === 'development') {
                $message .= ' Tautan pengembangan: ' . $link;
            }
        }

        return redirect()->back()->with('success', $message);
    }

    public function reset(string $token): string|RedirectResponse
    {
        if (! $this->findReset($token)) {
            return redirect()->to('/lupa-password')->with('error', 'Tautan reset tidak berlaku.');
        }

        return view('auth/reset', ['title' => 'Kata sandi baru', 'token' => $token]);
    }

    public function prosesReset(string $token): RedirectResponse
    {
        $row = $this->findReset($token);
        if (! $row) {
            return redirect()->to('/lupa-password')->with('error', 'Tautan reset tidak berlaku.');
        }

        $rules = [
            'password'         => 'required|min_length[8]|max_length[128]',
            'password_confirm' => 'required|matches[password]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $users = model(UserModel::class);
        $user  = $users->findByCredentials(['email' => $row['email']]);
        if (! $user) {
            return redirect()->to('/lupa-password')->with('error', 'Akun tidak ditemukan.');
        }

        $user->password = (string) $this->request->getPost('password');
        if (! $users->save($user)) {
            return redirect()->back()->with('error', implode(' ', $users->errors()));
        }

        model(PasswordResetModel::class)->delete($row['id']);

        return redirect()->route('login')->with('success', 'Kata sandi sudah diganti. Silakan masuk.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findReset(string $token): ?array
    {
        $row = model(PasswordResetModel::class)
            ->where('token_hash', hash('sha256', $token))
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        return $row ?: null;
    }

    private function homeForCurrentUser(): RedirectResponse
    {
        if (auth()->user()->inGroup('admin')) {
            return redirect()->to('/admin');
        }

        return redirect()->to('/agen');
    }
}
