<?php
// proses_hapus.php
include 'koneksi.php';

$id = $_GET['id'];
$id_bersih = mysqli_real_escape_string($koneksi, $id); 

// 1. Ambil email pelanggan sebelum data dihapus
$query_ambil_email = mysqli_query($koneksi, "SELECT email FROM t_pemesanan WHERE id='$id_bersih'");
$data_email = mysqli_fetch_array($query_ambil_email);
$email_pelanggan = $data_email ? $data_email['email'] : 'index.php'; // Default jika gagal

// 2. Hapus data
$query = "DELETE FROM t_pemesanan WHERE id='$id_bersih'";

if (mysqli_query($koneksi, $query)) {
    // Jika ada param 'return=modifikasi', arahkan ke halaman admin modifikasi
    $return_page = (isset($_GET['return']) && $_GET['return'] === 'modifikasi') ? 'modifikasi_pesanan.php' : 'riwayat_pemesanan.php?email='.$email_pelanggan;
    echo "<script>
            alert('Pemesanan Berhasil Dibatalkan (Dihapus)!'); 
            window.location='$return_page';
          </script>";
} else {
    echo "Error delete: " . mysqli_error($koneksi);
}
?>