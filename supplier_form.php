<?php
require_once 'functions.php';
wajib_admin();

$id     = (int)($_GET['id'] ?? 0);
$nama   = '';
$telp   = '';
$alamat = '';
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM supplier WHERE id_supplier = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        set_flash('danger', 'Data supplier tidak ditemukan.');
        header('Location: supplier.php');
        exit;
    }
    $nama   = $row['nama_supplier'];
    $telp   = $row['telepon'];
    $alamat = $row['alamat'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama   = trim($_POST['nama_supplier'] ?? '');
    $telp   = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama supplier wajib diisi.';
    }
    if ($telp === '') {
        $errors[] = 'Nomor telepon wajib diisi.';
    } elseif (!preg_match('/^[0-9]{8,15}$/', $telp)) {
        $errors[] = 'Nomor telepon harus angka saja, 8 sampai 15 digit.';
    }

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE supplier SET nama_supplier = ?, telepon = ?, alamat = ? WHERE id_supplier = ?');
            $stmt->execute([$nama, $telp, $alamat, $id]);
            set_flash('success', 'Supplier berhasil diubah.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO supplier (nama_supplier, telepon, alamat) VALUES (?, ?, ?)');
            $stmt->execute([$nama, $telp, $alamat]);
            set_flash('success', 'Supplier berhasil ditambahkan.');
        }
        header('Location: supplier.php');
        exit;
    }
}

$judul = $id ? 'Ubah Supplier' : 'Tambah Supplier';
$menu  = 'supplier';
require 'header.php';
?>
<h3 class="mb-3"><?= e($judul) ?></h3>
<div class="card"><div class="card-body">
    <?php foreach ($errors as $er): ?>
        <div class="alert alert-danger py-2"><?= e($er) ?></div>
    <?php endforeach; ?>
    <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
            <input type="text" name="nama_supplier" class="form-control" value="<?= e($nama) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon <span class="text-danger">*</span></label>
            <input type="text" name="telepon" class="form-control" value="<?= e($telp) ?>" placeholder="contoh: 081234567890">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3"><?= e($alamat) ?></textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="supplier.php" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
<?php require 'footer.php'; ?>
