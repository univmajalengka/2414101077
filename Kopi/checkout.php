<?php
require 'config.php';

// 1. Proteksi Halaman: Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout");
    exit;
}

// 2. Proteksi Halaman: Pastikan ada keranjang di session
if (empty($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: index.php"); // Redirect ke index jika keranjang kosong
    exit;
}

$cart = $_SESSION['cart'];
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

$error = '';
$success = '';

// 3. Proses Form Saat di-Submit (Method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customerName'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $purchaseDate = date("Y-m-d H:i:s"); // Tanggal hari ini
    $userId = $_SESSION['user_id'];

    // Validasi sederhana
    if (empty($customerName) || empty($address) || empty($phone)) {
        $error = "Semua field wajib diisi!";
    } else {
        
        // Mulai transaksi database (Copy dari place_order.php)
        mysqli_begin_transaction($conn);

        try {
            // Asumsi Anda punya kolom 'user_id', 'address', 'phone' di tabel 'orders'
            // Jika belum, sesuaikan query INSERT ini
            $sql_order = "INSERT INTO orders (user_id, customer_name, order_date, total_price, status, address, phone) 
                          VALUES (?, ?, ?, ?, 'baru', ?, ?)";
            $stmt_order = mysqli_prepare($conn, $sql_order);
            mysqli_stmt_bind_param($stmt_order, "ississ", $userId, $customerName, $purchaseDate, $totalPrice, $address, $phone);
            mysqli_stmt_execute($stmt_order);
            
            $order_id = mysqli_insert_id($conn);

            // Masukkan setiap item ke tabel 'order_items'
            $sql_items = "INSERT INTO order_items (order_id, product_name, quantity, price_per_item) VALUES (?, ?, ?, ?)";
            $stmt_items = mysqli_prepare($conn, $sql_items);
            
            foreach ($cart as $item) {
                mysqli_stmt_bind_param($stmt_items, "isii", $order_id, $item['name'], $item['quantity'], $item['price']);
                mysqli_stmt_execute($stmt_items);
            }
            
            // Jika semua berhasil, commit transaksi
            mysqli_commit($conn);

            // Kosongkan keranjang di session
            unset($_SESSION['cart']);

            // Set pesan sukses dan redirect
            $_SESSION['order_success'] = "Pesanan Anda (ID: $order_id) berhasil dibuat! Terima kasih.";
            header("Location: index.php");
            exit;

        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            $error = "Gagal membuat pesanan. Silakan coba lagi. Error: " . $exception->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kopi AJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Kopi AJ</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php#menu">Menu</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Hai, <?= htmlspecialchars($_SESSION['username']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/index.php">Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="btn btn-outline-light me-2">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <h2 class="text-center mb-4">Konfirmasi Pesanan</h2>
        
        <div class="row g-5">
            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-dark">Ringkasan Pesanan</span>
                    <span class="badge bg-dark rounded-pill"><?= count($cart) ?></span>
                </h4>
                <ul class="list-group mb-3">
                    <?php foreach ($cart as $item): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                            <small class="text-muted">Kuantitas: <?= $item['quantity'] ?></small>
                        </div>
                        <span class="text-muted">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Total (IDR)</strong>
                        <strong>Rp <?= number_format($totalPrice, 0, ',', '.') ?></strong>
                    </li>
                </ul>
            </div>

            <div class="col-md-7 col-lg-8">
                <h4 class="mb-3">Alamat Pengiriman</h4>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form action="checkout.php" method="POST" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="customerName" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="customerName" name="customerName" 
                                   value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="08..." required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <button class="w-100 btn btn-dark btn-lg" type="submit">Buat Pesanan</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p>&copy; 2025 Kopi AJ. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>