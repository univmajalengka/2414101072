document.addEventListener("DOMContentLoaded", function () {

    // Pakai ID `paket` yang ada di form
    const paketSelect = document.getElementById("paket") || document.getElementById("pilihan_paket");

    const displayHarga = document.getElementById("display_harga");
    const hargaPaketInput = document.getElementById("harga_paket_input");



    const lamaPerjalanan = document.getElementById("lama_perjalanan");
    const jumlahPeserta = document.getElementById("jumlah_peserta");

    const layananPenginapan = document.getElementById("layanan_penginapan");
    const layananTransportasi = document.getElementById("layanan_transportasi");
    const layananMakan = document.getElementById("layanan_makan");

    const displayHargaPerjalanan = document.getElementById("display_harga_perjalanan");
    const hargaPerjalananInput = document.getElementById("harga_paket_perjalanan_input");

    const displayTotal = document.getElementById("display_jumlah_tagihan");
    const totalInput = document.getElementById("jumlah_tagihan_input");

    function formatRupiah(angka) {
        return "Rp " + angka.toLocaleString("id-ID");
    }

    function hitungTotal() {
        if(!paketSelect) return;

        const selectedOption = paketSelect.options[paketSelect.selectedIndex];
        const hargaPaket = parseInt(selectedOption.getAttribute("data-price")) || 0;

        displayHarga.value = formatRupiah(hargaPaket);
        hargaPaketInput.value = hargaPaket;

        let totalLayanan = 0;
        if (layananPenginapan.checked) totalLayanan += parseInt(layananPenginapan.dataset.price);
        if (layananTransportasi.checked) totalLayanan += parseInt(layananTransportasi.dataset.price);
        if (layananMakan.checked) totalLayanan += parseInt(layananMakan.dataset.price);

        displayHargaPerjalanan.value = formatRupiah(totalLayanan);
        hargaPerjalananInput.value = totalLayanan;

        const hari = Math.max(1, parseInt(lamaPerjalanan.value) || 1);
        const peserta = Math.max(1, parseInt(jumlahPeserta.value) || 1);

        const total = hargaPaket + (totalLayanan * hari * peserta);

        displayTotal.value = formatRupiah(total);
        totalInput.value = total;
    }

    paketSelect.addEventListener("change", hitungTotal);
    lamaPerjalanan.addEventListener("input", hitungTotal);
    jumlahPeserta.addEventListener("input", hitungTotal);

    layananPenginapan.addEventListener("change", hitungTotal);
    layananTransportasi.addEventListener("change", hitungTotal);
    layananMakan.addEventListener("change", hitungTotal);

    const tanggalPesan = document.getElementById("tanggal_pesan");
    if (tanggalPesan) {
        tanggalPesan.value = new Date().toISOString().split("T")[0];
    }

    // Panggil saat pertama dimuat
    hitungTotal();


});