<?php
session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: index.php');
    exit;
}

// Helper functions (Duplikat dari admin.php)
function loadReviews() {
    $file = __DIR__ . '/data/reviews.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true) ?? [];
    }
    return [];
}
function saveData($data, $type) {
    $file = __DIR__ . '/data/reviews.json';
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $json) !== false;
}
// END Helper functions

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin.php#reviews-list');
    exit;
}

$id = intval($_GET['id']);

$reviews = loadReviews();

// Hapus review berdasarkan index
if (isset($reviews[$id])) {
    array_splice($reviews, $id, 1);
    if (!saveData($reviews, 'reviews')) {
        $_SESSION['crud_error'] = 'Gagal menghapus review dari file.';
    }
} else {
    $_SESSION['crud_error'] = 'ID Review tidak ditemukan.';
}

header('Location: admin.php#reviews-list');
exit;
?>