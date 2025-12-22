<?php
// setup_db.php - initialize database and tables from db_bunaken.sql
// Use only on local/dev. It will create database db_bunaken and run schema.

$host = 'localhost';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die('Koneksi ke MySQL gagal: ' . mysqli_connect_error());
}

$sqlFile = __DIR__ . DIRECTORY_SEPARATOR . 'db_bunaken.sql';
if (!file_exists($sqlFile)) {
    die('File SQL tidak ditemukan: ' . htmlspecialchars($sqlFile));
}

$sql = file_get_contents($sqlFile);
if ($sql === false) die('Gagal membaca file SQL.');

// Jalankan multi query untuk membuat DB dan tabel
if (mysqli_multi_query($conn, $sql)) {
    echo "Sukses: Database dan tabel dibuat / diperbarui.\n";
    // flush semua hasil sampai habis
    do { /* kosongkan */ } while (mysqli_more_results($conn) && mysqli_next_result($conn));
    echo "<p>Anda sekarang dapat kembali ke <a href='index.php'>halaman utama</a>.</p>";
} else {
    echo "Error menjalankan SQL: " . mysqli_error($conn);
}

mysqli_close($conn);
?>