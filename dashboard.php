<?php
require_once 'functions.php';
wajib_login();

// Ambil data ringkasan
$totalBarang   = $pdo->query('SELECT COUNT(*) FROM barang')->fetchColumn();
$totalKategori = $pdo->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$totalSupplier = $pdo->query('SELECT COUNT(*) FROM supplier')->fetchColumn();
$totalStok     = $pdo->query('SELECT COALESCE(SUM(stok),0) FROM barang')->fetchColumn();
$nilaiStok     = $pdo->query('SELECT COALESCE(SUM(harga*stok),0) FROM barang')->fetchColumn();

// Barang stok menipis (<= 5)
$menipis = $pdo->query(
    'SELECT b.*, k.nama_kategori FROM barang b
     JOIN kategori k ON b.id_kategori = k.id_kategori
     WHERE b.stok <= 5 ORDER BY b.stok ASC'
)->fetchAll();

// Jumlah barang per kategori
$perKategori = $pdo->query(
    'SELECT k.nama_kategori, COUNT(b.id_barang) AS jumlah
     FROM kategori k LEFT JOIN barang b ON b.id_kategori = k.id_kategori
     GROUP BY k.id_kategori ORDER BY jumlah DESC'
)->fetchAll();

$judul = 'Dashboard';
$menu  = 'dashboard';
require 'header.php';
?>
<h3 class="mb-1">Halo, <?= e($_SESSION['user']['nama']) ?> 👋</h3>
<p class="text-muted">Ini ringkasan data inventori hari ini.</p>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Total Barang</div>
            <div class="angka text-primary"><?= $totalBarang ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Kategori</div>
            <div class="angka text-success"><?= $totalKategori ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Supplier</div>
            <div class="angka text-warning"><?= $totalSupplier ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card"><div class="card-body">
            <div class="text-muted">Total Stok</div>
            <div class="angka text-danger"><?= number_format($totalStok, 0, ',', '.') ?></div>
        </div></div>
    </div>
</div>

<div class="card mb-4"><div class="card-body">
    <div class="text-muted">Estimasi nilai seluruh stok</div>
    <div class="angka h3 mb-0"><?= rupiah($nilaiStok) ?></div>
</div></div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card"><div class="card-body">
            <h5 class="card-title"><i class="bi bi-exclamation-triangle text-danger"></i> Stok Menipis (&le; 5)</h5>
            <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th></tr></thead>
                <tbody>
                <?php if (!$menipis): ?>
                    <tr><td colspan="4" class="text-center text-muted">Aman, tidak ada stok yang menipis.</td></tr>
                <?php endif; ?>
                <?php foreach ($menipis as $m): ?>
                    <tr>
                        <td><?= e($m['kode_barang']) ?></td>
                        <td><?= e($m['nama_barang']) ?></td>
                        <td><?= e($m['nama_kategori']) ?></td>
                        <td><span class="badge bg-danger"><?= $m['stok'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <h5 class="card-title"><i class="bi bi-tags"></i> Barang per Kategori</h5>
            <ul class="list-group list-group-flush">
                <?php foreach ($perKategori as $p): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <?= e($p['nama_kategori']) ?>
                        <span class="badge bg-primary rounded-pill"><?= $p['jumlah'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div></div>
    </div>
</div>
<?php require 'footer.php'; ?>
