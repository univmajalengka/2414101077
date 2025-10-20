<?php
require 'config.php';

// Membaca data JSON yang dikirim dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $customerName = $data['customerName'];
    $purchaseDate = $data['purchaseDate'];
    $cartItems = $data['items'];
    $totalPrice = 0;

    // Hitung total harga di sisi server untuk keamanan
    foreach ($cartItems as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }

    // Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // 1. Masukkan ke tabel 'orders'
        $sql_order = "INSERT INTO orders (customer_name, order_date, total_price, status) VALUES (?, ?, ?, 'baru')";
        $stmt_order = mysqli_prepare($conn, $sql_order);
        mysqli_stmt_bind_param($stmt_order, "ssi", $customerName, $purchaseDate, $totalPrice);
        mysqli_stmt_execute($stmt_order);
        
        // Dapatkan ID dari pesanan yang baru saja dibuat
        $order_id = mysqli_insert_id($conn);

        // 2. Masukkan setiap item ke tabel 'order_items'
        $sql_items = "INSERT INTO order_items (order_id, product_name, quantity, price_per_item) VALUES (?, ?, ?, ?)";
        $stmt_items = mysqli_prepare($conn, $sql_items);
        
        foreach ($cartItems as $item) {
            mysqli_stmt_bind_param($stmt_items, "isii", $order_id, $item['name'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt_items);
        }
        
        // Jika semua berhasil, commit transaksi
        mysqli_commit($conn);

        // Kirim response berhasil ke JavaScript
        echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dibuat!']);

    } catch (mysqli_sql_exception $exception) {
        // Jika ada error, batalkan semua perubahan
        mysqli_rollback($conn);
        
        // Kirim response error
        echo json_encode(['success' => false, 'message' => 'Gagal membuat pesanan.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
}

?>