<?php
/**
 * File ini untuk inisialisasi data awal MaDrinks
 * Jalankan sekali: php init.php dari command line
 * atau akses: http://localhost:8000/init.php di browser
 */

// Buat direktori data jika belum ada
if (!is_dir('data')) {
    mkdir('data', 0755, true);
    echo "✓ Folder 'data' berhasil dibuat\n";
} else {
    echo "✓ Folder 'data' sudah ada\n";
}

// Buat direktori api jika belum ada
if (!is_dir('api')) {
    mkdir('api', 0755, true);
    echo "✓ Folder 'api' berhasil dibuat\n";
} else {
    echo "✓ Folder 'api' sudah ada\n";
}

// Buat direktori img jika belum ada
if (!is_dir('img')) {
    mkdir('img', 0755, true);
    echo "✓ Folder 'img' berhasil dibuat\\n";
} else {
    echo "✓ Folder 'img' sudah ada\\n";
}

// Inisialisasi file reviews.json dengan data default
$reviewsFile = 'data/reviews.json';
$defaultReviews = [
    [
        'name' => 'Andi',
        'comment' => 'Makanannya enak dan pelayanannya ramah!',
        'photo' => null
    ],
    [
        'name' => 'Siti',
        'comment' => 'Suasana restoran sangat nyaman untuk keluarga.',
        'photo' => null
    ],
    [
        'name' => 'Budi',
        'comment' => 'Menu favorit saya adalah rendang dan sop iga!',
        'photo' => null
    ]
];

if (!file_exists($reviewsFile)) {
    file_put_contents($reviewsFile, json_encode($defaultReviews, JSON_PRETTY_PRINT));
    echo "✓ File 'data/reviews.json' berhasil dibuat dengan data default\n";
} else {
    echo "✓ File 'data/reviews.json' sudah ada\n";
}

// Inisialisasi file orders.json (kosong)
$ordersFile = 'data/orders.json';
if (!file_exists($ordersFile)) {
    file_put_contents($ordersFile, json_encode([]));
    echo "✓ File 'data/orders.json' berhasil dibuat\n";
} else {
    echo "✓ File 'data/orders.json' sudah ada\n";
}

// NEW: Inisialisasi file products.json (Menu Item)
$productsFile = 'data/products.json';
$defaultProducts = [
    [
        'id' => uniqid(),
        'name' => 'Es Kopi Susu',
        'price' => 18000,
        'description' => 'Espresso dengan susu dan gula aren.',
        'image' => 'img/kopi-susu.jpg' 
    ],
    [
        'id' => uniqid(),
        'name' => 'Matcha Latte',
        'price' => 25000,
        'description' => 'Minuman teh hijau premium Jepang.',
        'image' => 'img/matcha.jpg'
    ],
    [
        'id' => uniqid(),
        'name' => 'Red Velvet',
        'price' => 22000,
        'description' => 'Minuman dengan rasa kue red velvet.',
        'image' => 'img/red-velvet.jpg'
    ]
];

if (!file_exists($productsFile)) {
    file_put_contents($productsFile, json_encode($defaultProducts, JSON_PRETTY_PRINT));
    echo "✓ File 'data/products.json' berhasil dibuat dengan data default\n";
} else {
    echo "✓ File 'data/products.json' sudah ada\n";
}
?>