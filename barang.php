<?php
require_once 'functions.php';
wajib_login();

$cari       = trim($_GET['cari'] ?? '');
$filterKat  = (int)($_GET['kategori'] ?? 0);
$perHalaman = 5;
$halaman    = max(1, (int)($_GET['hal'] ?? 1));
$offset     = ($halaman - 1) * $perHalaman;

// Susun kondisi pencarian
$where  = '(b.nama_barang LIKE ? OR b.kode_barang LIKE ?)';
$params = ["%$cari%", "%$cari%"];
if ($filterKat) {
    $where   .= ' AND b.id_kategori = ?';
    $params[] = $filterKat;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM barang b WHERE $where");
$stmt->execute($params);
$total = $stmt->fetchColumn();

$sql = "SELECT b.*, k.nama_kategori, s.nama_supplier
        FROM barang b
        JOIN kategori k ON b.id_kategori = k.id_kategori
        JOIN supplier s ON b.id_supplier = s.id_supplier
        WHERE $where
        ORDER BY b.id_barang DESC
        LIMIT $perHalaman OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

$listKategori = $pdo->query('SELECT * FROM kategori ORDER BY nama_kategori')->fetchAll();

$judul = 'Barang';
$menu  = 'barang';
require 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Data Barang</h3>
    <a href="barang_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Barang</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-sm-6 col-md-4">
        <input type="text" name="cari" class="form-control" placeholder="Cari nama / kode barang..." value="<?= e($cari) ?>">
    </div>
    <div class="col-sm-6 col-md-3">
        <select name="kategori" class="form-select">
            <option value="0">Semua kategori</option>
            <?php foreach ($listKategori as $k): ?>
                <option value="<?= $k['id_kategori'] ?>" <?= $filterKat == $k['id_kategori'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-secondary"><i class="bi bi-search"></i> Cari</button>
        <?php if ($cari !== '' || $filterKat): ?><a href="barang.php" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>

<div class="card"><div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Gambar</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Supplier</th>
            <th>Harga</th><th>Stok</th><th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$data): ?>
        <tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>
    <?php endif; ?>
    <?php foreach ($data as $d): ?>
        <tr>
            <td>
                <?php if ($d['gambar']): ?>
                    <img src="uploads/<?= e($d['gambar']) ?>" class="thumb" alt="">
                <?php else: ?>
                    <div class="thumb d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                <?php endif; ?>
            </td>
            <td><?= e($d['kode_barang']) ?></td>
            <td><?= e($d['nama_barang']) ?></td>
            <td><?= e($d['nama_kategori']) ?></td>
            <td><?= e($d['nama_supplier']) ?></td>
            <td><?= rupiah($d['harga']) ?></td>
            <td><span class="badge bg-<?= $d['stok'] <= 5 ? 'danger' : 'success' ?>"><?= $d['stok'] ?></span></td>
            <td>
                <a href="barang_form.php?id=<?= $d['id_barang'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                <?php if (is_admin()): ?>
                <form action="barang_hapus.php" method="post" class="d-inline" onsubmit="return confirm('Yakin mau hapus barang ini?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $d['id_barang'] ?>">
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php tampil_paginasi($total, $perHalaman, $halaman); ?>
</div></div>
<?php require 'footer.php'; ?>
