<?php
require_once 'functions.php';
wajib_admin();

$id   = (int)($_GET['id'] ?? 0);
$nama = '';
$desk = '';
$errors = [];

// Mode ubah: ambil data lama
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM kategori WHERE id_kategori = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        set_flash('danger', 'Data kategori tidak ditemukan.');
        header('Location: kategori.php');
        exit;
    }
    $nama = $row['nama_kategori'];
    $desk = $row['deskripsi'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama = trim($_POST['nama_kategori'] ?? '');
    $desk = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama kategori wajib diisi.';
    } elseif (strlen($nama) > 100) {
        $errors[] = 'Nama kategori maksimal 100 karakter.';
    }

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?');
            $stmt->execute([$nama, $desk, $id]);
            set_flash('success', 'Kategori berhasil diubah.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)');
            $stmt->execute([$nama, $desk]);
            set_flash('success', 'Kategori berhasil ditambahkan.');
        }
        header('Location: kategori.php');
        exit;
    }
}

$judul = $id ? 'Ubah Kategori' : 'Tambah Kategori';
$menu  = 'kategori';
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
            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" name="nama_kategori" class="form-control" value="<?= e($nama) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= e($desk) ?></textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="kategori.php" class="btn btn-outline-secondary">Batal</a>
    </form>
</div></div>
<?php require 'footer.php'; ?>
