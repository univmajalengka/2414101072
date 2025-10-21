<?php
session_start();

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: admin.php');
    exit;
}

// Helper functions (Duplikat dari admin.php)
function loadOrders() {
    $file = __DIR__ . '/data/orders.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true) ?? [];
    }
    return [];
}
function saveData($data, $type) {
    $file = __DIR__ . '/data/orders.json';
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $json) !== false;
}
// END Helper functions

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $orderIdToUpdate = $_POST['order_id'] ?? '';
    $newStatus = $_POST['new_status'] ?? '';
    $validStatuses = ['Pending', 'Processing', 'Delivered', 'Cancelled'];

    $orders = loadOrders();
    $found = false;

    if (in_array($newStatus, $validStatuses) && !empty($orderIdToUpdate)) {
        foreach ($orders as $key => $order) {
            if ($order['id'] === $orderIdToUpdate) {
                $orders[$key]['status'] = $newStatus;
                $found = true;
                break;
            }
        }

        if ($found) {
            if (saveData($orders, 'orders')) {
                header('Location: admin.php#orders');
                exit;
            } else {
                $error = 'Gagal menyimpan perubahan status order.';
            }
        } else {
            $error = 'ID Pesanan tidak ditemukan.';
        }
    } else {
        $error = 'Data status atau ID pesanan tidak valid.';
    }
    
    // Jika ada error, simpan pesan error ke session dan redirect kembali ke admin
    if (!empty($error)) {
        $_SESSION['crud_error'] = $error;
        header('Location: admin.php#orders');
        exit;
    }
} else {
    // Jika diakses tanpa POST data yang valid
    $_SESSION['crud_error'] = 'Akses tidak valid.';
    header('Location: admin.php');
    exit;
}
?>