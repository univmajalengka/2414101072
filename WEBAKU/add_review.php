<?php
session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: admin.php');
    exit;
}

// Helper functions (Duplikat dari admin.php)
function loadReviews() {
    $file = __DIR__ . '/data/reviews.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true) ?? [];
    }
    return [];
}
function saveData($data, $type) {
    $file = __DIR__ . '/data/reviews.json';
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $json) !== false;
}
// END Helper functions

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $newReview = [
        'name' => trim($_POST['review_name'] ?? ''),
        'comment' => trim($_POST['review_comment'] ?? ''),
        'photo' => trim($_POST['review_photo'] ?? '')
    ];

    if (!empty($newReview['name']) && !empty($newReview['comment'])) {
        $reviews = loadReviews();
        $reviews[] = $newReview;
        if (saveData($reviews, 'reviews')) {
            header('Location: admin.php#reviews-list');
            exit;
        } else {
            $error = 'Gagal menyimpan review baru.';
        }
    } else {
        $error = 'Nama dan komentar review tidak boleh kosong.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Review - Admin</title>
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
            <h2 style="margin-bottom:1.5rem;">Tambah Review Baru</h2>
            
            <?php if (!empty($error)): ?>
              <div style="color:#b65b5b;margin-bottom:1rem;border:1px solid #b65b5b;padding:0.75rem;background:#ffeeee;border-radius:6px;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="add_review.php">
                <label for="review_name" style="display:block;margin-bottom:0.5rem;">Nama Reviewer:</label>
                <input type="text" id="review_name" name="review_name" placeholder="Nama Reviewer" required style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;">

                <label for="review_photo" style="display:block;margin-bottom:0.5rem;">URL Foto (Opsional):</label>
                <input type="text" id="review_photo" name="review_photo" placeholder="URL Foto" style="width:100%;padding:0.6rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:6px;">

                <label for="review_comment" style="display:block;margin-bottom:0.5rem;">Komentar Review:</label>
                <textarea id="review_comment" name="review_comment" placeholder="Komentar Review" required style="width:100%;padding:0.6rem;margin-bottom:1.5rem;border:1px solid #ddd;border-radius:6px;min-height: 120px;"></textarea>
                
                <button type="submit" name="add_review" style="background:#5cb85c;color:#fff;padding:0.8rem 1.5rem;border-radius:8px;border:none;cursor:pointer;width:100%;">Simpan Review</button>
            </form>
        </section>
    </main>
</body>
</html>