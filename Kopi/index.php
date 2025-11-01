<?php
require 'config.php'; // Gunakan file config terpusat

// Ambil data produk dari database
$result = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi AJ - Pesan Kopimu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">Kopi AJ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#menu">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#gallery">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Kontak</a></li>
    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/index.php">Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-outline-light position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                                <i class="bi bi-cart"></i> Keranjang
                                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                            </button>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="register.php" class="btn btn-warning">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="jumbotron text-white text-center">
        <div class="container">
            <h1 class="display-4">Selamat Datang di Kopi AJ</h1>
            <p class="lead">Nikmati secangkir kopi terbaik untuk menemani Aa Jem.</p>
        </div>
    </header>

    <main id="menu" class="container my-5">
        <h2 class="text-center mb-4">Menu Kami</h2>
        <div class="text-center mb-4 btn-group" role="group">
            <button type="button" class="btn btn-outline-dark active filter-btn" data-category="all">Semua</button>
            <button type="button" class="btn btn-outline-dark filter-btn" data-category="espresso-based">Espresso Based</button>
            <button type="button" class="btn btn-outline-dark filter-btn" data-category="manual-brew">Manual Brew</button>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="menu-list">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col product-item" data-category="<?= $row['category']; ?>">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $row['image']; ?>" class="card-img-top" alt="<?= $row['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $row['name']; ?></h5>
                            <p class="card-text">Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="card-footer bg-white border-0">
                             <button class="btn btn-dark w-100 mb-2 add-to-cart-btn" 
                                data-id="<?= $row['id']; ?>" 
                                data-name="<?= $row['name']; ?>" 
                                data-price="<?= $row['price']; ?>">
                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                            </button>
                            <button class="btn btn-success w-100 buy-now-btn"
                                data-id="<?= $row['id']; ?>" 
                                data-name="<?= $row['name']; ?>" 
                                data-price="<?= $row['price']; ?>">
                                Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
    
    <section id="gallery" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Galeri Kami</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <img src="img/latte.jpg" class="img-fluid rounded shadow" alt="Gallery Image 1">
                </div>
                <div class="col-md-4 mb-4">
                    <img src="img/v60.jpg" class="img-fluid rounded shadow" alt="Gallery Image 2">
                </div>
                <div class="col-md-4 mb-4">
                    <img src="img/cappuccino.jpg" class="img-fluid rounded shadow" alt="Gallery Image 3">
                </div>
            </div>
        </div>
    </section>

    <footer id="contact" class="bg-dark text-white text-center py-4">
        <div class="container">
            <p>&copy; 2025 Kopi AJ. All Rights Reserved.</p>
            <div>
                <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
    </footer>
    
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Keranjang Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items-container">
                        </div>
                    <hr>
                    <div class="text-end">
                        <h4>Total: <span id="cart-total">Rp 0</span></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lanjut Belanja</button>
                    <button type="button" class="btn btn-success" id="checkout-btn">Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Pemesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Nama Pemesan</label>
                            <input type="text" class="form-control" id="customerName" value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="purchaseDate" class="form-label">Tanggal Pembelian</label>
                            <input type="date" class="form-control" id="purchaseDate" required>
                        </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Pesan Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
