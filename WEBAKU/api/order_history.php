<?php
// FILE: api/order_history.php
// Fungsi: Mengambil riwayat pesanan berdasarkan nomor telepon pelanggan.

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Panggil file konfigurasi database
// Pastikan db_config.php ada di folder ROOT (satu level di atas)
require_once '../db_config.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    die();
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['phone'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nomor telepon wajib diisi.']);
    die();
}

$customerPhone = trim($data['phone']);

try {
    $pdo = getDB(); 
    
    // 1. Ambil semua pesanan berdasarkan nomor telepon
    // Pastikan kolom customer_phone, total, status, created_at ada di tabel orders
    $stmtOrders = $pdo->prepare('SELECT id, total, status, created_at FROM orders WHERE customer_phone = ? ORDER BY created_at DESC');
    $stmtOrders->execute([$customerPhone]);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

    $history = [];

    // 2. Untuk setiap pesanan, ambil detail itemnya
    foreach ($orders as $order) {
        // Pastikan kolom order_id, product_name, quantity, price ada di tabel order_items
        $stmtItems = $pdo->prepare('SELECT product_name, quantity, price FROM order_items WHERE order_id = ?');
        $stmtItems->execute([$order['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $order['items'] = $items;
        $history[] = $order;
    }

    echo json_encode(['success' => true, 'history' => $history]);

} catch (\PDOException $e) {
    http_response_code(500);
    error_log('Error fetching order history: ' . $e->getMessage()); 
    echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
}

die();