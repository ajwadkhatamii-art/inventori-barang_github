<?php
// Variabel yang dipakai: $judul (judul halaman), $menu (menu aktif)
$menu = $menu ?? '';
$u = $_SESSION['user'];
function aktif($nama, $menu) { return $nama === $menu ? ' active' : ''; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul ?? 'Inventori') ?> - Inventori Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-box-seam"></i> Inventori Barang</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuUtama">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link<?= aktif('dashboard', $menu) ?>" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link<?= aktif('barang', $menu) ?>" href="barang.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link<?= aktif('kategori', $menu) ?>" href="kategori.php">Kategori</a></li>
                <li class="nav-item"><a class="nav-link<?= aktif('supplier', $menu) ?>" href="supplier.php">Supplier</a></li>
            </ul>
            <span class="navbar-text me-3">
                <i class="bi bi-person-circle"></i> <?= e($u['nama']) ?>
                <span class="badge bg-<?= $u['role'] === 'admin' ? 'warning text-dark' : 'info text-dark' ?>"><?= e($u['role']) ?></span>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm" onclick="return confirm('Yakin mau logout?')">Logout</a>
        </div>
    </div>
</nav>
<div class="container pb-5">
<?php tampil_flash(); ?>
