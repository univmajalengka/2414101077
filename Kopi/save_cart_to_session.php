<?php
require 'config.php';

// Membaca data JSON yang dikirim dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['cart'])) {
    // Simpan data keranjang ke session
    $_SESSION['cart'] = $data['cart'];
    
    // Kirim response berhasil
    echo json_encode(['success' => true]);
} else {
    // Kirim response error jika data tidak valid
    echo json_encode(['success' => false, 'message' => 'Data keranjang tidak ditemukan.']);
}
?>