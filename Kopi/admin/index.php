<?php
require '../config.php';

// Proteksi halaman admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Logika untuk menandai pesanan sebagai 'diproses'
if (isset($_GET['tandai_diproses'])) {
    $order_id = $_GET['tandai_diproses'];
    $update_sql = "UPDATE orders SET status = 'diproses' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    header("Location: index.php"); // Refresh halaman agar status berubah
    exit;
}

// Ambil semua produk untuk ditampilkan
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

// Ambil semua pesanan baru untuk notifikasi dan daftar
$new_orders_query = mysqli_query($conn, "SELECT * FROM orders WHERE status = 'baru' ORDER BY created_at DESC");
$new_orders_count = mysqli_num_rows($new_orders_query);

// Ambil semua pesanan yang sudah diproses
$processed_orders_query = mysqli_query($conn, "SELECT * FROM orders WHERE status != 'baru' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Kopi AJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Kopi AJ</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="#pesanan" class="nav-link">
                            Pesanan Masuk
                            <?php if ($new_orders_count > 0): ?>
                                <span class="badge bg-danger"><?= $new_orders_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="btn btn-outline-light">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-5">
            <div class="col-md-4">
                <h3>Tambah Produk Baru</h3>
                <hr>
                <form action="add_product.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kopi</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" name="category" required>
                            <option value="espresso-based">Espresso Based</option>
                            <option value="manual-brew">Manual Brew</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga</label>
                        <input type="number" class="form-control" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Tambah Produk</button>
                </form>
            </div>

            <div class="col-md-8">
                <h3>Daftar Produk</h3>
                <hr>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><img src="../<?= htmlspecialchars($row['image']) ?>" width="80" alt=""></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td>Rp <?= number_format($row['price']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <hr class="my-5">
        <div id="pesanan">
            <h2 class="text-center mb-4">Pemberitahuan Pesanan</h2>
            
            <h3><i class="bi bi-bell-fill text-danger"></i> Pesanan Baru</h3>
            <div class="list-group mb-5">
                <?php if ($new_orders_count > 0): ?>
                    <?php while ($order = mysqli_fetch_assoc($new_orders_query)): ?>
                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Pesanan #<?= $order['id'] ?> - <?= htmlspecialchars($order['customer_name']) ?></h5>
                                <small><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></small>
                            </div>
                            <p class="mb-1">Total: <strong>Rp <?= number_format($order['total_price']) ?></strong></p>
        
                            <small class="text-muted d-block mt-2">Detail Pesanan:</small>
                            <ul>
                                <?php
                                $order_id = $order['id'];
                                $items_query = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $order_id");
                                while ($item = mysqli_fetch_assoc($items_query)) {
                                    echo '<li>' . htmlspecialchars($item['product_name']) . ' - <strong>' . $item['quantity'] . 'x</strong></li>';
                                }
                                ?>
                            </ul>
                            <a href="?tandai_diproses=<?= $order['id'] ?>" class="btn btn-sm btn-success mt-2">Tandai Sudah Diproses</a>
                        </div>
                <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">Tidak ada pesanan baru saat ini.</p>
                <?php endif; ?>
            </div>

            <h3><i class="bi bi-check-circle-fill text-success"></i> Riwayat Pesanan (Sudah Diproses)</h3>
            <div class="list-group">
                <?php while ($order = mysqli_fetch_assoc($processed_orders_query)): ?>
                    <div class="list-group-item list-group-item-action flex-column align-items-start list-group-item-light">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-muted">Pesanan #<?= $order['id'] ?> - <?= htmlspecialchars($order['customer_name']) ?></h5>
                            <small><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></small>
                        </div>
                        <p class="mb-1">Total: Rp <?= number_format($order['total_price']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>