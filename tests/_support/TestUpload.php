<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Upload palsu untuk pengujian CLI. move_uploaded_file() hanya berlaku pada unggahan HTTP.
 */
class TestUpload extends UploadedFile
{
    public function move(string $targetPath, ?string $name = null, bool $overwrite = false): bool
    {
        $targetPath = rtrim($targetPath, '/\\') . DIRECTORY_SEPARATOR;
        if (! is_dir($targetPath) && ! mkdir($targetPath, 0755, true) && ! is_dir($targetPath)) {
            return false;
        }

        $name ??= $this->getClientName();
        $from = $this->getRealPath() ?: $this->getPathname();

        return copy($from, $targetPath . $name);
    }
}
