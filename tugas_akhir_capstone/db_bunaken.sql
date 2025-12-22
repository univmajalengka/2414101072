CREATE DATABASE IF NOT EXISTS db_bunaken;
USE db_bunaken;

CREATE TABLE IF NOT EXISTS t_pemesanan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nomor_hp VARCHAR(30) DEFAULT NULL,
    email VARCHAR(100) NOT NULL,
    paket VARCHAR(100) NOT NULL,
    harga_paket INT(11) NOT NULL,
    -- Pilihan layanan (1 = dipilih, 0 = tidak)
    layanan_penginapan TINYINT(1) DEFAULT 0,
    layanan_transportasi TINYINT(1) DEFAULT 0,
    layanan_makan TINYINT(1) DEFAULT 0,
    -- Harga paket perjalanan dan perhitungan
    harga_paket_perjalanan INT(11) DEFAULT 0,
    lama_perjalanan INT(11) DEFAULT 1,
    jumlah_peserta INT(11) DEFAULT 1,
    jumlah_tagihan BIGINT(20) DEFAULT 0,
    -- Kolom lama/custom
    alat_menyelam TINYINT(1) DEFAULT 0,  -- KOLOM BARU (backwards compat)
    pendamping TINYINT(1) DEFAULT 0,     -- KOLOM BARU (backwards compat)
    tanggal DATE NOT NULL,
    tanggal_pesan DATE DEFAULT CURRENT_DATE,
    pesan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);