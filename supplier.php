<?php
require_once 'functions.php';
wajib_login();

$cari       = trim($_GET['cari'] ?? '');
$perHalaman = 5;
$halaman    = max(1, (int)($_GET['hal'] ?? 1));
$offset     = ($halaman - 1) * $perHalaman;

$stmt = $pdo->prepare('SELECT COUNT(*) FROM supplier WHERE nama_supplier LIKE ? OR telepon LIKE ?');
$stmt->execute(["%$cari%", "%$cari%"]);
$total = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM supplier WHERE nama_supplier LIKE ? OR telepon LIKE ? ORDER BY id_supplier DESC LIMIT $perHalaman OFFSET $offset");
$stmt->execute(["%$cari%", "%$cari%"]);
$data = $stmt->fetchAll();

$judul = 'Supplier';
$menu  = 'supplier';
require 'header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Data Supplier</h3>
    <?php if (is_admin()): ?>
        <a href="supplier_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Supplier</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-sm-6 col-md-4">
        <input type="text" name="cari" class="form-control" placeholder="Cari nama / telepon..." value="<?= e($cari) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-secondary"><i class="bi bi-search"></i> Cari</button>
        <?php if ($cari !== ''): ?><a href="supplier.php" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>

<div class="card"><div class="card-body">
<div class="table-responsive">
<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr><th>No</th><th>Nama Supplier</th><th>Telepon</th><th>Alamat</th><?php if (is_admin()): ?><th width="150">Aksi</th><?php endif; ?></tr>
    </thead>
    <tbody>
    <?php if (!$data): ?>
        <tr><td colspan="5" class="text-center text-muted">Data tidak ditemukan.</td></tr>
    <?php endif; ?>
    <?php $no = $offset + 1; foreach ($data as $d): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= e($d['nama_supplier']) ?></td>
            <td><?= e($d['telepon']) ?></td>
            <td><?= e($d['alamat']) ?></td>
            <?php if (is_admin()): ?>
            <td>
                <a href="supplier_form.php?id=<?= $d['id_supplier'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                <form action="supplier_hapus.php" method="post" class="d-inline" onsubmit="return confirm('Yakin mau hapus supplier ini?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $d['id_supplier'] ?>">
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php tampil_paginasi($total, $perHalaman, $halaman); ?>
</div></div>
<?php require 'footer.php'; ?>
