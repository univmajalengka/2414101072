<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "db_bunaken";

// Try to connect and catch mysqli_sql_exception to show a helpful message if DB is missing
try {
    // Use exceptions for mysqli so we can catch them; we will handle them explicitly
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $koneksi = mysqli_connect($host, $user, $pass, $db);
} catch (mysqli_sql_exception $e) {
    $errMsg = $e->getMessage();
    // Jika database belum dibuat, beri petunjuk yang jelas
    if (stripos($errMsg, 'Unknown database') !== false) {
        die("Koneksi gagal: Database '<strong>" . htmlspecialchars($db) . "</strong>' belum dibuat. <a href='setup_db.php'>Klik di sini untuk membuat database dan tabel (setup_db.php)</a>.<br>Detail error: " . htmlspecialchars($errMsg));
    }
    die("Koneksi gagal: " . htmlspecialchars($errMsg));
}
?>