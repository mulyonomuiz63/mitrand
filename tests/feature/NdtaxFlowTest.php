<?php

declare(strict_types=1);

use App\Controllers\Admin\Marketing;
use App\Libraries\GoogleLogin;
use App\Libraries\MembershipService;
use App\Models\SettingModel;
use App\Models\KartuModel;
use App\Models\KomisiModel;
use App\Models\MarketingAssetModel;
use App\Models\MitraModel;
use App\Models\PasswordResetModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\TestUpload;

/**
 * @internal
 */
final class NdtaxFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = null;

    protected $seed = 'App\Database\Seeds\AdminSeeder';

    private const ADMIN_EMAIL = 'kantor@ndtaxandlaw.local';

    private const ADMIN_PASSWORD = 'Pelindung#Kartu88';

    public function testGuestIsSentToLoginAndPublicPageHidesUnknownCards(): void
    {
        $admin = $this->callAs(null, 'GET', 'admin');
        $admin->assertRedirect();
        $this->assertStringContainsString('masuk', (string) $admin->getRedirectUrl());

        $agen = $this->callAs(null, 'GET', 'agen/komisi');
        $agen->assertRedirect();
        $this->assertStringContainsString('masuk', (string) $agen->getRedirectUrl());

        $missing = $this->callAs(null, 'GET', 'verifikasi/token-tidak-ada');
        $missing->assertOK();
        $missing->assertSee('Data kartu tidak ditemukan');
    }

    public function testApprovalIssuesCardAndAgentCannotOpenAdminOrOtherCommission(): void
    {
        $first  = $this->registerMitra('Sari Pajak', 'sari@example.test', '08111111111', 'Jl. Melati 1');
        $second = $this->registerMitra('Budi Hukum', 'budi@example.test', '08222222222', 'Jl. Mawar 2');

        $pendingCard = $this->callAs($first['session'], 'GET', 'agen/kartu');
        $pendingCard->assertRedirect();
        $this->assertStringContainsString('agen', (string) $pendingCard->getRedirectUrl());
        $this->dontSeeInDatabase('membership_cards', ['mitra_id' => $first['id']]);

        $denied = $this->callAs($first['session'], 'GET', 'admin/mitra');
        $denied->assertRedirect();
        $this->assertStringContainsString('akses-ditolak', (string) $denied->getRedirectUrl());

        $admin = $this->login(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $approved = $this->callAs($admin, 'POST', 'admin/mitra/' . $first['id'] . '/setujui');
        $approved->assertRedirect();

        $card = model(KartuModel::class)->findByMitra($first['id']);
        $this->assertNotNull($card);
        $this->assertSame('active', $card['status']);
        $this->assertMatchesRegularExpression('/^NDT-\d{4}-\d{5}$/', (string) $card['card_number']);
        $this->assertFileExists(WRITEPATH . 'uploads/' . $card['file_path']);

        $public = $this->callAs(null, 'GET', 'verifikasi/' . $card['qr_token']);
        $public->assertOK();
        $public->assertSee('Sari Pajak');
        $public->assertSee($card['card_number']);
        $public->assertSee('Aktif');
        $public->assertDontSee('08111111111');
        $public->assertDontSee('sari@example.test');
        $public->assertDontSee('Jl. Melati');

        model(KomisiModel::class)->insert([
            'mitra_id'   => $first['id'],
            'jenis'      => 'komisi',
            'nominal'    => 150000,
            'periode'    => '2026-11',
            'keterangan' => 'CATATAN-SARI',
            'status'     => 'disetujui',
            'created_by' => 1,
        ]);
        model(KomisiModel::class)->insert([
            'mitra_id'   => $second['id'],
            'jenis'      => 'bonus',
            'nominal'    => 500000,
            'periode'    => '2026-11',
            'keterangan' => 'CATATAN-BUDI-RAHASIA',
            'status'     => 'dibayar',
            'created_by' => 1,
        ]);

        $own = $this->callAs($first['session'], 'GET', 'agen/komisi');
        $own->assertOK();
        $own->assertSee('CATATAN-SARI');
        $own->assertDontSee('CATATAN-BUDI-RAHASIA');

        $foreign = $this->callAs($second['session'], 'GET', 'agen/komisi');
        $foreign->assertRedirect();
    }

    public function testOnlyActiveAgentsDownloadPublishedFiles(): void
    {
        $mitra = $this->registerMitra('Lina Mitra', 'lina@example.test', '08333333333', 'Jl. Kenanga 3');
        $admin = $this->login(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $this->callAs($admin, 'POST', 'admin/mitra/' . $mitra['id'] . '/setujui')->assertRedirect();

        $dir = WRITEPATH . 'uploads/marketing';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir . '/tayang.txt', 'ISI-BROSUR-TAYANG');
        file_put_contents($dir . '/arsip.txt', 'ISI-BROSUR-ARSIP');

        $assets = model(MarketingAssetModel::class);
        $liveId = $assets->insert([
            'title'         => 'Brosur tayang',
            'category'      => 'Brosur',
            'description'   => 'Untuk mitra',
            'file_path'     => 'marketing/tayang.txt',
            'original_name' => 'brosur-tayang.txt',
            'mime'          => 'text/plain',
            'file_size'     => 16,
            'is_published'  => 1,
            'created_by'    => 1,
        ], true);
        $hiddenId = $assets->insert([
            'title'         => 'Brosur arsip',
            'category'      => 'Brosur',
            'description'   => 'Belum tayang',
            'file_path'     => 'marketing/arsip.txt',
            'original_name' => 'brosur-arsip.txt',
            'mime'          => 'text/plain',
            'file_size'     => 16,
            'is_published'  => 0,
            'created_by'    => 1,
        ], true);

        $guest = $this->callAs(null, 'GET', 'agen/marketing/' . $liveId . '/unduh');
        $guest->assertRedirect();

        $hidden = $this->callAs($mitra['session'], 'GET', 'agen/marketing/' . $hiddenId . '/unduh');
        $hidden->assertRedirect();

        $live = $this->callAs($mitra['session'], 'GET', 'agen/marketing/' . $liveId . '/unduh');
        $live->assertStatus(200);
        $download = $live->response();
        $download->buildHeaders();
        $this->assertStringContainsString('brosur-tayang.txt', $download->getHeaderLine('Content-Disposition'));
        $file = (new ReflectionObject($download))->getProperty('file');
        $this->assertStringContainsString('ISI-BROSUR-TAYANG', (string) file_get_contents($file->getValue($download)->getPathname()));
    }

    public function testMarketingAcceptsVideo(): void
    {
        $this->assertStringContainsString('video/mp4', Marketing::BERKAS_RULE);
        $this->assertStringContainsString('video/webm', Marketing::BERKAS_RULE);
        $this->assertStringContainsString('application/pdf', Marketing::BERKAS_RULE);
        $this->assertStringContainsString('image/jpeg', Marketing::BERKAS_RULE);
        $this->assertStringContainsString('image/png', Marketing::BERKAS_RULE);

        $admin  = $this->login(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $upload = $this->callAs($admin, 'GET', 'admin/marketing/baru');
        $upload->assertOK();
        $upload->assertSee('MP4');
        $upload->assertSee('WEBM');
        $upload->assertDontSee('Asisten');
        $upload->assertDontSee('Koneksi AI');
    }

    public function testAdminTablesFilterOnTheServer(): void
    {
        $sari = $this->registerMitra('Sari Pajak', 'sari@example.test', '08111111111', 'Jl. Melati 1');
        $budi = $this->registerMitra('Budi Hukum', 'budi@example.test', '08222222222', 'Jl. Mawar 2');
        $admin = $this->login(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);

        $dash = $this->callAs($admin, 'GET', 'admin');
        $dash->assertOK();
        $dash->assertSee('Tutup sidebar');
        $dash->assertSee('sidebarToggle');

        $all = $this->callAs($admin, 'GET', 'admin/mitra');
        $all->assertOK();
        $all->assertSee('Sari Pajak');
        $all->assertSee('Budi Hukum');
        $all->assertSee('data-card');

        $found = $this->callAs($admin, 'GET', 'admin/mitra?q=' . rawurlencode('Sari Pajak'));
        $found->assertOK();
        $found->assertSee('Sari Pajak');
        $found->assertDontSee('Budi Hukum');

        for ($i = 1; $i <= 9; $i++) {
            $this->registerMitra('Agen ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'agen' . $i . '@example.test', '081000000' . $i, 'Jl. Uji ' . $i);
        }
        $page = $this->callAs($admin, 'GET', 'admin/mitra?sort=nama&dir=ASC&per_page=10&page=2');
        $page->assertOK();
        $body = $page->getBody();
        $this->assertStringContainsString('Sari Pajak', $body);
        $this->assertStringContainsString('Menampilkan 11', $body);
        $this->assertStringNotContainsString('Agen 01', $body);
        $this->assertStringNotContainsString('Budi Hukum', $body);

        $junk = $this->callAs($admin, 'GET', 'admin/mitra?sort=' . rawurlencode('nama;drop') . '&dir=SIDEWAYS&status=hacked');
        $junk->assertOK();
        $junk->assertSee('Agen 09');
        $junk->assertDontSee('nama;drop');

        model(\App\Models\KomisiModel::class)->insert([
            'mitra_id'   => $sari['id'],
            'jenis'      => 'komisi',
            'nominal'    => 150000,
            'periode'    => '2026-11',
            'keterangan' => 'CATATAN-SARI',
            'status'     => 'disetujui',
            'created_by' => 1,
        ]);
        model(\App\Models\KomisiModel::class)->insert([
            'mitra_id'   => $budi['id'],
            'jenis'      => 'bonus',
            'nominal'    => 500000,
            'periode'    => '2026-10',
            'keterangan' => 'CATATAN-BUDI-RAHASIA',
            'status'     => 'dibayar',
            'created_by' => 1,
        ]);

        $money = $this->callAs($admin, 'GET', 'admin/komisi?q=CATATAN-SARI&jenis=komisi&dari=2026-11&sampai=2026-11');
        $money->assertOK();
        $money->assertSee('CATATAN-SARI');
        $money->assertDontSee('CATATAN-BUDI-RAHASIA');

        $files = $this->callAs($admin, 'GET', 'admin/marketing');
        $files->assertOK();
        $files->assertSee('Jenis berkas');
        $files->assertSee('data-card');
    }

    public function testAuthScreensShareTheSameShellAndGoogleStaysOff(): void
    {
        $masuk = $this->callAs(null, 'GET', 'masuk');
        $masuk->assertOK();
        $masuk->assertSee('Selamat Datang Kembali');
        $masuk->assertSee('Ingat saya');
        $masuk->assertSee('Lupa Kata Sandi?');
        $masuk->assertSee('logo-nd.png');
        $masuk->assertDontSee('Masuk dengan Google');

        $daftar = $this->callAs(null, 'GET', 'daftar');
        $daftar->assertOK();
        $daftar->assertSee('Daftar Akun Mitra');
        $daftar->assertSee('logo-nd.png');

        $lupa = $this->callAs(null, 'GET', 'lupa-password');
        $lupa->assertOK();
        $lupa->assertSee('Lupa Kata Sandi');
        $lupa->assertSee('logo-nd.png');

        $token = bin2hex(random_bytes(16));
        model(PasswordResetModel::class)->insert([
            'email'      => self::ADMIN_EMAIL,
            'token_hash' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $reset = $this->callAs(null, 'GET', 'reset-password/' . $token);
        $reset->assertOK();
        $reset->assertSee('Atur Ulang Kata Sandi');
        $reset->assertSee('logo-nd.png');

        $blocked = $this->callAs(null, 'GET', 'masuk/google');
        $blocked->assertRedirect();
        $this->assertStringContainsString('masuk', (string) $blocked->getRedirectUrl());
        $this->assertStringNotContainsString('accounts.google.com', (string) $blocked->getRedirectUrl());

        $admin = $this->login(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $form  = $this->callAs($admin, 'GET', 'admin/masuk-google');
        $form->assertOK();
        $form->assertSee('Aktifkan masuk dengan Google');
        $form->assertSee('masuk/google/kembali');

        $denied = $this->callAs($admin, 'POST', 'admin/masuk-google', [
            'client_id' => 'uji-client.apps.googleusercontent.com',
            'enabled'   => '1',
        ]);
        $denied->assertRedirect();
        $again = $this->callAs($admin, 'GET', 'masuk');
        $again->assertDontSee('Masuk dengan Google');
    }

    public function testGoogleLoginSignsInOnlyARegisteredEmail(): void
    {
        $mitra = $this->registerMitra('Sari Pajak', 'sari@example.test', '08111111111', 'Jl. Melati 1');
        $admin = $this->login(self::ADMIN_EMAIL, self::ADMIN_PASSWORD);
        $saved = $this->callAs($admin, 'POST', 'admin/masuk-google', [
            'client_id'     => 'uji-client.apps.googleusercontent.com',
            'client_secret' => 'rahasia-google-uji',
            'enabled'       => '1',
        ]);
        $saved->assertRedirect();

        $page = $this->callAs($admin, 'GET', 'admin/masuk-google');
        $page->assertOK();
        $page->assertDontSee('rahasia-google-uji');
        $stored = model(SettingModel::class)->value('google_client_secret');
        $this->assertIsString($stored);
        $this->assertStringNotContainsString('rahasia-google-uji', $stored);

        $login = $this->callAs(null, 'GET', 'masuk');
        $login->assertSee('Masuk dengan Google');

        $start = $this->callAs(null, 'GET', 'masuk/google');
        $start->assertRedirect();
        $target = (string) $start->getRedirectUrl();
        $this->assertStringContainsString('accounts.google.com', $target);
        $this->assertStringContainsString('uji-client.apps.googleusercontent.com', $target);

        $bad = $this->callAs(['google_oauth_state' => 'state-benar'], 'GET', 'masuk/google/kembali?code=kode&state=state-salah');
        $bad->assertRedirect();
        $this->assertStringNotContainsString('/agen', (string) $bad->getRedirectUrl());

        Services::injectMock('googleLogin', new GoogleLogin(static function (string $method): array {
            if ($method === 'POST') {
                return ['access_token' => 'token-uji'];
            }

            return [
                'email'          => 'tidak-ada@example.test',
                'email_verified' => true,
                'name'           => 'Tamu',
            ];
        }));
        $before = model(UserModel::class)->countAllResults();
        $unknown = $this->callAs(['google_oauth_state' => 'state-uji'], 'GET', 'masuk/google/kembali?code=kode-uji&state=state-uji');
        $unknown->assertRedirect();
        $this->assertStringContainsString('masuk', (string) $unknown->getRedirectUrl());
        $this->assertStringNotContainsString('/agen', (string) $unknown->getRedirectUrl());
        $this->assertSame($before, model(UserModel::class)->countAllResults());

        Services::injectMock('googleLogin', new GoogleLogin(static function (string $method): array {
            if ($method === 'POST') {
                return ['access_token' => 'token-uji'];
            }

            return [
                'email'          => 'sari@example.test',
                'email_verified' => true,
                'name'           => 'Sari Pajak',
            ];
        }));
        $ok = $this->callAs(['google_oauth_state' => 'state-sari'], 'GET', 'masuk/google/kembali?code=kode-sari&state=state-sari');
        $ok->assertRedirect();
        $this->assertStringContainsString('/agen', (string) $ok->getRedirectUrl());
        $this->assertSame($mitra['user_id'], (int) ($_SESSION['user']['id'] ?? 0));

        $unverified = new GoogleLogin(static function (string $method): array {
            if ($method === 'POST') {
                return ['access_token' => 'token-uji'];
            }

            return [
                'email'          => 'sari@example.test',
                'email_verified' => false,
            ];
        });
        try {
            $unverified->profileFromCode('kode', 'id', 'secret');
            $this->fail('Email belum terverifikasi harus ditolak.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('belum terverifikasi', $e->getMessage());
        }

        Services::resetSingle('googleLogin');
    }

    /**
     * @return array{id: int, user_id: int, session: array<string, mixed>}
     */
    private function registerMitra(string $name, string $email, string $phone, string $address): array
    {
        $png  = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $path = WRITEPATH . 'cache/uji-' . md5($email) . '.png';
        file_put_contents($path, $png);

        $upload = new TestUpload($path, 'foto.png', 'image/png', strlen($png), UPLOAD_ERR_OK);
        $id     = (new MembershipService())->register([
            'full_name' => $name,
            'email'     => $email,
            'phone'     => $phone,
            'address'   => $address,
            'password'  => 'Rahasia#Mitra88',
        ], $upload);

        $mitra = model(MitraModel::class)->find($id);
        $this->assertSame('pending', $mitra['status']);
        $this->assertNull($mitra['member_code']);

        return [
            'id'      => (int) $id,
            'user_id' => (int) $mitra['user_id'],
            'session' => $this->login($email, 'Rahasia#Mitra88'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function login(string $email, string $password): array
    {
        $result = $this->callAs(null, 'POST', 'masuk', [
            'email'    => $email,
            'password' => $password,
        ]);
        $result->assertRedirect();
        $this->assertArrayHasKey('user', $_SESSION);

        return $_SESSION;
    }

    /**
     * @param array<string, mixed>|null $session
     */
    private function callAs(?array $session, string $method, string $path, array $params = [])
    {
        Services::resetSingle('auth');
        Services::resetSingle('security');

        $session ??= [];
        if ($method === 'POST') {
            $hash                        = bin2hex(random_bytes(16));
            $session['csrf_test_name']   = $hash;
            $params['csrf_test_name']    = $hash;
        }

        $this->withSession($session);

        return $method === 'POST' ? $this->post($path, $params) : $this->get($path);
    }
}
