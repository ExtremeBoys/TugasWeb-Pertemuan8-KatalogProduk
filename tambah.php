<?php
session_start();
require_once 'Database.php';

$pdo = Database::getInstance()->getConnection();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
$supplierList = $pdo->query("SELECT * FROM supplier ORDER BY nama_supplier ASC")->fetchAll();

$errors = [];
$nama_produk = '';
$id_kategori = '';
$id_supplier = '';
$stok = '';
$harga = '';
$deskripsi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = trim($_POST['nama_produk'] ?? '');
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $id_supplier = (int)($_POST['id_supplier'] ?? 0);
    $stok = trim($_POST['stok'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama_produk === '') {
        $errors[] = 'Nama produk wajib diisi.';
    }
    if ($id_kategori <= 0) {
        $errors[] = 'Pilih kategori yang valid.';
    }
    if ($id_supplier <= 0) {
        $errors[] = 'Pilih supplier yang valid.';
    }
    if ($stok === '' || !is_numeric($stok) || (int)$stok < 0) {
        $errors[] = 'Stok harus berupa angka minimal 0.';
    }
    if ($harga === '' || !is_numeric($harga) || (int)$harga < 0) {
        $errors[] = 'Harga harus berupa angka minimal 0.';
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga, deskripsi) 
                    VALUES (:nama_produk, :id_kategori, :id_supplier, :stok, :harga, :deskripsi)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama_produk' => $nama_produk,
                ':id_kategori' => $id_kategori,
                ':id_supplier' => $id_supplier,
                ':stok' => (int)$stok,
                ':harga' => (int)$harga,
                ':deskripsi' => $deskripsi
            ]);

            $_SESSION['flash'] = [
                'tipe' => 'success',
                'pesan' => 'Produk baru berhasil ditambahkan.'
            ];
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Inventaris Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-sm">
        <header class="header">
            <h1>Tambah Produk Baru</h1>
            <p>Masukkan data barang inventaris ke dalam sistem</p>
        </header>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="tambah.php" method="POST">
                <div class="form-group">
                    <label for="nama_produk">Nama Produk</label>
                    <input type="text" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($nama_produk); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="id_kategori">Kategori</label>
                        <select id="id_kategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?php echo (int)$kat['id']; ?>" <?php echo (int)$id_kategori === (int)$kat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_supplier">Supplier</label>
                        <select id="id_supplier" name="id_supplier" required>
                            <option value="">-- Pilih Supplier --</option>
                            <?php foreach ($supplierList as $sup): ?>
                                <option value="<?php echo (int)$sup['id']; ?>" <?php echo (int)$id_supplier === (int)$sup['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sup['nama_supplier']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="stok">Jumlah Stok</label>
                        <input type="number" id="stok" name="stok" min="0" value="<?php echo htmlspecialchars((string)$stok); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga Satuan (Rp)</label>
                        <input type="number" id="harga" name="harga" min="0" value="<?php echo htmlspecialchars((string)$harga); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Produk (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($deskripsi); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    <a href="index.php" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
