<?php
// proses_edit.php
include 'koneksi.php';

$id          = $_POST['id'];
$nama        = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nomor_hp    = mysqli_real_escape_string($koneksi, $_POST['nomor_hp']);
$email       = mysqli_real_escape_string($koneksi, $_POST['email']);
$paket       = mysqli_real_escape_string($koneksi, $_POST['paket']);
$harga_paket = (int)$_POST['harga_paket']; 
$tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
$pesan       = mysqli_real_escape_string($koneksi, $_POST['pesan']);

// Layanan & perhitungan
$lama_perjalanan = isset($_POST['lama_perjalanan']) ? (int)$_POST['lama_perjalanan'] : 1;
$jumlah_peserta  = isset($_POST['jumlah_peserta']) ? (int)$_POST['jumlah_peserta'] : 1;
$lay_penginapan  = isset($_POST['layanan_penginapan']) ? 1 : 0;
$lay_transport   = isset($_POST['layanan_transportasi']) ? 1 : 0;
$lay_makan       = isset($_POST['layanan_makan']) ? 1 : 0;
$harga_paket_perjalanan = isset($_POST['harga_paket_perjalanan']) ? (int)$_POST['harga_paket_perjalanan'] : 0;
$jumlah_tagihan = (int)$harga_paket + (int)$harga_paket_perjalanan * (int)$lama_perjalanan * (int)$jumlah_peserta;

// KOLOM BARU: Ambil nilai checkbox lama jika ada
$alat_menyelam = isset($_POST['alat_menyelam']) ? (int)$_POST['alat_menyelam'] : 0;
$pendamping    = isset($_POST['pendamping']) ? (int)$_POST['pendamping'] : 0;

$query = "UPDATE t_pemesanan SET 
          nama='$nama', 
          nomor_hp='$nomor_hp',
          email='$email', 
          paket='$paket', 
          harga_paket='$harga_paket',
          layanan_penginapan='$lay_penginapan',
          layanan_transportasi='$lay_transport',
          layanan_makan='$lay_makan',
          harga_paket_perjalanan='$harga_paket_perjalanan',
          lama_perjalanan='$lama_perjalanan',
          jumlah_peserta='$jumlah_peserta',
          jumlah_tagihan='$jumlah_tagihan',
          alat_menyelam='$alat_menyelam',
          pendamping='$pendamping',
          tanggal='$tanggal', 
          pesan='$pesan' 
          WHERE id='$id'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>
            alert('Data Berhasil Diupdate!'); 
            window.location='riwayat_pemesanan.php?email=$email';
          </script>";
} else {
    echo "Error update: " . mysqli_error($koneksi);
}
?>