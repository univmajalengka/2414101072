<?php
// FILE: db_config.php (di root folder)

// Konfigurasi Database - Menggunakan madriks_db
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // Ganti sesuai username MySQL Anda
define('DB_PASS', '');       // Ganti sesuai password MySQL Anda
define('DB_NAME', 'madriks_db'); // Nama Database Anda

/**
 * Membuat koneksi PDO ke database madriks_db.
 * @return PDO
 */
function getDB() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
         // Koneksi database
         return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (\PDOException $e) {
         // Hentikan eksekusi dan berikan pesan error
         http_response_code(500);
         error_log('Koneksi Database Gagal: ' . $e->getMessage()); 
         die(json_encode(['success' => false, 'message' => 'Koneksi database gagal. Silakan hubungi administrator.']));
    }
}
// Hindari tag penutup PHP di akhir file