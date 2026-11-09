<?php

declare(strict_types=1);

namespace App\Controllers\Agen;

use App\Controllers\BaseController;
use App\Libraries\MembershipService;
use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Models\UserModel;
use RuntimeException;

class Profil extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $mitra = $this->mitra();
        if (! $mitra) {
            return redirect()->to('/agen')->with('error', 'Profil mitra tidak ditemukan.');
        }

        return view('agen/profil', [
            'title' => 'Keanggotaan',
            'mitra' => $mitra,
            'email' => auth()->user()->email,
        ]);
    }

    public function update(): RedirectResponse
    {
        $mitra = $this->mitra();
        if (! $mitra) {
            return redirect()->to('/agen')->with('error', 'Profil mitra tidak ditemukan.');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[150]',
            'phone'     => 'required|min_length[8]|max_length[20]|regex_match[/^[0-9+\-\s]+$/]',
            'address'   => 'required|min_length[5]|max_length[1000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid()) {
            if (! $this->validate([
                'photo' => 'is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]|max_size[photo,2048]',
            ])) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        } else {
            $file = null;
        }

        try {
            (new MembershipService())->updateProfile((int) $mitra['id'], [
                'full_name' => trim((string) $this->request->getPost('full_name')),
                'phone'     => trim((string) $this->request->getPost('phone')),
                'address'   => trim((string) $this->request->getPost('address')),
            ], $file);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/agen/profil')->with('success', 'Data diri diperbarui. Nomor anggota dan status tidak berubah.');
    }

    public function password(): string
    {
        return view('agen/password', ['title' => 'Ganti kata sandi']);
    }

    public function updatePassword(): RedirectResponse
    {
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

        $users = model(UserModel::class);
        $user  = $users->findById(auth()->id());
        $user->password = (string) $this->request->getPost('password');
        if (! $users->save($user)) {
            return redirect()->back()->with('error', implode(' ', $users->errors()));
        }

        return redirect()->to('/agen/password')->with('success', 'Kata sandi diperbarui.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mitra(): ?array
    {
        return model(MitraModel::class)->findByUserId((int) auth()->id());
    }
}
