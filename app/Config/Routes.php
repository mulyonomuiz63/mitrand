<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('verifikasi/(:segment)', 'Verifikasi::show/$1');
$routes->get('akses-ditolak', 'Home::ditolak');
$routes->get('berkas/foto/(:num)', 'Berkas::foto/$1', ['filter' => 'session']);

$routes->get('masuk', 'Auth::masuk', ['as' => 'login']);
$routes->post('masuk', 'Auth::prosesMasuk');
$routes->get('masuk/google', 'Auth::google');
$routes->get('masuk/google/kembali', 'Auth::googleKembali');
$routes->post('keluar', 'Auth::keluar');
$routes->get('akun/sandi', 'Auth::sandi', ['filter' => 'session']);
$routes->post('akun/sandi', 'Auth::perbaruiSandi', ['filter' => 'session']);
$routes->get('daftar', 'Auth::daftar');
$routes->post('daftar', 'Auth::prosesDaftar');
$routes->get('lupa-password', 'Auth::lupa');
$routes->post('lupa-password', 'Auth::prosesLupa');
$routes->get('reset-password/(:segment)', 'Auth::reset/$1');
$routes->post('reset-password/(:segment)', 'Auth::prosesReset/$1');

$routes->group('admin', ['filter' => 'group:admin'], static function ($routes) {
    $routes->get('/', 'Admin\Dasbor::index');
    $routes->get('mitra', 'Admin\Mitra::index');
    $routes->get('mitra/(:num)', 'Admin\Mitra::show/$1');
    $routes->get('mitra/(:num)/edit', 'Admin\Mitra::edit/$1');
    $routes->post('mitra/(:num)/edit', 'Admin\Mitra::update/$1');
    $routes->post('mitra/(:num)/setujui', 'Admin\Mitra::setujui/$1');
    $routes->post('mitra/(:num)/tolak', 'Admin\Mitra::tolak/$1');
    $routes->post('mitra/(:num)/nonaktifkan', 'Admin\Mitra::nonaktifkan/$1');
    $routes->get('marketing', 'Admin\Marketing::index');
    $routes->get('marketing/baru', 'Admin\Marketing::create');
    $routes->post('marketing/baru', 'Admin\Marketing::store');
    $routes->post('marketing/(:num)/tayang', 'Admin\Marketing::tayang/$1');
    $routes->post('marketing/(:num)/hapus', 'Admin\Marketing::hapus/$1');
    $routes->get('marketing/(:num)/lihat', 'Admin\Marketing::lihat/$1');
    $routes->get('komisi', 'Admin\Komisi::index');
    $routes->get('komisi/baru', 'Admin\Komisi::create');
    $routes->post('komisi/baru', 'Admin\Komisi::store');
    $routes->get('komisi/(:num)/edit', 'Admin\Komisi::edit/$1');
    $routes->post('komisi/(:num)/edit', 'Admin\Komisi::update/$1');
    $routes->get('masuk-google', 'Admin\MasukGoogle::index');
    $routes->post('masuk-google', 'Admin\MasukGoogle::simpan');
});

$routes->group('agen', ['filter' => 'group:agen'], static function ($routes) {
    $routes->get('/', 'Agen\Dasbor::index');
    $routes->get('profil', 'Agen\Profil::index');
    $routes->post('profil', 'Agen\Profil::update');
    $routes->get('password', 'Agen\Profil::password');
    $routes->post('password', 'Agen\Profil::updatePassword');
    $routes->get('kartu', 'Agen\Kartu::unduh');
    $routes->get('marketing', 'Agen\Marketing::index');
    $routes->get('marketing/(:num)/unduh', 'Agen\Marketing::unduh/$1');
    $routes->get('marketing/(:num)/lihat', 'Agen\Marketing::lihat/$1');
    $routes->get('komisi', 'Agen\Komisi::index');
});
