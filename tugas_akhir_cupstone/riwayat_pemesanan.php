<?php 
// riwayat_pemesanan.php
include 'koneksi.php'; 

// Fungsi Helper untuk menampilkan status checkbox
function displayStatus($status) {
    return $status == 1 ? '<span style="color: green; font-weight: bold;">&#10003; Ya</span>' : 'Tidak';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pemesanan Anda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Pesan Baru</a></li>
            <li><a href="riwayat_pemesanan.php" class="active" style="background-color: #00bcd4;">Riwayat Pemesanan</a></li>
        </ul>
    </nav>

    <div class="table-container">
        <h2>Riwayat Pemesanan Anda</h2>
        
        <div class="form-container" style="padding: 20px; margin-bottom: 20px;">
            <p>Masukkan Email Anda untuk menampilkan riwayat pemesanan:</p>
            <form method="GET" action="riwayat_pemesanan.php">
                <div class="form-group" style="display: flex; gap: 10px;">
                    <input type="email" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" placeholder="Email Anda" required style="flex-grow: 1;">
                    <button type="submit" class="submit-button" style="width: auto;">Cari Riwayat</button>
                </div>
            </form>
        </div>

        <?php
        $email_dicari = isset($_GET['email']) ? mysqli_real_escape_string($koneksi, $_GET['email']) : '';
        if ($email_dicari) {
            $data = mysqli_query($koneksi, "SELECT * FROM t_pemesanan WHERE email='$email_dicari' ORDER BY id DESC");
            $check = mysqli_num_rows($data);

            if ($check > 0) {
        ?>
        <h3>Ditemukan <?php echo $check; ?> Riwayat untuk Email: <?php echo htmlspecialchars($email_dicari); ?></h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Paket</th>
                    <th>Penginapan</th>
                    <th>Transport</th>
                    <th>Service/Makan</th>
                    <th>Peserta</th>
                    <th>Lama (Hari)</th>
                    <th>Harga Paket/Layanan</th>
                    <th>Jumlah Tagihan</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while($d = mysqli_fetch_array($data)){
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($d['paket']); ?></td>
                    <td><?php echo displayStatus($d['layanan_penginapan']); ?></td>
                    <td><?php echo displayStatus($d['layanan_transportasi']); ?></td>
                    <td><?php echo displayStatus($d['layanan_makan']); ?></td>
                    <td><?php echo intval($d['jumlah_peserta']); ?></td>
                    <td><?php echo intval($d['lama_perjalanan']); ?></td>
                    <td><?php echo 'Rp ' . number_format($d['harga_paket_perjalanan'], 0, ',', '.'); ?></td>
                    <td><?php echo 'Rp ' . number_format($d['jumlah_tagihan'], 0, ',', '.'); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($d['tanggal'])); ?></td>
                    <td>
                        <a href="edit_pemesanan.php?id=<?php echo $d['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="proses_hapus.php?id=<?php echo $d['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin batalkan pemesanan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <p>Tidak ada riwayat pemesanan yang ditemukan untuk Email: **<?php echo htmlspecialchars($email_dicari); ?>**</p>
        <?php }
        } else { ?>
            <p>Silakan masukkan email Anda untuk melihat riwayat pemesanan yang pernah Anda buat.</p>
        <?php } ?>
    </div>
</body>
</html>