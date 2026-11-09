<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\MitraModel;
use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index', ['title' => 'NDTAXANDLAW']);
    }

    public function ditolak(): RedirectResponse
    {
        if (! auth()->loggedIn()) {
            return redirect()->route('login');
        }

        $target = auth()->user()->inGroup('admin') ? '/admin' : '/agen';

        return redirect()->to($target)->with('error', 'Anda tidak memiliki akses ke halaman itu.');
    }
}
