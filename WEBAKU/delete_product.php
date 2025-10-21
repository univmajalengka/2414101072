<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: admin.php');
    exit;
}

$productId = $_GET['id'] ?? null;

if (empty($productId) || !is_numeric($productId)) {
    $_SESSION['crud_error'] = 'ID Produk tidak valid.';
    header('Location: admin.php#products');
    exit;
}

$pdo = getDB();

try {
    // Gunakan Prepared Statement untuk DELETE
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    
    // Cek apakah ada baris yang terpengaruh (terhapus)
    if ($stmt->rowCount() === 0) {
        $_SESSION['crud_error'] = 'Produk dengan ID ' . htmlspecialchars($productId) . ' tidak ditemukan.';
    }

} catch (\PDOException $e) {
    $_SESSION['crud_error'] = 'Gagal menghapus produk. Error Database: ' . $e->getMessage();
}

header('Location: admin.php#products');
exit;
?>