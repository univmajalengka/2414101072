<?php
include("koneksi.php");

if (isset($_POST['daftar'])) {

    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $jk = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $sekolah = $_POST['sekolah_asal'];

    // Prepare Statement
    $sql = "INSERT INTO calon_siswa 
        (nama, alamat, jenis_kelamin, agama, sekolah_asal)
        VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "sssss",
            $nama, $alamat, $jk, $agama, $sekolah
        );

        $execute = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        if ($execute) {
            header("Location: index.php?status=sukses");
            exit;
        } else {
            header("Location: index.php?status=gagal");
            exit;
        }

    } else {
        die("Query gagal dipersiapkan: " . mysqli_error($db));
    }

} else {
    die("Akses dilarang...");
}
?>
