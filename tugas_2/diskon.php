<?php
// -------------------------------
// Fungsi hitungDiskon()
// -------------------------------
function hitungDiskon($totalBelanja) {
    $diskon = 0;

    if ($totalBelanja >= 100000) {
        $diskon = 0.10 * $totalBelanja; // Diskon 10%
    } elseif ($totalBelanja >= 50000) {
        $diskon = 0.05 * $totalBelanja; // Diskon 5%
    }
    return $diskon; // Mengembalikan nominal diskon
}

// -------------------------------
// Contoh pemanggilan fungsi
// -------------------------------
$totalBelanja = 120000;
$diskon = hitungDiskon($totalBelanja);
$totalBayar = $totalBelanja - $diskon;

// -------------------------------
// Output
// -------------------------------
echo "Total Belanja : Rp. " . number_format($totalBelanja, 0, ',', '.') . "<br>";
echo "Diskon        : Rp. " . number_format($diskon, 0, ',', '.') . "<br>";
echo "Total Bayar   : Rp. " . number_format($totalBayar, 0, ',', '.') . "<br>";
?>