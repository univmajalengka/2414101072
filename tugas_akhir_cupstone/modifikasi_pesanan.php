<?php
// modifikasi_pesanan.php - admin listing for all pemesanan
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Modifikasi Pemesanan - Semua Pesanan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Pesan Baru</a></li>
            <li><a href="riwayat_pemesanan.php" style="background-color:#00bcd4;">Riwayat Pemesanan</a></li>
            <li><a href="modifikasi_pesanan.php" class="active">Modifikasi Pesanan</a></li>
        </ul>
    </nav>

    <div class="table-container">
        <h2>Daftar Semua Pesanan</h2>
        <?php
        $data = mysqli_query($koneksi, "SELECT * FROM t_pemesanan ORDER BY id DESC");
        $count = mysqli_num_rows($data);
        if ($count > 0) {
        ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>HP</th>
                    <th>Paket</th>
                    <th>Penginapan</th>
                    <th>Transport</th>
                    <th>Service</th>
                    <th>Peserta</th>
                    <th>Lama</th>
                    <th>Harga/Layanan</th>
                    <th>Jumlah Tagihan</th>
                    <th>Tgl Pelaksanaan</th>
                    <th>Tgl Pesan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($d = mysqli_fetch_array($data)) { ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($d['nama']); ?></td>
                    <td><?php echo htmlspecialchars($d['email']); ?></td>
                    <td><?php echo htmlspecialchars($d['nomor_hp']); ?></td>
                    <td><?php echo htmlspecialchars($d['paket']); ?></td>
                    <td><?php echo $d['layanan_penginapan'] ? 'Ya' : 'Tidak'; ?></td>
                    <td><?php echo $d['layanan_transportasi'] ? 'Ya' : 'Tidak'; ?></td>
                    <td><?php echo $d['layanan_makan'] ? 'Ya' : 'Tidak'; ?></td>
                    <td><?php echo intval($d['jumlah_peserta']); ?></td>
                    <td><?php echo intval($d['lama_perjalanan']); ?></td>
                    <td><?php echo 'Rp ' . number_format($d['harga_paket_perjalanan'],0,',','.'); ?></td>
                    <td><?php echo 'Rp ' . number_format($d['jumlah_tagihan'],0,',','.'); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($d['tanggal'])); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($d['tanggal_pesan'])); ?></td>
                    <td>
                        <a href="edit_pemesanan.php?id=<?php echo $d['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="proses_hapus.php?id=<?php echo $d['id']; ?>&return=modifikasi" class="btn btn-delete" onclick="return confirm('Yakin hapus pemesanan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <p>Tidak ada data pemesanan saat ini.</p>
        <?php } ?>
    </div>
</body>
</html>