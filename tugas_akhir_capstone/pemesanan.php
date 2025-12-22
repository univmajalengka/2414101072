<?php
date_default_timezone_set("Asia/Jakarta");
$tanggal = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pemesanan Paket Wisata Bunaken</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<section id="pemesanan" class="content-section">
<h2>Pesan Perjalanan Anda ke Bunaken</h2>
<p>Isi formulir di bawah ini untuk memulai pemesanan paket wisata.</p>

<div class="form-container">
<form action="proses_simpan.php" method="POST">

<div class="form-group">
<label>Nama Lengkap</label>
<input type="text" name="nama" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="form-group">
<label>Pilih Paket Wisata</label>
<select id="paket" name="paket" required>
    <option value="" data-price="0">-- Pilih Paket --</option>
    <option value="Paket Penyelam Sejati" data-price="4500000">
        Paket Penyelam Sejati (Rp 4.500.000)
    </option>
    <option value="Paket Keluarga Snorkeling" data-price="750000">
        Paket Keluarga Snorkeling (Rp 750.000)
    </option>
    <option value="Custom" data-price="0">
        Permintaan Custom
    </option>
</select>
</div>

<div class="form-group">
<label>Harga Paket</label>
<input type="text" id="display_harga_paket" readonly value="Rp 0">
<input type="hidden" id="harga_paket" name="harga_paket" value="0">
</div>

<div class="form-group">
<label>Pelayanan</label>
<label><input type="checkbox" class="layanan" data-price="1000000"> Penginapan (Rp 1.000.000)</label><br>
<label><input type="checkbox" class="layanan" data-price="1200000"> Transportasi (Rp 1.200.000)</label><br>
<label><input type="checkbox" class="layanan" data-price="500000"> Service / Makan (Rp 500.000)</label>
</div>

<div class="form-group">
<label>Jumlah Peserta</label>
<input type="number" id="jumlah_peserta" name="jumlah_peserta" value="1" min="1" required>
</div>

<div class="form-group">
<label>Total Layanan</label>
<input type="text" id="display_total_layanan" readonly value="Rp 0">
<input type="hidden" id="total_layanan" name="total_layanan" value="0">
</div>

<div class="form-group">
<label>Jumlah Tagihan</label>
<input type="text" id="display_jumlah_tagihan" readonly value="Rp 0">
<input type="hidden" id="jumlah_tagihan" name="jumlah_tagihan" value="0">
</div>

<div class="form-group">
<label>Tanggal Pesan</label>
<input type="date" name="tanggal_pesan" value="<?= $tanggal ?>" readonly>
</div>

<button type="submit" class="submit-button">Kirim Pemesanan</button>

</form>
</div>
</section>

<!-- ================= JAVASCRIPT ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

const paket = document.getElementById("paket");
const layanan = document.querySelectorAll(".layanan");
const jumlahPeserta = document.getElementById("jumlah_peserta");

const displayHargaPaket = document.getElementById("display_harga_paket");
const inputHargaPaket = document.getElementById("harga_paket");

const displayTotalLayanan = document.getElementById("display_total_layanan");
const inputTotalLayanan = document.getElementById("total_layanan");

const displayJumlahTagihan = document.getElementById("display_jumlah_tagihan");
const inputJumlahTagihan = document.getElementById("jumlah_tagihan");

function rupiah(angka) {
    return "Rp " + angka.toLocaleString("id-ID");
}

function hitung() {
    const hargaPaket = parseInt(paket.selectedOptions[0].dataset.price) || 0;
    const peserta = parseInt(jumlahPeserta.value) || 1;

    let totalLayanan = 0;
    layanan.forEach(l => {
        if (l.checked) {
            totalLayanan += parseInt(l.dataset.price);
        }
    });

    const total = (hargaPaket + totalLayanan) * peserta;

    displayHargaPaket.value = rupiah(hargaPaket);
    inputHargaPaket.value = hargaPaket;

    displayTotalLayanan.value = rupiah(totalLayanan);
    inputTotalLayanan.value = totalLayanan;

    displayJumlahTagihan.value = rupiah(total);
    inputJumlahTagihan.value = total;
}

paket.addEventListener("change", hitung);
jumlahPeserta.addEventListener("input", hitung);
layanan.forEach(l => l.addEventListener("change", hitung));

hitung();
});
</script>

</body>
</html>
