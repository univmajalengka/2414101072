<?php
// FILE: api/upload_image.php
// Fungsi: Mengunggah file gambar ke folder img/ dan mengembalikan path-nya.

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();

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

if (!isset($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tidak ada file gambar yang diupload.']);
    die();
}

$file = $_FILES['image'];
$target_dir = "../img/"; // Target direktori (folder img di root)

// Pastikan folder img/ ada
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0777, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal membuat folder upload. Cek izin folder.']);
        die();
    }
}

$imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$newFileName = uniqid('prod_', true) . '.' . $imageFileType;
$target_file = $target_dir . $newFileName;
$uploadOk = 1;

// Cek apakah file adalah gambar asli
$check = getimagesize($file["tmp_name"]);
if($check === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File bukan gambar.']);
    $uploadOk = 0;
}

// Batasi tipe file
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.']);
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    // Error sudah ditangani di atas
} else {
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $relativePath = 'img/' . $newFileName; 
        echo json_encode(['success' => true, 'message' => 'Gambar berhasil diunggah.', 'path' => $relativePath]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Terjadi error saat memindahkan file. Cek izin tulis folder img/.']);
    }
}

die();