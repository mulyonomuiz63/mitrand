<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\GoogleLogin;
use App\Libraries\GoogleSettings;
use CodeIgniter\HTTP\RedirectResponse;

class MasukGoogle extends BaseController
{
    public function index(): string
    {
        $settings = new GoogleSettings();
        $snap     = $settings->snapshot();

        return view('admin/google/index', [
            'title'    => 'Masuk Google',
            'enabled'  => $snap['enabled'],
            'clientId' => $snap['client_id'],
            'hint'     => $settings->hint(),
            'siap'     => $snap['ready'],
            'redirect' => (new GoogleLogin())->redirectUri(),
        ]);
    }

    public function simpan(): RedirectResponse
    {
        $rules = [
            'client_id'     => 'permit_empty|max_length[200]|regex_match[/^[A-Za-z0-9._:-]*$/]',
            'client_secret' => 'permit_empty|max_length[300]',
        ];
        if (! $this->validate($rules)) {
            $this->lupakanRahasia();

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clientId = trim((string) $this->request->getPost('client_id'));
        $secret   = trim((string) $this->request->getPost('client_secret'));
        $enabled  = (bool) $this->request->getPost('enabled');
        $settings = new GoogleSettings();
        $punya    = $secret !== '' || $settings->snapshot()['secret'] !== '';

        if ($enabled && ($clientId === '' || ! $punya)) {
            $this->lupakanRahasia();

            return redirect()->back()->withInput()->with('error', 'Isi Client ID dan Client Secret sebelum mengaktifkan masuk dengan Google.');
        }

        $settings->save($clientId, $secret, $enabled);

        return redirect()->to('/admin/masuk-google')->with('success', 'Pengaturan masuk Google tersimpan. Rahasia tidak ditampilkan kembali.');
    }

    private function lupakanRahasia(): void
    {
        $post = $this->request->getPost();
        if (! is_array($post)) {
            return;
        }

        $post['client_secret'] = '';
        $this->request->setGlobal('post', $post);
    }
}
