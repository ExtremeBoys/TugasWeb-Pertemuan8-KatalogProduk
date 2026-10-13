<?php
session_start();
require_once 'Database.php';

$pdo = Database::getInstance()->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = [
        'tipe' => 'danger',
        'pesan' => 'ID produk tidak valid.'
    ];
    header('Location: index.php');
    exit;
}

try {
    $stmtCek = $pdo->prepare("SELECT nama_produk FROM produk WHERE id = :id");
    $stmtCek->execute([':id' => $id]);
    $produk = $stmtCek->fetch();

    if (!$produk) {
        $_SESSION['flash'] = [
            'tipe' => 'danger',
            'pesan' => 'Data produk tidak ditemukan.'
        ];
        header('Location: index.php');
        exit;
    }

    $pdo->beginTransaction();

    $stmtDelete = $pdo->prepare("DELETE FROM produk WHERE id = :id");
    $stmtDelete->execute([':id' => $id]);

    $logAktivitas = 'Menghapus produk: ' . $produk['nama_produk'] . ' (ID: ' . $id . ')';
    $stmtLog = $pdo->prepare("INSERT INTO log_aktivitas (aktivitas) VALUES (:aktivitas)");
    $stmtLog->execute([':aktivitas' => $logAktivitas]);

    $pdo->commit();

    $_SESSION['flash'] = [
        'tipe' => 'success',
        'pesan' => 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus dan dicatat di log.'
    ];
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = [
        'tipe' => 'danger',
        'pesan' => 'Gagal menghapus data: ' . $e->getMessage()
    ];
}

header('Location: index.php');
exit;
