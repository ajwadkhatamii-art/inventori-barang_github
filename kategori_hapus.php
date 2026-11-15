<?php
require_once 'functions.php';
wajib_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kategori.php');
    exit;
}
csrf_cek();
$id = (int)($_POST['id'] ?? 0);

try {
    $stmt = $pdo->prepare('DELETE FROM kategori WHERE id_kategori = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Kategori berhasil dihapus.');
} catch (PDOException $e) {
    // Error 23000 = masih dipakai di tabel barang (foreign key)
    if ($e->getCode() == 23000) {
        set_flash('danger', 'Kategori gagal dihapus karena masih dipakai oleh data barang.');
    } else {
        set_flash('danger', 'Terjadi kesalahan saat menghapus data.');
    }
}
header('Location: kategori.php');
exit;
