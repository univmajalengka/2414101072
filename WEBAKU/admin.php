<?php
session_start();

// WAJIB: Sertakan file konfigurasi database
require_once 'db_config.php';

// Simple admin credentials for local development
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'admin123';
$error = '';
$orders = [];
$products = []; // Array untuk menyimpan data produk

// Handle login submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u === $ADMIN_USER && $p === $ADMIN_PASS) {
        $_SESSION['isAdmin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}

// If user requested logout via query param, destroy session
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// --- FUNGSI LOAD DATA DARI DATABASE (PDO) ---

function loadProducts() {
    $pdo = getDB(); // getDB() dipanggil di sini
    try {
        $stmt = $pdo->query('SELECT id, name, price, description, image FROM products ORDER BY name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        return ['error' => 'Gagal memuat produk: Pastikan tabel products ada. Detail: ' . $e->getMessage()];
    }
}

function loadOrders() {
    $pdo = getDB(); // getDB() dipanggil di sini
    try {
        $stmt = $pdo->query('SELECT id, customer_name, customer_phone, total, status, created_at FROM orders ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        return ['error' => 'Gagal memuat pesanan: Pastikan tabel orders ada. Detail: ' . $e->getMessage()];
    }
}

function loadOrderItems($orderId) {
    $pdo = getDB(); // getDB() dipanggil di sini
    try {
        $stmt = $pdo->prepare('SELECT product_name, price, quantity FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        return ['error' => 'Gagal memuat detail item: ' . $e->getMessage()];
    }
}

function loadReviews() {
    $file = __DIR__ . '/data/reviews.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true) ?? [];
    }
    return [];
}


// --- LOGIC HALAMAN ADMIN (HANYA DIEKSEKUSI SETELAH LOGIN) ---
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true) {
    
    // Handle AJAX request untuk mengambil detail pesanan
    if (isset($_GET['action']) && $_GET['action'] === 'get_order_details' && isset($_GET['id'])) {
        $orderId = intval($_GET['id']);
        $details = loadOrderItems($orderId);
        header('Content-Type: application/json');
        echo json_encode($details);
        exit;
    }
    
    // Muat semua data yang diperlukan (Hanya setelah login)
    $productData = loadProducts();
    $orderData = loadOrders();
    $reviews = loadReviews();
    
    // Cek error produk
    if (isset($productData['error'])) {
        $error = $productData['error'];
        $products = [];
    } else {
        $products = $productData;
    }

    // Cek error pesanan
    if (isset($orderData['error'])) {
        if ($error) {
            $error .= " | " . $orderData['error'];
        } else {
            $error = $orderData['error'];
        }
        $orders = [];
    } else {
        $orders = $orderData;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MaDrinks</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #b65b5b; padding-bottom: 10px; margin-bottom: 20px; }
        .admin-header h2 { margin: 0; color: #b65b5b; }
        .logout-btn { background: #b65b5b; color: #fff; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .login-form { max-width: 400px; margin: 100px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .login-form input, .login-form button { width: 100%; padding: 10px; margin-bottom: 10px; box-sizing: border-box; }
        .login-form button { background: #b65b5b; color: #fff; border: none; cursor: pointer; }
        .error-message { color: #fff; background-color: #f44; padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .admin-section { margin-bottom: 30px; }
        .admin-section h3 { border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; }
        table { border: 1px solid #ddd; width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; text-align: left; padding: 10px; }
        
        /* Modal for Order Details */
        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
        .detail-item { margin-bottom: 5px; }
        .status-update-box { margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee; }
        .status-update-box select { padding: 8px; margin-right: 10px; }
        .status-update-box button { background: #4CAF50; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
        
        /* Form Produk */
        #productForm { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; border: 1px solid #eee; padding: 15px; border-radius: 8px; }
        #productForm input[type="text"], 
        #productForm input[type="number"], 
        #productForm textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        #productForm button { background: #5cb85c; color: #fff; border: none; padding: 10px; cursor: pointer; grid-column: 1 / -1; }
        #productForm .form-group { grid-column: 1 / -1; }
        #productForm .form-group:nth-child(3),
        #productForm .form-group:nth-child(4) { grid-column: auto; }
        #productImagePreview { max-width: 100px; height: auto; margin-top: 10px; display: block; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true): ?>
    <div class="login-form">
        <h2>Admin Login</h2>
        <?php if ($error): ?><p class="error-message" style="background:#f0ad4e;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        <form method="POST" action="admin.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="hidden" name="login" value="1">
            <button type="submit">Login</button>
        </form>
    </div>
<?php else: ?>
    <div class="container">
        <div class="admin-header">
            <h2>Dashboard Admin MaDrinks</h2>
            <a href="admin.php?action=logout" class="logout-btn">Logout</a>
        </div>

        <?php if ($error): ?>
            <p class="error-message">⚠️ **PERINGATAN KONEKSI/DATA**: <?php echo nl2br(htmlspecialchars($error)); ?></p>
        <?php endif; ?>

        <section id="products" class="admin-section">
            <h3>Kelola Produk (CRUD)</h3>
            
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="id" value="">
                
                <div class="form-group">
                    <label for="productName">Nama Produk:</label>
                    <input type="text" id="productName" name="name" required>
                </div>
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="productDescription">Deskripsi:</label>
                    <textarea id="productDescription" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="productPrice">Harga (Rp):</label>
                    <input type="number" id="productPrice" name="price" required min="0">
                </div>
                
                <div class="form-group">
                    <label for="productImage">Gambar Produk (.jpg/.png):</label>
                    <input type="file" id="productImage" name="image" accept="image/*">
                    <input type="hidden" id="currentImage" name="current_image">
                    <img id="productImagePreview" src="" alt="Preview Gambar" style="display:none;"/>
                </div>
                
                <button type="submit" id="submitProductBtn">Tambah Produk Baru</button>
                <button type="button" id="cancelEditBtn" style="background:#f0ad4e; display:none;">Batal Edit</button>
            </form>

            <table style="width:100%; border-collapse: collapse; margin-top:20px;">
                <thead>
                    <tr style="background:#f4f4f4; border-bottom:2px solid #ddd;">
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php foreach ($products as $product): ?>
                    <tr data-id="<?php echo htmlspecialchars($product['id']); ?>" style="border-bottom:1px solid #eee;">
                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($product['image'] ?? 'img/default.jpg'); ?>" style="width:50px; height:50px; object-fit: cover;"></td>
                        <td>
                            <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" style="background:#007bff; color:#fff; border:none; padding:5px 10px; cursor:pointer;">Edit</button>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" style="background:#dc3545; color:#fff; border:none; padding:5px 10px; cursor:pointer;">Hapus</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>


        <section id="orders" class="admin-section">
            <h3>Daftar Pesanan (MySQL)</h3>
            <?php if (count($orders) > 0): ?>
            <table style="width:100%; border-collapse: collapse; margin-top:10px;">
                <thead>
                    <tr style="background:#f4f4f4; border-bottom:2px solid #ddd;">
                        <th>ID</th>
                        <th>Nama Customer</th>
                        <th>Telp</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr data-id="<?php echo htmlspecialchars($order['id']); ?>" style="border-bottom:1px solid #eee;">
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                        <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                        <td id="status-<?php echo $order['id']; ?>"><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)">Detail</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>Belum ada pesanan.</p>
            <?php endif; ?>
        </section>

        <section id="reviews" class="admin-section">
            <h3>Daftar Review Pelanggan</h3>
            <?php if (count($reviews) > 0): ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($reviews as $index => $review): ?>
                <li style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                    <strong><?php echo htmlspecialchars($review['name']); ?>:</strong> 
                    <?php echo htmlspecialchars($review['comment']); ?>
                    <?php if (isset($review['photo']) && $review['photo']): ?>
                        <br><small>Ada foto</small>
                    <?php endif; ?>
                    <a href="delete_review.php?id=<?php echo $index; ?>" style="float: right; color: #b65b5b;">[Hapus]</a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p>Belum ada review.</p>
            <?php endif; ?>
        </section>
    </div>

    <div id="orderDetailModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle">Detail Pesanan #<span id="orderIdDisplay"></span></h3>
            <p><strong>Customer:</strong> <span id="customerNameDetail"></span></p>
            <p><strong>Total:</strong> <span id="orderTotalDetail"></span></p>
            <p><strong>Status Saat Ini:</strong> <span id="orderStatusDetail"></span></p>
            
            <h4 style="margin-top:15px;">Item Pesanan:</h4>
            <div id="itemsList">
                </div>

            <div class="status-update-box">
                <h4>Ubah Status</h4>
                <select id="newStatusSelect">
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button onclick="updateOrderStatus()">Simpan Status</button>
                <p id="statusFeedback" style="color: green; margin-top: 5px;"></p>
            </div>
        </div>
    </div>

    <script>
        // Data Produk (agar tidak perlu refresh saat CRUD)
        let products = <?php echo json_encode($products); ?>;

        // --- Order Handling ---
        let currentOrderId = null;

        function closeModal() {
            document.getElementById('orderDetailModal').style.display = 'none';
        }

        // Fungsi untuk menampilkan detail pesanan (memanggil endpoint di admin.php via AJAX)
        async function viewOrderDetails(orderId) {
            currentOrderId = orderId;
            const statusCell = document.getElementById(`status-${orderId}`);
            if (!statusCell) {
                alert('Detail pesanan tidak ditemukan di tabel.');
                return;
            }
            const orderRow = statusCell.closest('tr');
            
            // Ambil data dasar dari tabel di halaman
            const customerName = orderRow.children[1].textContent;
            const orderTotal = orderRow.children[3].textContent;
            const orderStatus = orderRow.children[4].textContent;

            document.getElementById('orderIdDisplay').textContent = orderId;
            document.getElementById('customerNameDetail').textContent = customerName;
            document.getElementById('orderTotalDetail').textContent = orderTotal;
            document.getElementById('orderStatusDetail').textContent = orderStatus;
            document.getElementById('newStatusSelect').value = orderStatus;
            document.getElementById('statusFeedback').textContent = '';
            
            document.getElementById('orderDetailModal').style.display = 'block';

            const itemsList = document.getElementById('itemsList');
            itemsList.innerHTML = '<p>Memuat detail item...</p>';
            
            try {
                // Memanggil admin.php dengan action khusus untuk mendapatkan detail item
                const response = await fetch(`admin.php?action=get_order_details&id=${orderId}`);
                const items = await response.json();

                itemsList.innerHTML = '';
                if (items.error) {
                    itemsList.innerHTML = `<p style="color:red;">Error: ${items.error}</p>`;
                    return;
                }

                if (items.length > 0) {
                    items.forEach(item => {
                        const subtotal = item.price * item.quantity;
                        itemsList.innerHTML += `
                            <div class="detail-item">
                                <strong>${item.product_name}</strong> (${item.quantity}x) - 
                                Rp ${Number(item.price).toLocaleString('id-ID')} (Subtotal: Rp ${subtotal.toLocaleString('id-ID')})
                            </div>
                        `;
                    });
                } else {
                    itemsList.innerHTML = '<p>Tidak ada detail item yang ditemukan.</p>';
                }

            } catch (error) {
                itemsList.innerHTML = '<p style="color:red;">Gagal memuat detail pesanan.</p>';
                console.error('Fetch error:', error);
            }
        }

        // Fungsi untuk mengubah status pesanan (memanggil api/update_order_status.php)
        async function updateOrderStatus() {
            if (!currentOrderId) return;

            const newStatus = document.getElementById('newStatusSelect').value;
            const feedback = document.getElementById('statusFeedback');
            feedback.textContent = 'Memperbarui...';
            feedback.style.color = 'orange';

            try {
                // Endpoint yang akan menangani perubahan status
                const response = await fetch('api/update_order_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: currentOrderId, status: newStatus })
                });

                const result = await response.json();

                if (result.success) {
                    feedback.textContent = 'Status berhasil diperbarui!';
                    feedback.style.color = 'green';
                    // Perbarui status di modal dan tabel utama
                    document.getElementById('orderStatusDetail').textContent = newStatus;
                    document.getElementById(`status-${currentOrderId}`).textContent = newStatus;
                } else {
                    feedback.textContent = `Gagal: ${result.message}`;
                    feedback.style.color = 'red';
                }
            } catch (error) {
                feedback.textContent = 'Terjadi kesalahan jaringan.';
                feedback.style.color = 'red';
                console.error('Update status error:', error);
            }
        }
        
        // --- Product CRUD Handling ---

        document.getElementById('productForm').addEventListener('submit', handleProductSubmit);
        document.getElementById('cancelEditBtn').addEventListener('click', resetProductForm);

        function resetProductForm() {
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('submitProductBtn').textContent = 'Tambah Produk Baru';
            document.getElementById('cancelEditBtn').style.display = 'none';
            document.getElementById('productImage').required = false; 
            document.getElementById('productImagePreview').style.display = 'none';
            document.getElementById('productImagePreview').src = '';
            document.getElementById('currentImage').value = '';
        }

        function editProduct(product) {
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('currentImage').value = product.image;
            
            document.getElementById('submitProductBtn').textContent = 'Simpan Perubahan';
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            document.getElementById('productImage').required = false; 
            
            const preview = document.getElementById('productImagePreview');
            if (product.image) {
                preview.src = product.image;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            
            document.getElementById('productForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        async function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const response = await fetch('api/upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                return result; 
            } catch (error) {
                console.error('Error uploading image:', error);
                return { success: false, message: 'Gagal mengunggah gambar. Pastikan api/upload_image.php ada.' };
            }
        }

        async function handleProductSubmit(e) {
            e.preventDefault();

            const productId = document.getElementById('productId').value;
            const isEdit = !!productId;
            
            const name = document.getElementById('productName').value;
            const price = document.getElementById('productPrice').value;
            const description = document.getElementById('productDescription').value;
            const imageFile = document.getElementById('productImage').files[0];
            const currentImage = document.getElementById('currentImage').value;
            
            let imagePath = currentImage; 
            
            if (imageFile) {
                const uploadResult = await uploadImage(imageFile);
                if (uploadResult.success) {
                    imagePath = uploadResult.path;
                } else {
                    alert('Gagal mengunggah gambar: ' + uploadResult.message);
                    return;
                }
            } else if (!isEdit) {
                imagePath = 'img/default.jpg'; 
            }

            const productData = {
                id: productId,
                name: name,
                price: price,
                description: description,
                image: imagePath
            };

            const method = isEdit ? 'PUT' : 'POST';
            
            try {
                const response = await fetch('api/products.php', {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });
                
                const result = await response.json();

                if (result.success) {
                    alert(isEdit ? 'Produk berhasil diubah!' : 'Produk berhasil ditambahkan!');
                    window.location.reload(); 

                } else {
                    alert('Gagal memproses produk: ' + result.message);
                }

            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
                console.error('CRUD Error:', error);
            }
        }
        
        async function deleteProduct(productId) {
            if (!confirm(`Yakin ingin menghapus produk dengan ID ${productId}?`)) return;

            try {
                const response = await fetch('api/products.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: productId })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Produk berhasil dihapus!');
                    window.location.reload(); 
                } else {
                    alert('Gagal menghapus produk: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan saat menghapus produk.');
                console.error('Delete Error:', error);
            }
        }

    </script>
<?php endif; ?>
</body>
</html>