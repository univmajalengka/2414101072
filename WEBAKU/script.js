/* =============================
   MaDrinks - script.js (Mengelola Cart, Review, Modals, dan History)
   ============================= */

// ========== REVIEW SYSTEM (Basic Local Storage) ==========
let reviews = [];

function loadReviews() {
  try {
    // Pastikan key local storage unik, disesuaikan dengan nama toko 'madriks'
    const saved = localStorage.getItem("madriks_reviews_v1");
    reviews = saved ? JSON.parse(saved) : [];
  } catch (e) {
    reviews = [];
  }
}

function saveReviews() {
  try {
    localStorage.setItem("madriks_reviews_v1", JSON.stringify(reviews));
  } catch (e) {}
}

function renderReviews() {
  const reviewList = document.getElementById("reviewList");
  if (!reviewList) return;

  reviewList.innerHTML = "";
  reviews.forEach((review) => {
    const li = document.createElement("li");
    li.style.cssText = "border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.75rem; color: #222;";
    li.innerHTML = `
      <div style="display:flex;align-items:center;gap:1rem;">
        ${
          review.photo
            ? `<img src="${review.photo}" alt="Foto" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">`
            : ""
        }
        <div>
          <strong>${review.name}:</strong> <span>${review.comment}</span>
        </div>
      </div>
    `;
    reviewList.appendChild(li);
  });
}

// Fungsi untuk mengunggah gambar ke server (perlu api/upload_image.php di folder root)
async function uploadReviewImage(file) {
    const formData = new FormData();
    formData.append('image', file);
    
    // Asumsikan ada endpoint untuk upload image di root/api/upload_review_image.php
    // Karena kita tidak memiliki endpoint tersebut, kita akan menggunakan mock up
    // Jika Anda ingin menyimpan gambar secara persisten, Anda harus membuat endpoint ini
    
    return new Promise(resolve => {
        // Simulasi upload berhasil dan mengembalikan path
        setTimeout(() => {
            const mockPath = URL.createObjectURL(file); // Menggunakan URL lokal sebagai preview
            resolve({ success: true, path: mockPath });
        }, 500);
    });
}


document.addEventListener("DOMContentLoaded", () => {
  loadReviews();
  renderReviews();

  const reviewForm = document.getElementById("reviewForm");
  if (reviewForm) {
    reviewForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const name = document.getElementById("reviewerName").value;
      const comment = document.getElementById("reviewerComment").value;
      const photoFile = document.getElementById("reviewPhoto").files[0];
      
      let photoPath = null;
      if (photoFile) {
          // Melakukan mock-up upload gambar
          const uploadResult = await uploadReviewImage(photoFile);
          if (uploadResult.success) {
              photoPath = uploadResult.path;
          }
      }

      const newReview = { name, comment, photo: photoPath };
      reviews.unshift(newReview); // Tambahkan ke depan
      saveReviews();
      renderReviews();
      reviewForm.reset();
      
      // Catatan: Jika ingin menyimpan review di server, Anda harus
      // memanggil API endpoint di sini setelah saveReviews() lokal.
    });
  }
});


// ========== CART SYSTEM (Basic Local Storage) ==========
let cart = [];
const cartModal = document.getElementById("cart-modal");
const cartBtn = document.getElementById("cartBtn");
const closeCartModal = document.getElementById("closeCartModal");
const cartList = document.getElementById("cartList");
const cartTotalDisplay = document.getElementById("cartTotal");
const cartBadge = document.getElementById("cartBadge");

function loadCart() {
  try {
    const saved = localStorage.getItem("madriks_cart_v1");
    cart = saved ? JSON.parse(saved) : [];
  } catch (e) {
    cart = [];
  }
}

function saveCart() {
  try {
    localStorage.setItem("madriks_cart_v1", JSON.stringify(cart));
    renderCart();
  } catch (e) {}
}

function renderCart() {
  cartList.innerHTML = "";
  let total = 0;

  if (cart.length === 0) {
    cartList.innerHTML = "<li style='color:#333; list-style:none; padding:10px 0;'>Keranjang kosong.</li>";
  } else {
    cart.forEach((item, index) => {
      const subtotal = item.price * item.quantity;
      total += subtotal;

      const li = document.createElement("li");
      li.style.cssText = "display:flex; justify-content:space-between; align-items:center; padding:5px 0; border-bottom:1px dotted #eee; color:#333;";
      li.innerHTML = `
        <span style="flex-grow:1;">${item.name} (${item.quantity}x)</span>
        <span style="min-width:100px; text-align:right;">Rp ${subtotal.toLocaleString('id-ID')}</span>
        <button onclick="updateCartQuantity(${index}, 1)" style="margin-left:10px; padding:2px 6px; background:#4CAF50; color:white; border:none; cursor:pointer;">+</button>
        <button onclick="updateCartQuantity(${index}, -1)" style="margin-left:5px; padding:2px 6px; background:#f44; color:white; border:none; cursor:pointer;">-</button>
      `;
      cartList.appendChild(li);
    });
  }

  cartTotalDisplay.textContent = total.toLocaleString('id-ID');
  cartBadge.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

function addToCart(productId, name, price) {
  const existingItem = cart.find(item => item.id === productId);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({ id: productId, name, price: parseFloat(price), quantity: 1 });
  }

  saveCart();
  alert(`${name} ditambahkan ke keranjang.`);
}

function updateCartQuantity(index, change) {
  const item = cart[index];
  item.quantity += change;

  if (item.quantity <= 0) {
    cart.splice(index, 1);
  }
  saveCart();
}

// --- Init Cart ---
loadCart();
renderCart();

// --- Event Listeners Cart ---
document.querySelectorAll(".add-to-cart").forEach(button => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    const card = e.target.closest(".menu-card");
    const id = card.getAttribute("data-id");
    const name = card.getAttribute("data-name");
    const price = card.getAttribute("data-price");
    addToCart(id, name, price);
  });
});

if (cartBtn) {
  cartBtn.addEventListener("click", (e) => {
    e.preventDefault();
    renderCart();
    if (cartModal) cartModal.style.display = "block";
  });
}

if (closeCartModal) {
  closeCartModal.addEventListener("click", () => {
    if (cartModal) cartModal.style.display = "none";
  });
}

// --- Checkout Logic ---
const checkoutForm = document.getElementById('checkoutForm');

if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (cart.length === 0) {
            alert('Keranjang Anda kosong!');
            return;
        }

        const customerName = document.getElementById('custName').value.trim();
        const customerPhone = document.getElementById('custPhone').value.trim();
        const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        if (!customerName || !customerPhone || totalAmount <= 0) {
            alert('Mohon isi nama dan nomor telepon, dan pastikan keranjang tidak kosong.');
            return;
        }

        const orderData = {
            customer_name: customerName,
            customer_phone: customerPhone,
            total: totalAmount,
            items: cart.map(item => ({
                product_id: item.id,
                product_name: item.name,
                price: item.price,
                quantity: item.quantity
            }))
        };

        try {
            // Memanggil api/checkout.php
            const response = await fetch('api/checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (result.success) {
                alert(`✅ Pesanan berhasil! ${result.message}\nTotal: Rp ${totalAmount.toLocaleString('id-ID')}`);
                
                // Reset keranjang dan form
                cart = [];
                saveCart();
                checkoutForm.reset();
                document.getElementById('cart-modal').style.display = 'none';
            } else {
                alert(`❌ Gagal Checkout: ${result.message}`);
            }
        } catch (error) {
            console.error('Error during checkout:', error);
            alert('Terjadi kesalahan jaringan saat memproses pesanan.');
        }
    });
}

// --- Navbar Hamburger Menu ---
const hamburger = document.getElementById("hamburger-menu");
const navbarNav = document.querySelector(".navbar-nav");

if (hamburger && navbarNav) {
    hamburger.addEventListener("click", (e) => {
        e.preventDefault();
        // Gunakan class CSS untuk toggle menu mobile
        navbarNav.classList.toggle("active");
    });

    document.addEventListener("click", function(e) {
        if (!hamburger.contains(e.target) && !navbarNav.contains(e.target)) {
            navbarNav.classList.remove("active");
        }
    });
}

// --- Admin/Login Modals ---
const adminModal = document.getElementById("admin-modal");
const adminBtn = document.getElementById("adminBtn");
const loginBtn = document.getElementById("loginBtn");
const closeAdmin = document.getElementById("closeAdminModal");

if (adminBtn)
  adminBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const inline = document.getElementById("admin-inline");
    if (inline) {
      inline.style.display = "flex";
      inline.scrollIntoView({ behavior: "smooth", block: "start" });
      return;
    }
    if (adminModal) adminModal.style.display = "flex";
  });
  
if (loginBtn)
  loginBtn.addEventListener("click", (e) => {
    e.preventDefault();
    // Gunakan admin-inline untuk kesederhanaan, atau modal auth-modal
    const inline = document.getElementById("admin-inline");
    const authModal = document.getElementById("auth-modal");
    
    if (authModal) { // Tampilkan modal login/register
        authModal.style.display = "flex";
        return;
    }
    
    if (inline) { // Jika tidak ada auth-modal, fallback ke admin inline
        inline.style.display = "flex";
        inline.scrollIntoView({ behavior: "smooth", block: "start" });
        return;
    }
    
    // Fallback jika tidak ada keduanya
    if (adminModal) adminModal.style.display = "flex";
  });
  
if (closeAdmin)
  closeAdmin.addEventListener("click", () => {
    if (adminModal) adminModal.style.display = "none";
  });
  
window.addEventListener("click", (e) => {
  if (e.target === adminModal) adminModal.style.display = "none";
});

// close inline admin form
const closeInline = document.getElementById("closeInlineAdmin");
if(closeInline) {
    closeInline.addEventListener("click", () => {
        const inline = document.getElementById("admin-inline");
        if(inline) inline.style.display = "none";
    });
}


// ======================================
// ========== ORDER HISTORY HANDLING ==========
// ======================================

const historyModal = document.getElementById('history-modal');
const historyBtn = document.getElementById('historyBtn');
const closeHistoryModal = document.getElementById('closeHistoryModal');
const historyForm = document.getElementById('historyForm');
const historyResult = document.getElementById('historyResult');

// B. Event Listeners untuk Membuka/Menutup Modal
if (historyBtn) {
    historyBtn.addEventListener('click', (e) => {
        e.preventDefault();
        historyModal.style.display = 'block';
    });
}

if (closeHistoryModal) {
    closeHistoryModal.addEventListener('click', () => {
        historyModal.style.display = 'none';
        // Opsional: Reset form dan hasil saat ditutup
        historyForm.reset();
        historyResult.innerHTML = '<p style="text-align:center; color:#888;">Masukkan nomor telepon untuk melihat pesanan.</p>';
    });
}

// C. Event Listener untuk Form Cek Riwayat
if (historyForm) {
    historyForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const phoneInput = document.getElementById('historyPhone');
        const phone = phoneInput.value.trim();

        if (!phone) return;

        // Tampilkan loading
        historyResult.innerHTML = '<p style="text-align:center; color:#007bff;">Memuat riwayat pesanan...</p>';

        try {
            // Memanggil endpoint API baru: api/order_history.php
            const response = await fetch('api/order_history.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ phone: phone })
            });
            const result = await response.json();

            if (result.success) {
                renderOrderHistory(result.history);
            } else {
                historyResult.innerHTML = `<p style="color:red; text-align:center;">Gagal: ${result.message}</p>`;
            }

        } catch (error) {
            historyResult.innerHTML = '<p style="color:red; text-align:center;">Terjadi kesalahan jaringan saat mengambil riwayat.</p>';
            console.error('Fetch History Error:', error);
        }
    });
}

// D. Fungsi untuk Merender Hasil Riwayat Pesanan
function renderOrderHistory(history) {
    if (history.length === 0) {
        historyResult.innerHTML = '<p style="text-align:center; color:#888;">Tidak ada riwayat pesanan yang ditemukan untuk nomor ini.</p>';
        return;
    }

    let html = '';
    
    // Membatasi tinggi agar tidak memenuhi layar
    html += '<div style="max-height: 400px; overflow-y: auto;">';

    history.forEach((order, index) => {
        // Format tanggal & total
        const date = new Date(order.created_at).toLocaleDateString('id-ID', {
            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });
        const total = Number(order.total).toLocaleString('id-ID');

        // Penentuan warna status
        let statusColor = 'gray';
        if (order.status === 'Completed') statusColor = 'green';
        else if (order.status === 'Processing') statusColor = 'orange';
        else if (order.status === 'Cancelled') statusColor = 'red';
        

        html += `
            <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 6px; background: ${index % 2 === 0 ? '#f9f9f9' : '#fff'};">
                <p><strong>Pesanan #${order.id}</strong></p>
                <p>Status: <strong style="color: ${statusColor};">${order.status}</strong></p>
                <p>Tanggal: ${date}</p>
                <p>Total: Rp ${total}</p>
                
                <h4 style="margin-top: 5px; font-size: 1rem;">Detail Item:</h4>
                <ul style="list-style: disc; padding-left: 20px; margin-left: 0px;">
        `;
        
        order.items.forEach(item => {
            const itemPrice = Number(item.price).toLocaleString('id-ID');
            html += `
                <li style="color:#444;">${item.product_name} (${item.quantity}x) - @Rp ${itemPrice}</li>
            `;
        });
        
        html += `
                </ul>
            </div>
        `;
    });
    
    html += '</div>';
    historyResult.innerHTML = html;
}