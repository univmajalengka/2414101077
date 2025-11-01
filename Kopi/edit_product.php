<?php
session_start();
require '../config.php';

// Proteksi halaman admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Logika 1: Saat Form di-submit (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Cek apakah ada gambar baru yang di-upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Ada gambar baru
        $target_dir = "../img/"; // Asumsi folder gambar di luar folder admin
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $image_db_path = "img/" . $image_name; // Path untuk disimpan ke DB

        // Pindahkan file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update database dengan gambar baru
            $sql = "UPDATE products SET name = ?, category = ?, price = ?, image = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssisi", $name, $category, $price, $image_db_path, $id);
        } else {
            $error = "Gagal meng-upload gambar baru.";
        }
    } else {
        // Tidak ada gambar baru, update tanpa mengubah gambar
        $sql = "UPDATE products SET name = ?, category = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssii", $name, $category, $price, $id);
    }

    if (empty($error) && mysqli_stmt_execute($stmt)) {
        $success = "Produk berhasil diperbarui!";
        // Redirect kembali ke index setelah beberapa saat
        header("Refresh: 2; URL=index.php");
    } else if (empty($error)) {
        $error = "Gagal memperbarui produk.";
    }
}

// Logika 2: Ambil data produk untuk ditampilkan di form (GET)
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - Kopi AJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Kopi AJ - Edit Produk</a>
            <a href="index.php" class="btn btn-outline-light">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container my-5" style="max-width: 600px;">
        <h3>Edit Produk: <?= htmlspecialchars($product['name']) ?></h3>
        <hr>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form action="edit_product.php?id=<?= $product['id'] ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Kopi</label>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select class="form-select" name="category" required>
                    <option value="espresso-based" <?= ($product['category'] == 'espresso-based') ? 'selected' : '' ?>>
                        Espresso Based
                    </option>
                    <option value="manual-brew" <?= ($product['category'] == 'manual-brew') ? 'selected' : '' ?>>
                        Manual Brew
                    </option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Harga</label>
                <input type="number" class="form-control" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini</label><br>
                <img src="../<?= htmlspecialchars($product['image']) ?>" width="150" alt="Gambar Produk" class="img-thumbnail mb-2">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ubah Gambar Produk</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>