<?php
session_start();
// Sertakan file koneksi database
require_once 'db_config.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $newName = trim($_POST['name'] ?? '');
    $newPrice = intval($_POST['price'] ?? 0);
    $newDesc = trim($_POST['description'] ?? '');
    $newImage = trim($_POST['image'] ?? '');

    if (!empty($newName) && $newPrice > 0) {
        $pdo = getDB();
        
        try {
            // Gunakan Prepared Statement untuk keamanan
            $sql = "INSERT INTO products (name, price, description, image) 
                    VALUES (:name, :price, :description, :image)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':name' => $newName,
                ':price' => $newPrice,
                ':description' => $newDesc,
                ':image' => $newImage
            ]);

            // Jika berhasil, redirect
            header('Location: admin.php#products');
            exit;

        } catch (\PDOException $e) {
            // Tampilkan error database jika ada
            $error = 'Gagal menyimpan produk baru. Error Database: ' . $e->getMessage(); 
        }
    } else {
        $error = 'Nama dan Harga Produk tidak boleh kosong.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Produk - Admin</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="navbar-logo">MaDrinks</a>
      <div class="navbar-extra">
        <a href="admin.php" id="adminBack">Kembali ke Dashboard</a>
      </div>
    </nav>
    <main style="padding: 4rem 6%;">
        <section style="max-width:600px;margin:2rem auto;padding:1.5rem;background:#fff;border-radius:10px;color:#222;">
            <h2 style="margin-bottom:1.5rem;">Tambah Produk Baru</h2>
            
            <?php if (!empty($error)): ?>
              <div style="color:#b65b5b;margin-bottom:1rem;border:1px solid #b65b5b;padding:0.75rem;background:#ffeeee;border-radius:6px;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="add_product.php">
                <label for="name" style="display:block;margin-bottom:0.5rem;">Nama Produk:</label>
                <input type="text" id="name" name="name" required style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;">

                <label for="price" style="display:block;margin-bottom:0.5rem;">Harga (Rp):</label>
                <input type="number" id="price" name="price" required min="0" style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;">
                
                <label for="description" style="display:block;margin-bottom:0.5rem;">Deskripsi:</label>
                <textarea id="description" name="description" style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;min-height: 80px;"></textarea>

                <label for="image" style="display:block;margin-bottom:0.5rem;">URL/Path Gambar:</label>
                <input type="text" id="image" name="image" placeholder="img/namafile.jpg" style="width:100%;padding:0.6rem;margin-bottom:1.5rem;border:1px solid #ddd;border-radius:6px;">
                
                <button type="submit" name="add_product" style="background:#5cb85c;color:#fff;padding:0.8rem 1.5rem;border-radius:8px;border:none;cursor:pointer;width:100%;">Simpan Produk</button>
            </form>
        </section>
    </main>
</body>
</html>