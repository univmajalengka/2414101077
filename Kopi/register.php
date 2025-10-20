<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        // Cek apakah username sudah ada
        $sql_check = "SELECT id FROM users WHERE username = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Username sudah digunakan, silakan pilih yang lain.";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql_insert = "INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, "ss", $username, $hashed_password);

            if (mysqli_stmt_execute($stmt_insert)) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Kopi Senja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f0e1; }
        .register-container { max-width: 400px; margin: 5rem auto; padding: 2rem; background: white; border-radius: 0.5rem; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center mb-4">Buat Akun</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php else: ?>
        <form action="register.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Register</button>
        </form>
        <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        <?php endif; ?>
    </div>
</body>
</html>