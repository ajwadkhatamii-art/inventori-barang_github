<?php
// File ini di-include di semua halaman
session_start();
require_once __DIR__ . '/koneksi.php';

// Biar aman dari XSS
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

// Pesan sukses / gagal (flash message)
function set_flash($tipe, $pesan) {
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}
function tampil_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['tipe']) . ' alert-dismissible fade show" role="alert">'
           . e($f['pesan'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Cek login & role
function wajib_login() {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}
function wajib_admin() {
    wajib_login();
    if (!is_admin()) {
        set_flash('danger', 'Kamu tidak punya akses ke halaman itu (khusus admin).');
        header('Location: dashboard.php');
        exit;
    }
}

// CSRF token (biar form aman dari request palsu)
function csrf_field() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_cek() {
    if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die('Token tidak valid. Coba muat ulang halaman.');
    }
}

function rupiah($angka) {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

// Pagination (nomor halaman di bawah tabel)
function tampil_paginasi($total, $perHalaman, $halaman) {
    $jumlahHalaman = (int)ceil($total / $perHalaman);
    if ($jumlahHalaman <= 1) return;
    $params = $_GET;
    echo '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';
    for ($i = 1; $i <= $jumlahHalaman; $i++) {
        $params['hal'] = $i;
        $aktif = ($i == $halaman) ? ' active' : '';
        echo '<li class="page-item' . $aktif . '"><a class="page-link" href="?' . e(http_build_query($params)) . '">' . $i . '</a></li>';
    }
    echo '</ul></nav>';
}
