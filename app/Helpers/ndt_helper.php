<?php

declare(strict_types=1);

if (! function_exists('ndt_rupiah')) {
    function ndt_rupiah(float|int|string|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('ndt_status_mitra')) {
    function ndt_status_mitra(string $status): string
    {
        return match ($status) {
            'pending'   => 'Menunggu persetujuan',
            'active'    => 'Aktif',
            'rejected'  => 'Ditolak',
            'suspended' => 'Nonaktif',
            default     => $status,
        };
    }
}

if (! function_exists('ndt_status_komisi')) {
    function ndt_status_komisi(string $status): string
    {
        return match ($status) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'dibayar'   => 'Dibayar',
            default     => $status,
        };
    }
}

if (! function_exists('ndt_badge')) {
    function ndt_badge(string $status): string
    {
        return match ($status) {
            'active', 'disetujui', 'dibayar' => 'text-bg-success',
            'pending'                        => 'text-bg-warning',
            'rejected', 'suspended'          => 'text-bg-danger',
            default                          => 'text-bg-secondary',
        };
    }
}

if (! function_exists('ndt_jenis')) {
    function ndt_jenis(string $jenis): string
    {
        return $jenis === 'bonus' ? 'Bonus' : 'Komisi';
    }
}

if (! function_exists('ndt_ukuran')) {
    function ndt_ukuran(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }
}

if (! function_exists('ndt_query_url')) {
    /**
     * @param array<string, mixed> $params
     */
    function ndt_query_url(array $params): string
    {
        $clean = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $clean[$key] = $value;
        }

        $path = site_url(uri_string());

        return $clean === [] ? $path : $path . '?' . http_build_query($clean);
    }
}

if (! function_exists('ndt_jenis_berkas')) {
    function ndt_jenis_berkas(string $mime): string
    {
        if (str_starts_with($mime, 'video/')) {
            return 'Video';
        }
        if (str_starts_with($mime, 'image/')) {
            return 'Gambar';
        }
        if ($mime === 'application/pdf') {
            return 'PDF';
        }

        return 'Berkas';
    }
}
