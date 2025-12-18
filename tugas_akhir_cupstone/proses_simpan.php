<?php
// proses_simpan.php
include 'koneksi.php';

$nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nomor_hp   = mysqli_real_escape_string($koneksi, $_POST['nomor_hp']);
$email      = mysqli_real_escape_string($koneksi, $_POST['email']);
$paket      = mysqli_real_escape_string($koneksi, $_POST['paket']);
$harga_paket= (int)$_POST['harga_paket']; // Pastikan ini integer
$tanggal    = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
$pesan      = mysqli_real_escape_string($koneksi, $_POST['pesan']);

// New fields
$lama_perjalanan = isset($_POST['lama_perjalanan']) ? (int)$_POST['lama_perjalanan'] : 1;
$jumlah_peserta  = isset($_POST['jumlah_peserta']) ? (int)$_POST['jumlah_peserta'] : 1;
$lay_penginapan  = isset($_POST['layanan_penginapan']) ? 1 : 0;
$lay_transport   = isset($_POST['layanan_transportasi']) ? 1 : 0;
$lay_makan       = isset($_POST['layanan_makan']) ? 1 : 0;
$harga_paket_perjalanan = isset($_POST['harga_paket_perjalanan']) ? (int)$_POST['harga_paket_perjalanan'] : 0;
// Hitung jumlah tagihan di server juga (keamanan): paket + (layanan * hari * peserta)
$jumlah_tagihan = (int)$harga_paket + (int)$harga_paket_perjalanan * (int)$lama_perjalanan * (int)$jumlah_peserta;

// Tanggal pesan (server side)
$tanggal_pesan = date('Y-m-d');

$query = "INSERT INTO t_pemesanan (nama, nomor_hp, email, paket, harga_paket, layanan_penginapan, layanan_transportasi, layanan_makan, harga_paket_perjalanan, lama_perjalanan, jumlah_peserta, jumlah_tagihan, tanggal, tanggal_pesan, pesan) 
          VALUES ('$nama', '$nomor_hp', '$email', '$paket', '$harga_paket', '$lay_penginapan', '$lay_transport', '$lay_makan', '$harga_paket_perjalanan', '$lama_perjalanan', '$jumlah_peserta', '$jumlah_tagihan', '$tanggal', '$tanggal_pesan', '$pesan')";

if (mysqli_query($koneksi, $query)) {
    echo "<script>
            alert('Pemesanan Berhasil Disimpan! Silakan cek Riwayat Pemesanan Anda.');
            // Langsung arahkan ke halaman riwayat dengan email yang baru diinput
            window.location = 'riwayat_pemesanan.php?email=$email'; 
          </script>";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
}
?>