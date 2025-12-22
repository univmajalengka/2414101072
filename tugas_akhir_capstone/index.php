<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taman Nasional Bunaken - Pesona Bawah Laut Sulawesi</title>
    <link rel="stylesheet" href="style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <div class="header-content">
            <h1>TAMAN NASIONAL BUNAKEN</h1>
            <p>Jelajahi Surga Bawah Laut di Jantung Coral Triangle</p>
        </div>
    </header>

    <section class="banner-gallery">
        <div class="gallery-container">
            <div class="gallery-item item-1"><div class="caption">Menyelam Bersama Ribuan Ikan</div></div>
            <div class="gallery-item item-2"><div class="caption">Keindahan Pantai Berpasir Putih</div></div>
            <div class="gallery-item item-3"><div class="caption">Petualangan Kapal Tradisional</div></div>
        </div>
    </section>

    <nav class="main-nav">
        <ul>
            <li><a href="#about" class="active">Beranda</a></li> 
            <li><a href="#about">About</a></li>
            <li><a href="#objek">Obyek Wisata</a></li>
            <li><a href="#paket">Paket</a></li>
            <li><a href="#galery">Galery</a></li>
            <li><a href="#pemesanan">Pemesanan</a></li>
            <li><a href="riwayat_pemesanan.php" style="background-color: #00bcd4;">Riwayat Pemesanan</a></li>
        </ul>
    </nav>

    <main>
        
        <section id="about" class="content-section">
            <h2>Tentang Bunaken</h2>
            <p>Taman Nasional Bunaken di Sulawesi Utara adalah salah satu destinasi menyelam terbaik di dunia.</p>
            <div class="video-container">
                <iframe src="https://www.youtube.com/embed/y2RC_UUMPGc" title="Video Bunaken" frameborder="0" allowfullscreen></iframe>
            </div>
        </section>

        <section id="paket" class="content-section">
            <h2>Paket Eksklusif</h2>
            <div class="package-cards">
                <div class="card" data-price="4500000">
                    <h3>Paket Penyelam Sejati (3H2M)</h3>
                    <p>Termasuk 5x Dive, akomodasi, dan transportasi lokal. **Harga: Rp 4.500.000**</p>
                </div>
                <div class="card" data-price="1000000">
                    <h3>Paket Keluarga Snorkeling (1 Hari)</h3>
                    <p>Wisata snorkeling dan jelajah pulau. **Harga: Rp 750.000**</p>
                </div>
                <div class="card" data-price="0">
                    <h3>Permintaan Custom</h3>
                    <p>Harga akan dikonfirmasi. **Harga: Rp 0**</p>
                </div>
            </div>
        </section>

        <section id="objek" class="content-section">
            <h2>Spot Menyelam & Pulau Ikonik</h2>
            <div class="objek-list package-cards"> 
                <div class="objek-card card"><h3>Pulau Siladen</h3><p>Pasir putih tenang.</p></div>
                <div class="objek-card card"><h3>Fukui Point</h3><p>Spot dinding karang terbaik.</p></div>
            </div>
        </section>

        <section id="galery" class="content-section">
            <h2>Galeri</h2>
            <div class="galery-grid">
                <div class="galery-item"><img src="img/nemo.jpg" alt="Foto 1"></div>
                <div class="galery-item"><img src="img/penyu.jpg" alt="Foto 2"></div>
                <div class="galery-item"><img src="img/terumbu.jpg" alt="Foto 3"></div>
                <div class="galery-item"><img src="img/diving.jpg" alt="Foto 4"></div>
            </div>
        </section>

        <section id="pemesanan" class="content-section">
            <h2>Pesan Perjalanan Anda ke Bunaken</h2>
            <p>Isi formulir di bawah ini untuk memulai pemesanan paket wisata.</p>
            
            <div class="form-container">
                <form action="proses_simpan.php" method="POST"> 
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email (Digunakan untuk Riwayat Pemesanan)</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="paket">Pilih Paket Wisata</label>
                        <select id="paket" name="paket" required>
                            <option value="" data-price="0">-- Pilih salah satu --</option>
                            <option value="Paket Penyelam Sejati (3H2M)" data-price="3000000">Paket Penyelam Sejati (Rp 3.000.000)</option>
                            <option value="Paket Keluarga Snorkeling" data-price="5000000">Paket Keluarga Snorkeling (Rp 5.000.000)</option>
                            <option value="Custom" data-price="0">Permintaan Custom (Rp 0)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Harga Paket</label>
                        <input type="text" id="display_harga" value="Rp 0" readonly style="background-color: #f8f8f8; font-weight: bold;">
                    </div>

                    <input type="hidden" id="harga_paket_input" name="harga_paket" value="0">

                    <div class="form-group">
                        <label for="phone">Nomor HP / Telp</label>
                        <input type="tel" id="phone" name="nomor_hp" placeholder="0812xxxx" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_pesan">Tanggal Pesan</label>
                        <input type="date" id="tanggal_pesan" name="tanggal_pesan" readonly style="background-color: #f0f0f0;">
                    </div>

                    <div class="form-group">
                        <label for="lama_perjalanan">Waktu Pelaksanaan Perjalanan (Hari)</label>
                        <input type="number" id="lama_perjalanan" name="lama_perjalanan" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Pelayanan (pilih sesuai kebutuhan)</label>
                        <div style="display: flex; flex-direction: column; gap: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #fafafa;">
                            <label><input type="checkbox" id="layanan_penginapan" name="layanan_penginapan" value="1" data-price="1000000"> Penginapan (Rp 1.000.000)</label>
                            <label><input type="checkbox" id="layanan_transportasi" name="layanan_transportasi" value="1" data-price="1200000"> Transportasi (Rp 1.200.000)</label>
                            <label><input type="checkbox" id="layanan_makan" name="layanan_makan" value="1" data-price="500000"> Service / Makan (Rp 500.000)</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_peserta">Jumlah Peserta</label>
                        <input type="number" id="jumlah_peserta" name="jumlah_peserta" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Harga Paket Perjalanan (Total layanan)</label>
                        <input type="text" id="display_harga_perjalanan" value="Rp 0" readonly style="background-color: #f8f8f8; font-weight: bold;">
                    </div>
                    <input type="hidden" id="harga_paket_perjalanan_input" name="harga_paket_perjalanan" value="0">

                    <div class="form-group">
                        <label>Jumlah Tagihan</label>
                        <input type="text" id="display_jumlah_tagihan" value="Rp 0" readonly style="background-color: #f8f8f8; font-weight: bold;">
                    </div>
                    <input type="hidden" id="jumlah_tagihan_input" name="jumlah_tagihan" value="0">

                    <div class="form-group">
                        <label for="tanggal">Tanggal Kedatangan</label>
                        <input type="date" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="pesan">Pesan / Permintaan Khusus</label>
                        <textarea id="pesan" name="pesan" rows="4"></textarea>
                    </div>
                    <button type="submit" class="submit-button">Kirim Pemesanan</button>
                </form>
            </div>
        </section>
        
    </main>

    <footer>
        <p>&copy; 2025 Taman Nasional Bunaken. Jelajahi. Lindungi. Nikmati.</p>
    </footer>

    <script src="script.js?v=<?php echo time(); ?>"></script> 
</body>
</html>