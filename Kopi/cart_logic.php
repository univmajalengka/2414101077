<?php
session_start();
require 'config.php'; // Hubungkan ke database

// Jika pengguna tidak login, tolak aksi
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu.']);
    exit;
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_REQUEST['action'] ?? null;
header('Content-Type: application/json'); // Set header output ke JSON

switch ($action) {
    case 'add':
        add_to_cart();
        break;
    case 'get':
        get_cart_contents();
        break;
    case 'update':
        update_cart_item();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
}

function get_cart_count() {
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}

// --- FUNGSI TAMBAH KE KERANJANG ---
function add_to_cart() {
    global $conn;
    $product_id = $_POST['product_id'] ?? 0;

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak valid.']);
        return;
    }

    // Cek apakah produk sudah ada di keranjang
    if (isset($_SESSION['cart'][$product_id])) {
        // Jika sudah ada, tambahkan kuantitasnya
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        // Jika belum ada, ambil data produk dari DB
        $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($product = $result->fetch_assoc()) {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            ];
        } else {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
            return;
        }
    }

    echo json_encode(['success' => true, 'cart_count' => get_cart_count()]);
}

// --- FUNGSI UPDATE/HAPUS ITEM ---
function update_cart_item() {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = (int)($_POST['quantity'] ?? 0);

    if ($product_id <= 0 || !isset($_SESSION['cart'][$product_id])) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ada di keranjang.']);
        return;
    }

    if ($quantity <= 0) {
        // Hapus item jika kuantitas 0 atau kurang
        unset($_SESSION['cart'][$product_id]);
    } else {
        // Update kuantitas
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }

    echo json_encode(['success' => true, 'cart_count' => get_cart_count()]);
}

// --- FUNGSI MENDAPATKAN ISI KERANJANG (HTML) ---
function get_cart_contents() {
    $html = '';
    $total_price = 0;

    if (empty($_SESSION['cart'])) {
        $html = '<p class="text-center">Keranjang Anda kosong.</p>';
    } else {
        foreach ($_SESSION['cart'] as $id => $item) {
            $item_total = $item['price'] * $item['quantity'];
            $total_price += $item_total;

            $html .= '
            <div class="row mb-3 align-items-center">
                <div class="col-md-6">
                    <strong>' . htmlspecialchars($item['name']) . '</strong><br>
                    <small>Rp ' . number_format($item['price'], 0, ',', '.') . '</small>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control qty-input text-center" value="' . $item['quantity'] . '" min="1" data-id="' . $id . '">
                        <button class="btn btn-outline-secondary update-qty-btn" type="button" data-id="' . $id . '">Update</button>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-sm btn-outline-danger remove-item-btn" data-id="' . $id . '">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>';
        }
    }

    echo json_encode([
        'success' => true,
        'html' => $html,
        'total_formatted' => 'Rp ' . number_format($total_price, 0, ',', '.'),
        'total_raw' => $total_price,
        'cart_count' => get_cart_count()
    ]);
}
?>