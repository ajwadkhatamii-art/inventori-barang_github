<?php
require_once 'functions.php';
wajib_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: supplier.php');
    exit;
}
csrf_cek();
$id = (int)($_POST['id'] ?? 0);

try {
    $stmt = $pdo->prepare('DELETE FROM supplier WHERE id_supplier = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Supplier berhasil dihapus.');
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        set_flash('danger', 'Supplier gagal dihapus karena masih dipakai oleh data barang.');
    } else {
        set_flash('danger', 'Terjadi kesalahan saat menghapus data.');
    }
}
header('Location: supplier.php');
exit;
