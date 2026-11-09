<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\KartuModel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;

class CardService
{
    public function __construct(private readonly KartuModel $kartu = new KartuModel())
    {
    }

    /**
     * @param array<string, mixed> $mitra
     */
    public function issue(array $mitra): void
    {
        if (empty($mitra['member_code'])) {
            throw new RuntimeException('Nomor anggota belum terbit.');
        }

        $token = bin2hex(random_bytes(16));
        $until = date('Y-m-d', strtotime('+2 years'));
        $file  = 'cards/' . $mitra['member_code'] . '.pdf';
        $this->writePdf($mitra, $token, $until, $file);

        $existing = $this->kartu->findByMitra((int) $mitra['id']);
        $payload  = [
            'mitra_id'    => $mitra['id'],
            'card_number' => $mitra['member_code'],
            'qr_token'    => $token,
            'issued_at'   => date('Y-m-d H:i:s'),
            'valid_until' => $until,
            'file_path'   => $file,
            'status'      => 'active',
        ];

        if ($existing) {
            $this->kartu->update($existing['id'], $payload);
        } else {
            $this->kartu->insert($payload);
        }
    }

    public function isPubliclyActive(array $card): bool
    {
        return ($card['mitra_status'] ?? '') === 'active'
            && ($card['status'] ?? '') === 'active'
            && ($card['valid_until'] ?? '') >= date('Y-m-d');
    }

    /**
     * @param array<string, mixed> $mitra
     */
    private function writePdf(array $mitra, string $token, string $until, string $relative): void
    {
        $dir = WRITEPATH . 'uploads/cards';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('Folder kartu tidak dapat dibuat.');
        }

        $verify = base_url('verifikasi/' . $token);
        $qr     = (new QRCode(new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64'    => true,
            'scale'           => 5,
        ])))->render($verify);

        $photo = $this->photoDataUri((string) ($mitra['photo'] ?? ''));
        $html  = view('kartu/pdf', [
            'nama'   => (string) $mitra['full_name'],
            'nomor'  => (string) $mitra['member_code'],
            'berlaku'=> date('d M Y', strtotime($until)),
            'qr'     => $qr,
            'photo'  => $photo,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 540, 320]);
        $dompdf->render();

        if (file_put_contents(WRITEPATH . 'uploads/' . $relative, $dompdf->output()) === false) {
            throw new RuntimeException('Berkas kartu gagal disimpan.');
        }
    }

    private function photoDataUri(string $relative): ?string
    {
        if ($relative === '') {
            return null;
        }

        $path = WRITEPATH . 'uploads/' . ltrim($relative, '/');
        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }
}
