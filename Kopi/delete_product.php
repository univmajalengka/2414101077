<?php
session_start();
require '../config.php';

// Proteksi halaman admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hati-hati: Sebaiknya hapus juga file gambar dari server
    // 1. Ambil nama file gambar
    $stmt_get = mysqli_prepare($conn, "SELECT image FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $id);
    mysqli_stmt_execute($stmt_get);
    $result = mysqli_stmt_get_result($stmt_get);
    if ($product = mysqli_fetch_assoc($result)) {
        $image_path = "../" . $product['image'];
    }

    // 2. Hapus data dari database
    $stmt_del = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $id);
    
    if (mysqli_stmt_execute($stmt_del)) {
        // 3. Jika data di DB berhasil dihapus, hapus file gambar
        if (isset($image_path) && file_exists($image_path)) {
            unlink($image_path); // Hapus file gambar
        }
    } else {
        echo "Error: Gagal menghapus produk.";
    }

    header("Location: index.php");
    exit;

} else {
    header("Location: index.php");
    exit;
}
?>