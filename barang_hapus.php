<?php
require_once 'functions.php';
wajib_admin();   // staff tidak boleh hapus

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: barang.php');
    exit;
}
csrf_cek();
$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT gambar FROM barang WHERE id_barang = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row) {
    $pdo->prepare('DELETE FROM barang WHERE id_barang = ?')->execute([$id]);
    if ($row['gambar'] && file_exists(__DIR__ . '/uploads/' . $row['gambar'])) {
        unlink(__DIR__ . '/uploads/' . $row['gambar']);
    }
    set_flash('success', 'Barang berhasil dihapus.');
} else {
    set_flash('danger', 'Data barang tidak ditemukan.');
}
header('Location: barang.php');
exit;
