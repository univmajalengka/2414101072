<?php
header('Content-Type: application/json');

// Sertakan konfigurasi database
require_once '../db_config.php';

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Ambil data JSON dari request body
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validasi data yang diterima
if (!isset($data['name'], $data['phone'], $data['cart'], $data['total']) || !is_array($data['cart']) || empty($data['cart'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

$customer_name = trim($data['name']);
$customer_phone = trim($data['phone']);
$cart = $data['cart'];
$total = floatval($data['total']); // Pastikan total adalah float/numeric

$pdo = getDB();

try {
    $pdo->beginTransaction();

    // 1. Masukkan data pesanan ke tabel 'orders'
    $stmt = $pdo->prepare('INSERT INTO orders (customer_name, customer_phone, total, status) VALUES (?, ?, ?, "Pending")');
    $stmt->execute([$customer_name, $customer_phone, $total]);
    
    // Ambil ID pesanan yang baru saja dibuat
    $order_id = $pdo->lastInsertId();

    // 2. Masukkan detail produk ke tabel 'order_items'
    $item_stmt = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, price, quantity) 
         VALUES (?, ?, ?, ?, ?)'
    );

    foreach ($cart as $item) {
        $item_stmt->execute([
            $order_id,
            $item['id'], // ID produk (dari data-id di index.php)
            $item['name'],
            $item['price'],
            $item['qty']
        ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil diproses. ID: ' . $order_id]);

} catch (\PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    // Log error di server, jangan tampilkan detail error ke customer
    error_log('Checkout error: ' . $e->getMessage()); 
    echo json_encode(['success' => false, 'message' => 'Gagal memproses pesanan. Silakan coba lagi.']);
}
?>