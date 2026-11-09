<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\MitraModel;
use CodeIgniter\HTTP\ResponseInterface;

class Berkas extends BaseController
{
    public function foto(int $id): ResponseInterface
    {
        $mitra = model(MitraModel::class)->find($id);
        $user  = auth()->user();

        if (! $mitra || ! $user) {
            return $this->response->setStatusCode(404);
        }

        $allowed = $user->inGroup('admin') || (int) $mitra['user_id'] === (int) $user->id;
        if (! $allowed || empty($mitra['photo'])) {
            return $this->response->setStatusCode(403);
        }

        $path = WRITEPATH . 'uploads/' . $mitra['photo'];
        if (! is_file($path)) {
            return $this->response->setStatusCode(404);
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody((string) file_get_contents($path));
    }
}
