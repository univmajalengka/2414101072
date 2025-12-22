<?php 
// edit_pemesanan.php
include 'koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM t_pemesanan WHERE id='$id'");
$d = mysqli_fetch_array($query);

if(!$d) {
    die("Data tidak ditemukan.");
}

// Data harga paket yang sudah fix
$packagePrices = [
    'Paket Penyelam Sejati (3H2M)' => 4500000,
    'Paket Keluarga Snorkeling' => 750000,
    'Custom' => 0
];
// Data harga tambahan custom
$customAddons = [
    'alat_menyelam' => 250000,
    'pendamping' => 500000
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Pemesanan #<?php echo $d['id']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container" style="margin-top: 50px;">
        <a href="riwayat_pemesanan.php?email=<?php echo urlencode($d['email']); ?>" class="btn btn-back">&laquo; Kembali ke Riwayat</a>
        <h2>Edit Pemesanan #<?php echo $d['id']; ?></h2>
        
        <form action="proses_edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($d['nama']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($d['email']); ?>" required readonly style="background-color: #eee;">
            </div>
            
            <div class="form-group">
                <label for="paket_edit">Pilih Paket Wisata</label>
                <select id="paket_edit" name="paket" required>
                    <option value="<?php echo htmlspecialchars($d['paket']); ?>" data-price="<?php echo $packagePrices[$d['paket']]; ?>"><?php echo htmlspecialchars($d['paket']); ?> (Saat ini)</option>
                    
                    <?php 
                    foreach ($packagePrices as $packageName => $price) {
                        if ($packageName !== $d['paket']) {
                            $displayPrice = $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Rp 0';
                            echo "<option value='{$packageName}' data-price='{$price}'>{$packageName} ({$displayPrice})</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div id="custom_options_container_edit" style="display: <?php echo $d['paket'] == 'Custom' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Pilihan Tambahan Custom:</label>
                    <div style="display: flex; flex-direction: column; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                        
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" id="alat_menyelam_edit" name="alat_menyelam" value="1" data-price-add="<?php echo $customAddons['alat_menyelam']; ?>" style="margin-right: 10px;" <?php echo $d['alat_menyelam'] == 1 ? 'checked' : ''; ?>> 
                            Sewa Alat Menyelam Lengkap (Rp <?php echo number_format($customAddons['alat_menyelam'], 0, ',', '.'); ?>/orang/hari)
                        </label>
                        
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" id="pendamping_edit" name="pendamping" value="1" data-price-add="<?php echo $customAddons['pendamping']; ?>" style="margin-right: 10px;" <?php echo $d['pendamping'] == 1 ? 'checked' : ''; ?>> 
                            Sewa Pendamping / Guide Pribadi (Rp <?php echo number_format($customAddons['pendamping'], 0, ',', '.'); ?>/hari)
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Total Harga Paket</label>
                <input type="text" id="display_harga_edit" value="Rp <?php echo number_format($d['harga_paket'], 0, ',', '.'); ?>" readonly style="background-color: #f8f8f8; font-weight: bold;">
            </div>

            <input type="hidden" id="harga_paket_edit_input" name="harga_paket" value="<?php echo $d['harga_paket']; ?>">

            <div class="form-group">
                <label for="phone_edit">Nomor HP / Telp</label>
                <input type="tel" id="phone_edit" name="nomor_hp" value="<?php echo htmlspecialchars(isset($d['nomor_hp']) ? $d['nomor_hp'] : ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="lama_perjalanan_edit">Waktu Pelaksanaan Perjalanan (Hari)</label>
                <input type="number" id="lama_perjalanan_edit" name="lama_perjalanan" value="<?php echo isset($d['lama_perjalanan']) ? $d['lama_perjalanan'] : 1; ?>" min="1" required>
            </div>

            <div class="form-group">
                <label>Pelayanan</label>
                <div style="display: flex; flex-direction: column; gap: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #fafafa;">
                    <label><input type="checkbox" id="layanan_penginapan_edit" name="layanan_penginapan" value="1" data-price="1000000" <?php echo intval($d['layanan_penginapan']) ? 'checked' : ''; ?>> Penginapan (Rp 1.000.000)</label>
                    <label><input type="checkbox" id="layanan_transportasi_edit" name="layanan_transportasi" value="1" data-price="1200000" <?php echo intval($d['layanan_transportasi']) ? 'checked' : ''; ?>> Transportasi (Rp 1.200.000)</label>
                    <label><input type="checkbox" id="layanan_makan_edit" name="layanan_makan" value="1" data-price="500000" <?php echo intval($d['layanan_makan']) ? 'checked' : ''; ?>> Service / Makan (Rp 500.000)</label>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_peserta_edit">Jumlah Peserta</label>
                <input type="number" id="jumlah_peserta_edit" name="jumlah_peserta" value="<?php echo isset($d['jumlah_peserta']) ? $d['jumlah_peserta'] : 1; ?>" min="1" required>
            </div>

            <div class="form-group">
                <label>Harga Paket Perjalanan (Total layanan)</label>
                <input type="text" id="display_harga_perjalanan_edit" value="Rp <?php echo number_format(isset($d['harga_paket_perjalanan']) ? $d['harga_paket_perjalanan'] : 0, 0, ',', '.'); ?>" readonly style="background-color: #f8f8f8; font-weight: bold;">
            </div>
            <input type="hidden" id="harga_paket_perjalanan_edit_input" name="harga_paket_perjalanan" value="<?php echo isset($d['harga_paket_perjalanan']) ? $d['harga_paket_perjalanan'] : 0; ?>">

            <div class="form-group">
                <label>Jumlah Tagihan</label>
                <input type="text" id="display_jumlah_tagihan_edit" value="Rp <?php echo number_format(isset($d['jumlah_tagihan']) ? $d['jumlah_tagihan'] : 0, 0, ',', '.'); ?>" readonly style="background-color: #f8f8f8; font-weight: bold;">
            </div>
            <input type="hidden" id="jumlah_tagihan_edit_input" name="jumlah_tagihan" value="<?php echo isset($d['jumlah_tagihan']) ? $d['jumlah_tagihan'] : 0; ?>">

            <div class="form-group">
                <label>Tanggal Kedatangan</label>
                <input type="date" name="tanggal" value="<?php echo $d['tanggal']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Pesan</label>
                <textarea name="pesan" rows="4"><?php echo htmlspecialchars($d['pesan']); ?></textarea>
            </div>
            
            <button type="submit" class="submit-button">Update Pemesanan</button>
        </form>
    </div>

    <script>
        // Fungsi format Rupiah lokal
        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, '');
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return 'Rp ' + rupiah;
        }

        const paketSelectEdit = document.getElementById('paket_edit');
        const hargaPaketHiddenInputEdit = document.getElementById('harga_paket_edit_input');
        const customOptionsContainerEdit = document.getElementById('custom_options_container_edit');
        const customCheckboxesEdit = document.querySelectorAll('#custom_options_container_edit input[type="checkbox"]');
        const displayHargaEdit = document.getElementById('display_harga_edit');


        function updateHargaEdit() {
            const selectedOption = paketSelectEdit.options[paketSelectEdit.selectedIndex];
            let basePrice = parseInt(selectedOption.getAttribute('data-price'));
            const isCustom = selectedOption.value === 'Custom';
            
            // Tampilkan/Sembunyikan opsi custom
            customOptionsContainerEdit.style.display = isCustom ? 'block' : 'none';

            // Jika Custom dipilih, hitung biaya tambahan
            if (isCustom) {
                let additionalPrice = 0;
                customCheckboxesEdit.forEach(checkbox => {
                    if (checkbox.checked) {
                        additionalPrice += parseInt(checkbox.getAttribute('data-price-add'));
                    }
                });
                basePrice = additionalPrice; // Total harga custom adalah jumlah pilihan tambahan
            }
            
            // Update nilai input hidden dan display (paket)
            hargaPaketHiddenInputEdit.value = basePrice;
            displayHargaEdit.value = formatRupiah(basePrice);

            // --- Hitung harga paket perjalanan berdasarkan layanan ---
            const layananPenginapan = document.getElementById('layanan_penginapan_edit');
            const layananTransport = document.getElementById('layanan_transportasi_edit');
            const layananMakan = document.getElementById('layanan_makan_edit');
            const lamaPerjalanan = document.getElementById('lama_perjalanan_edit');
            const jumlahPeserta = document.getElementById('jumlah_peserta_edit');
            const displayHargaPerjalanan = document.getElementById('display_harga_perjalanan_edit');
            const hargaPaketPerjalananInput = document.getElementById('harga_paket_perjalanan_edit_input');
            const displayJumlahTagihan = document.getElementById('display_jumlah_tagihan_edit');
            const jumlahTagihanInput = document.getElementById('jumlah_tagihan_edit_input');

            let paketPerjalananPrice = 0;
            if (layananPenginapan && layananPenginapan.checked) paketPerjalananPrice += parseInt(layananPenginapan.getAttribute('data-price'));
            if (layananTransport && layananTransport.checked) paketPerjalananPrice += parseInt(layananTransport.getAttribute('data-price'));
            if (layananMakan && layananMakan.checked) paketPerjalananPrice += parseInt(layananMakan.getAttribute('data-price'));

            const days = lamaPerjalanan ? Math.max(1, parseInt(lamaPerjalanan.value) || 1) : 1;
            const people = jumlahPeserta ? Math.max(1, parseInt(jumlahPeserta.value) || 1) : 1;

            const total = paketPerjalananPrice * days * people;

            if (displayHargaPerjalanan) displayHargaPerjalanan.value = formatRupiah(paketPerjalananPrice);
            if (hargaPaketPerjalananInput) hargaPaketPerjalananInput.value = paketPerjalananPrice;
            if (displayJumlahTagihan) displayJumlahTagihan.value = formatRupiah(total);
            if (jumlahTagihanInput) jumlahTagihanInput.value = total;
        }

        if(paketSelectEdit) {
            updateHargaEdit(); 
            // Tambahkan event listener untuk perubahan pada select paket
            paketSelectEdit.addEventListener('change', updateHargaEdit);
            // Tambahkan event listener untuk perubahan pada checkbox custom
            customCheckboxesEdit.forEach(checkbox => {
                checkbox.addEventListener('change', updateHargaEdit);
            });

            // Layanan dan perhitungan update
            const layananInputsEdit = document.querySelectorAll('#layanan_penginapan_edit, #layanan_transportasi_edit, #layanan_makan_edit');
            layananInputsEdit.forEach(item => item && item.addEventListener('change', updateHargaEdit));
            const lamaInputEdit = document.getElementById('lama_perjalanan_edit');
            const pesertaInputEdit = document.getElementById('jumlah_peserta_edit');
            if (lamaInputEdit) lamaInputEdit.addEventListener('input', updateHargaEdit);
            if (pesertaInputEdit) pesertaInputEdit.addEventListener('input', updateHargaEdit);
        }
    </script>
</body>
</html>