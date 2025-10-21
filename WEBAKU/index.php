<?php
session_start();
// WAJIB: Sertakan file konfigurasi database
// Pastikan db_config.php ada di folder yang sama
require_once 'db_config.php'; 

// Fungsi untuk memuat data produk dari database
function loadProducts() {
    // Diasumsikan fungsi getDB() tersedia dari db_config.php
    $pdo = getDB(); 
    try {
        $stmt = $pdo->query('SELECT id, name, price, description, image FROM products ORDER BY name ASC');
        // Mengambil semua hasil sebagai array asosiatif
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        // Mengembalikan error untuk ditampilkan jika koneksi/query gagal
        return ['error' => 'Gagal memuat menu: ' . $e->getMessage()];
    }
}

$products = loadProducts();
$dbError = false;

// Cek jika ada error database saat memuat produk
if (isset($products['error'])) {
    $dbError = $products['error'];
    $products = []; // Set produk kosong jika ada error
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MaDrinks</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
      rel="stylesheet"
    />

    <script src="https://unpkg.com/feather-icons"></script>

  <link rel="stylesheet" href="style.css?ver=<?php echo file_exists(__DIR__.'/style.css') ? filemtime(__DIR__.'/style.css') : time(); ?>" />
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="navbar-logo">MaDrinks</a>
      <div class="navbar-nav">
        <a href="#home">Home</a>
        <a href="#menu">Menu</a>
        <a href="#about">Tentang Kami</a>
        <a href="#contact">Kontak</a>
      </div>
      <div class="navbar-extra">
        <a href="#" id="historyBtn" class="btn-nav">RIWAYAT</a> 
        <a href="#" id="loginBtn" class="btn-nav">LOGIN</a> 
        <a href="#" id="adminBtn" class="admin-btn btn-nav" style="margin-left:12px;">ADMIN</a>
        <a href="#" id="cartBtn"><i data-feather="shopping-cart"></i><span id="cartBadge" class="cart-badge">0</span></a>
        <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
      </div>
    </nav>

    <section class="hero" id="home">
      <main class="content">
        <h1>Selamat datang di <span>MaDrinks</span></h1>
        <p>
          Nikmati sensasi minuman segar dengan cita rasa unik dan tampilan kekinian.
Kami menghadirkan berbagai varian minuman yang dibuat dari bahan pilihan, disajikan dengan penuh kreativitas untuk menemani setiap momen spesialmu.
        </p>
        <a href="#menu" class="cta">Beli Sekarang</a>
      </main>
    </section>

    <section id="admin-inline" class="inline-admin" style="display:none;">
      <div class="inline-admin-card">
        <h3 style="margin-bottom:0.5rem;">Admin Login</h3>
        <form id="adminInlineForm" method="post" action="admin.php">
          <input type="text" name="username" id="inlineAdminUser" placeholder="Admin Username" required />
          <input type="password" name="password" id="inlineAdminPass" placeholder="Admin Password" required />
          <input type="hidden" name="login" value="1">
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem;">
            <button type="submit" class="cta" style="background:var(--primary);">Login</button>
            <button type="button" id="closeInlineAdmin" class="btn-white" style="background:var(--primary);">Tutup</button>
          </div>
        </form>
      </div>
    </section>

    <section id="menu" class="menu">
      <h2><span>Our</span> Menus</h2>
      
      <?php if ($dbError): ?>
        <p style="color:#b65b5b; text-align:center; padding:1rem; background:#ffeeee; border-radius:8px; max-width: 600px; margin: 1rem auto;">⚠️ **Database Error**: <?php echo htmlspecialchars($dbError); ?>. Cek konfigurasi `db_config.php` dan status server MySQL Anda.</p>
      <?php endif; ?>

      <div class="tab-content active" id="starters">
        <div class="menu-grid">
          
          <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="menu-card" 
                 data-id="<?php echo htmlspecialchars($product['id']); ?>" 
                 data-price="<?php echo htmlspecialchars($product['price']); ?>" 
                 data-name="<?php echo htmlspecialchars($product['name']); ?>">
              
              <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'img/default.jpg'; ?>" 
                   alt="<?php echo htmlspecialchars($product['name']); ?>" />
              
              <h3><?php echo htmlspecialchars($product['name']); ?></h3>
              <p>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
              
              <button class="add-to-cart">Add to cart</button>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <?php if (!$dbError): ?>
              <p style="text-align:center; width:100%; color:#888;">Menu kosong. Silakan tambahkan produk baru melalui halaman Admin.</p>
            <?php endif; ?>
          <?php endif; ?>
          
        </div>
      </div>
    </section>

    <div class="modal" id="cart-modal">
      <div class="modal-content">
        <span class="close" id="closeCartModal">&times;</span>
        <h3>Keranjang</h3>
        <ul id="cartList"></ul>
        <div style="margin-top:1rem;">
          <strong>Total: Rp <span id="cartTotal">0</span></strong>
        </div>
        <form id="checkoutForm" style="margin-top:1rem;">
          <input type="text" id="custName" placeholder="Nama" required />
          <input type="tel" id="custPhone" placeholder="No. Telp" required />
          <button type="submit">Checkout</button>
        </form>
      </div>
    </div>

    <div class="modal" id="history-modal">
      <div class="modal-content">
        <span class="close" id="closeHistoryModal">&times;</span>
        <h3>Riwayat Pesanan</h3>
        <form id="historyForm" style="margin-bottom:1rem;">
          <input type="tel" id="historyPhone" placeholder="Masukkan No. Telp Anda" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px; width:100%; box-sizing:border-box; margin-bottom:0.5rem;" />
          <button type="submit" style="padding:0.6rem; background:var(--primary); color:#fff; border:none; border-radius:4px; cursor:pointer; width:100%;">Cek Riwayat</button>
        </form>
        
        <div id="historyResult">
          <p style="text-align:center; color:#888;">Masukkan nomor telepon untuk melihat pesanan.</p>
        </div>
      </div>
    </div>
    <section id="about" class="about">
      <h2><span>Tentang</span> Kami</h2>
      <div class="about-content">
        <h3>MaDrinks — More Than Just a Drink</h3>
        <p>
          Didirikan dengan semangat anak muda yang ingin menghadirkan inovasi rasa, Madrinks menjadi tempat terbaik untuk menemukan minuman dengan gaya baru.
Kami percaya, setiap tegukan bisa membawa kebahagiaan.
Dengan bahan alami, resep eksklusif, dan pelayanan ramah, kami ingin menciptakan pengalaman minum yang tak terlupakan.
      </div>
    </section>

    <section id="review" class="review">
      <h2><span>Review</span> Pelanggan</h2>
      <form id="reviewForm" class="review-upload" enctype="multipart/form-data" style="margin-bottom:2rem;">
        <input type="text" id="reviewerName" placeholder="Nama" required>
        <input type="text" id="reviewerComment" placeholder="Komentar" required>
        <input type="file" id="reviewPhoto" accept="image/*">
        <button type="submit">Tambah Review</button>
      </form>
      <ul id="reviewList" class="review-list"></ul>
    </section>

    <footer class="footer" id="contact">
      <div class="footer-container">
        <div class="footer-about">
          <h3>MaDrinks</h3>
          <p>Menghadirkan cita rasa autentik dengan suasana hangat.</p>
          <div class="footer-social">
            <a href="#"><i data-feather="facebook"></i></a>
            <a href="#"><i data-feather="instagram"></i></a>
            <a href="#"><i data-feather="twitter"></i></a>
          </div>
        </div>
        <div class="footer-contact">
          <h3>Kontak Kami</h3>
          <p>Email: info@MaDrinks.com</p>
          <p>Telp: +62 812 3456 7890</p>
        </div>
        <div class="footer-form">
          <h3>Kirim Pesan</h3>
          <form>
            <input type="text" placeholder="Nama" required />
            <input type="email" placeholder="Email" required />
            <textarea placeholder="Pesan" required></textarea>
            <button type="submit">Kirim</button>
          </form>
        </div>
      </div>
      <p class="footer-bottom">&copy; 2025 MaDrinks. All Rights Reserved.</p>
    </footer>

    <div class="modal" id="auth-modal">
      <div class="modal-content">
        <span class="close" id="closeCustomerModal">&times;</span>
        <div class="tabs" style="display:flex; justify-content:center; gap:1rem; margin-bottom:1rem;">
          <button class="auth-tab active" data-tab="login" style="padding:0.5rem 1rem; border:none; background:#eee; cursor:pointer;">Login</button>
          <button class="auth-tab" data-tab="register" style="padding:0.5rem 1rem; border:none; background:transparent; cursor:pointer;">Register</button>
        </div>
        <div class="auth-form active" id="login-form">
          <form style="display:flex; flex-direction:column; gap:0.5rem;">
            <input type="text" placeholder="Username" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px;" />
            <input type="password" placeholder="Password" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px;" />
            <button type="submit" style="padding:0.6rem; background:var(--primary); color:#fff; border:none; border-radius:4px; cursor:pointer;">Login</button>
          </form>
        </div>
        <div class="auth-form" id="register-form" style="display:none;">
          <form style="display:flex; flex-direction:column; gap:0.5rem;">
            <input type="text" placeholder="Nama Lengkap" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px;" />
            <input type="email" placeholder="Email" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px;" />
            <input type="password" placeholder="Password" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px;" />
            <input type="password" placeholder="Konfirmasi Password" required style="padding:0.6rem; border:1px solid #ddd; border-radius:4px;" />
            <button type="submit" style="padding:0.6rem; background:var(--primary); color:#fff; border:none; border-radius:4px; cursor:pointer;">Register</button>
          </form>
        </div>
      </div>
    </div>

    <div class="modal" id="admin-modal" style="display:none;">
      <div class="modal-content">
        <span class="close" id="closeAdminModal">&times;</span>
        <h3 style="text-align:center;">Admin Login</h3>
        <form id="adminForm" method="post" action="admin.php">
          <input type="text" id="adminUser" name="username" placeholder="Admin Username" required />
          <input type="password" id="adminPass" name="password" placeholder="Admin Password" required />
          <input type="hidden" name="login" value="1">
          <div id="adminFeedback" style="margin:8px 0;color:#b65b5b;display:none;"></div>
          <button type="submit">Login</button>
        </form>
      </div>
    </div>

    <script>
      feather.replace();
    </script>
    <script src="script.js?ver=<?php echo file_exists(__DIR__.'/script.js') ? filemtime(__DIR__.'/script.js') : time(); ?>"></script>
  </body>
</html>