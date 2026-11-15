<?php
require_once 'functions.php';
wajib_login();   // admin & staff boleh tambah/ubah barang

$id = (int)($_GET['id'] ?? 0);
$f = ['kode_barang' => '', 'nama_barang' => '', 'id_kategori' => '', 'id_supplier' => '', 'harga' => '', 'stok' => '', 'gambar' => ''];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM barang WHERE id_barang = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        set_flash('danger', 'Data barang tidak ditemukan.');
        header('Location: barang.php');
        exit;
    }
    $f = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $f['kode_barang'] = trim($_POST['kode_barang'] ?? '');
    $f['nama_barang'] = trim($_POST['nama_barang'] ?? '');
    $f['id_kategori'] = (int)($_POST['id_kategori'] ?? 0);
    $f['id_supplier'] = (int)($_POST['id_supplier'] ?? 0);
    $f['harga']       = trim($_POST['harga'] ?? '');
    $f['stok']        = trim($_POST['stok'] ?? '');

    // ---- Validasi ----
    if ($f['kode_barang'] === '') {
        $errors[] = 'Kode barang wajib diisi.';
    } elseif (!preg_match('/^[A-Za-z0-9]{3,20}$/', $f['kode_barang'])) {
        $errors[] = 'Kode barang hanya huruf dan angka (3-20 karakter).';
    } else {
        // kode tidak boleh kembar
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM barang WHERE kode_barang = ? AND id_barang <> ?');
        $stmt->execute([$f['kode_barang'], $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Kode barang sudah dipakai barang lain.';
        }
    }
    if ($f['nama_barang'] === '') $errors[] = 'Nama barang wajib diisi.';
    if (!$f['id_kategori'])       $errors[] = 'Kategori wajib dipilih.';
    if (!$f['id_supplier'])       $errors[] = 'Supplier wajib dipilih.';
    if ($f['harga'] === '' || !ctype_digit($f['harga'])) $errors[] = 'Harga wajib diisi dengan angka (tanpa titik/koma).';
    if ($f['stok'] === ''  || !ctype_digit($f['stok']))  $errors[] = 'Stok wajib diisi dengan angka.';

    // ---- Upload gambar (opsional) ----
    $gambarBaru = null;
    if (!empty($_FILES['gambar']['name'])) {
        $file = $_FILES['gambar'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload gambar gagal, coba lagi.';
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $errors[] = 'Gambar harus berformat JPG atau PNG.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran gambar maksimal 2 MB.';
        } elseif (@getimagesize($file['tmp_name']) === false) {
            $errors[] = 'File yang diupload bukan gambar yang valid.';
        } else {
            $gambarBaru = uniqid('brg_') . '.' . $ext;
        }
    }

    if (!$errors) {
        if ($gambarBaru) {
            move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/uploads/' . $gambarBaru);
            // hapus gambar lama kalau ada
            if ($id && $f['gambar'] && file_exists(__DIR__ . '/uploads/' . $f['gambar'])) {
                unlink(__DIR__ . '/uploads/' . $f['gambar']);
            }
            $f['gambar'] = $gambarBaru;
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE barang SET kode_barang=?, nama_barang=?, id_kategori=?, id_supplier=?, harga=?, stok=?, gambar=? WHERE id_barang=?');
            $stmt->execute([$f['kode_barang'], $f['nama_barang'], $f['id_kategori'], $f['id_supplier'], $f['harga'], $f['stok'], $f['gambar'] ?: null, $id]);
            set_flash('success', 'Barang berhasil diubah.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_supplier, harga, stok, gambar) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$f['kode_barang'], $f['nama_barang'], $f['id_kategori'], $f['id_supplier'], $f['harga'], $f['stok'], $f['gambar'] ?: null]);
            set_flash('success', 'Barang berhasil ditambahkan.');
        }
        header('Location: barang.php');
        exit;
    }
}

$listKategori = $pdo->query('SELECT * FROM kategori ORDER BY nama_kategori')->fetchAll();
$listSupplier = $pdo->query('SELECT * FROM supplier ORDER BY nama_supplier')->fetchAll();

$judul = $id ? 'Ubah Barang' : 'Tambah Barang';
$menu  = 'barang';
require 'header.php';
?>
<h3 class="mb-3"><?= e($judul) ?></h3>
<div class="card"><div class="card-body">
    <?php foreach ($errors as $er): ?>
        <div class="alert alert-danger py-2"><?= e($er) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode_barang" class="form-control" value="<?= e($f['kode_barang']) ?>" placeholder="contoh: BRG013">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" class="form-control" value="<?= e($f['nama_barang']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="id_kategori" class="form-select">
                    <option value="">-- Pilih kategori --</option>
                    <?php foreach ($listKategori as $k): ?>
                        <option value="<?= $k['id_kategori'] ?>" <?= $f['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                <select name="id_supplier" class="form-select">
                    <option value="">-- Pilih supplier --</option>
                    <?php foreach ($listSupplier as $s): ?>
                        <option value="<?= $s['id_supplier'] ?>" <?= $f['id_supplier'] == $s['id_supplier'] ? 'selected' : '' ?>><?= e($s['nama_supplier']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                <input type="text" name="harga" class="form-control" value="<?= e($f['harga']) ?>" placeholder="contoh: 85000">
            </div>
            <div class="col-md-6">
                <label class="form-label">Stok <span class="text-danger">*</span></label>
                <input type="text" name="stok" class="form-control" value="<?= e($f['stok']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Gambar (JPG/PNG, maks 2 MB)</label>
                <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png">
                <?php if ($f['gambar']): ?>
                    <div class="mt-2"><img src="uploads/<?= e($f['gambar']) ?>" class="thumb" alt=""> <small class="text-muted">Gambar saat ini</small></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary">Simpan</button>
            <a href="barang.php" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
<?php require 'footer.php'; ?>
