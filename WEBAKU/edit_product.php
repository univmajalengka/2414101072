<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: admin.php');
    exit;
}

$productId = $_GET['id'] ?? null;
$product = null;
$error = '';
$pdo = getDB();

if (empty($productId) || !is_numeric($productId)) {
    $_SESSION['crud_error'] = 'ID Produk tidak valid.';
    header('Location: admin.php#products');
    exit;
}

// 1. Ambil data produk untuk diisi di form (READ)
try {
    $stmt = $pdo->prepare('SELECT id, name, price, description, image FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
} catch (\PDOException $e) {
    $_SESSION['crud_error'] = 'Gagal memuat data produk: ' . $e->getMessage();
    header('Location: admin.php#products');
    exit;
}

if (!$product) {
    $_SESSION['crud_error'] = 'Produk tidak ditemukan.';
    header('Location: admin.php#products');
    exit;
}

// 2. Handle POST untuk update (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $newName = trim($_POST['name'] ?? '');
    $newPrice = intval($_POST['price'] ?? 0);
    $newDesc = trim($_POST['description'] ?? '');
    $newImage = trim($_POST['image'] ?? '');

    if (!empty($newName) && $newPrice > 0) {
        try {
            $sql = "UPDATE products SET name = :name, price = :price, description = :description, image = :image WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':name' => $newName,
                ':price' => $newPrice,
                ':description' => $newDesc,
                ':image' => $newImage,
                ':id' => $productId
            ]);
            
            header('Location: admin.php#products');
            exit;

        } catch (\PDOException $e) {
            $error = 'Gagal menyimpan perubahan produk. Error Database: ' . $e->getMessage();
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
    <title>Edit Produk - Admin</title>
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
            <h2 style="margin-bottom:1.5rem;">Edit Produk: <?php echo htmlspecialchars($product['name']); ?></h2>
            
            <?php if (!empty($error)): ?>
              <div style="color:#b65b5b;margin-bottom:1rem;border:1px solid #b65b5b;padding:0.75rem;background:#ffeeee;border-radius:6px;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="edit_product.php?id=<?php echo htmlspecialchars($productId); ?>">
                <label for="name" style="display:block;margin-bottom:0.5rem;">Nama Produk:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;">

                <label for="price" style="display:block;margin-bottom:0.5rem;">Harga (Rp):</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required min="0" style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;">
                
                <label for="description" style="display:block;margin-bottom:0.5rem;">Deskripsi:</label>
                <textarea id="description" name="description" style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;min-height: 80px;"><?php echo htmlspecialchars($product['description']); ?></textarea>

                <label for="image" style="display:block;margin-bottom:0.5rem;">URL/Path Gambar:</label>
                <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" style="width:100%;padding:0.6rem;margin-bottom:1.5rem;border:1px solid #ddd;border-radius:6px;">
                
                <button type="submit" name="edit_product" style="background:#007bff;color:#fff;padding:0.8rem 1.5rem;border-radius:8px;border:none;cursor:pointer;width:100%;">Simpan Perubahan</button>
            </form>
        </section>
    </main>
</body>
</html>