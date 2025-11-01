<?php
require '../config.php';

// Proteksi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Logika upload gambar
    $target_dir = "../img/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $image_path_for_db = "img/" . $image_name; // Path yang disimpan ke DB

    // Coba upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Jika berhasil, masukkan data ke database
        $sql = "INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssis", $name, $category, $price, $image_path_for_db);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?status=success");
        } else {
            header("Location: index.php?status=error");
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: index.php?status=upload_error");
    }
}
?>