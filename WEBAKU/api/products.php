<?php
// FILE: api/products.php
// Fungsi: Menangani permintaan CRUD Produk di database madriks_db.

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

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

$pdo = getDB(); 

try {
    switch ($method) {
        // --- CREATE (POST) ---
        case 'POST':
            if (!isset($data['name'], $data['price'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nama dan Harga produk wajib diisi.']);
                break;
            }
            $stmt = $pdo->prepare('INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)');
            $stmt->execute([
                $data['name'], 
                $data['price'], 
                $data['description'] ?? '', 
                $data['image'] ?? 'img/default.jpg'
            ]);
            echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan.', 'id' => $pdo->lastInsertId()]);
            break;

        // --- UPDATE (PUT) ---
        case 'PUT':
            if (!isset($data['id'], $data['name'], $data['price'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID, Nama, dan Harga produk wajib diisi untuk update.']);
                break;
            }
            $stmt = $pdo->prepare('UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?');
            $stmt->execute([
                $data['name'], 
                $data['price'], 
                $data['description'] ?? '', 
                $data['image'] ?? 'img/default.jpg',
                $data['id']
            ]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Produk berhasil diperbarui.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tidak ada perubahan atau ID produk tidak ditemukan.']);
            }
            break;

        // --- DELETE (DELETE) ---
        case 'DELETE':
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID produk wajib diisi untuk hapus.']);
                break;
            }
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
            $stmt->execute([$data['id']]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Produk berhasil dihapus.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID produk tidak ditemukan.']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            break;
    }
} catch (\PDOException $e) {
    http_response_code(500);
    error_log('Error in api/products.php: ' . $e->getMessage()); 
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
}

die();