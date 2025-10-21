<?php
// FILE: api/update_order_status.php
// Fungsi: Menerima POST request untuk mengubah status pesanan di database madriks_db.

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();

require_once '../db_config.php'; // Path ke db_config.php

// Cek hak akses admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    die(); 
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    die();
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!isset($data['order_id'], $data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input: order_id dan status diperlukan.']);
    die();
}

$orderId = intval($data['order_id']);
$newStatus = $data['status'];
$validStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];

if (!in_array($newStatus, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    die();
}

try {
    $pdo = getDB(); 
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$newStatus, $orderId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Status pesanan berhasil diperbarui.']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan atau status sudah sama.']);
    }

} catch (\PDOException $e) {
    http_response_code(500);
    error_log('Error updating order status: ' . $e->getMessage()); 
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
}

die();