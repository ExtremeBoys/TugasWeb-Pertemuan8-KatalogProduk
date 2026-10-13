<?php
session_start();
require_once 'Database.php';

$pdo = Database::getInstance()->getConnection();

$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) {
    $halaman = 1;
}

$limit = 5;
$offset = ($halaman - 1) * $limit;

if ($keyword !== '') {
    $countSql = "SELECT COUNT(*) FROM produk p 
                 JOIN kategori k ON p.id_kategori = k.id 
                 JOIN supplier s ON p.id_supplier = s.id 
                 WHERE p.nama_produk LIKE :keyword 
                    OR k.nama_kategori LIKE :keyword 
                    OR s.nama_supplier LIKE :keyword";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([':keyword' => '%' . $keyword . '%']);
    $totalData = $countStmt->fetchColumn();

    $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier 
            FROM produk p 
            JOIN kategori k ON p.id_kategori = k.id 
            JOIN supplier s ON p.id_supplier = s.id 
            WHERE p.nama_produk LIKE :keyword 
               OR k.nama_kategori LIKE :keyword 
               OR s.nama_supplier LIKE :keyword 
            ORDER BY p.id DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produkList = $stmt->fetchAll();
} else {
    $totalData = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();

    $sql = "SELECT p.*, k.nama_kategori, s.nama_supplier 
            FROM produk p 
            JOIN kategori k ON p.id_kategori = k.id 
            JOIN supplier s ON p.id_supplier = s.id 
            ORDER BY p.id DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produkList = $stmt->fetchAll();
}

$totalHalaman = ceil($totalData / $limit);
if ($totalHalaman < 1) {
    $totalHalaman = 1;
}

$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Inventaris Barang</h1>
            <p>Manajemen data stok produk, kategori, dan supplier</p>
        </header>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash['tipe']); ?>">
                <?php echo htmlspecialchars($flash['pesan']); ?>
            </div>
        <?php endif; ?>

        <div class="toolbar">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="cari" placeholder="Cari nama, kategori, supplier..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <?php if ($keyword !== ''): ?>
                    <a href="index.php" class="btn btn-light">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produkList) > 0): ?>
                        <?php $no = $offset + 1; ?>
                        <?php foreach ($produkList as $item): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['nama_produk']); ?></strong>
                                    <?php if (!empty($item['deskripsi'])): ?>
                                        <div class="text-muted"><?php echo htmlspecialchars($item['deskripsi']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge"><?php echo htmlspecialchars($item['nama_kategori']); ?></span></td>
                                <td><?php echo htmlspecialchars($item['nama_supplier']); ?></td>
                                <td><?php echo htmlspecialchars($item['stok']); ?> unit</td>
                                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                    <a href="hapus.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?php echo htmlspecialchars(addslashes($item['nama_produk'])); ?>?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data produk yang ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalHalaman > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                    <a href="index.php?halaman=<?php echo $i; ?><?php echo $keyword !== '' ? '&cari=' . urlencode($keyword) : ''; ?>" class="page-link <?php echo $i === $halaman ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
