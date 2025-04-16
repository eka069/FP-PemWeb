<?php
// Start session
session_start();

// Koneksi ke database
require_once '../config/database.php';
require_once '../functions/cart_functions.php';
require_once '../functions/menu_functions.php';

// Set header untuk JSON
header('Content-Type: application/json');

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Ambil action dari request
$action = isset($_POST['action']) ? $_POST['action'] : '';
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validasi item_id
if ($item_id <= 0 && $action !== 'get_cart' && $action !== 'clear_cart') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid item ID'
    ]);
    exit;
}

// Proses action
$response = [
    'success' => false,
    'message' => 'Unknown action',
    'cart_count' => 0
];

switch ($action) {
    case 'add':
        // Cek stok
        $item = getFoodItemById($conn, $item_id);
        if (!$item) {
            $response['message'] = 'Menu tidak ditemukan';
            break;
        }
        
        // Cek stok tersedia
        $current_quantity = isset($_SESSION['cart'][$item_id]) ? $_SESSION['cart'][$item_id] : 0;
        if ($current_quantity + $quantity > $item['stock']) {
            $response['message'] = 'Stok tidak mencukupi';
            break;
        }
        
        // Tambahkan ke keranjang
        $result = addToCart($item_id, $quantity);
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Menu berhasil ditambahkan ke keranjang';
        } else {
            $response['message'] = 'Gagal menambahkan menu ke keranjang';
        }
        break;
        
    case 'remove':
        $result = removeFromCart($item_id);
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Menu berhasil dihapus dari keranjang';
        } else {
            $response['message'] = 'Gagal menghapus menu dari keranjang';
        }
        break;
        
    case 'update':
        // Cek stok
        $item = getFoodItemById($conn, $item_id);
        if (!$item) {
            $response['message'] = 'Menu tidak ditemukan';
            break;
        }
        
        // Cek stok tersedia
        if ($quantity > $item['stock']) {
            $response['message'] = 'Stok tidak mencukupi';
            break;
        }
        
        $result = updateCartItemQuantity($item_id, $quantity);
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Jumlah menu berhasil diperbarui';
        } else {
            $response['message'] = 'Gagal memperbarui jumlah menu';
        }
        break;
        
    case 'get_cart':
        $cart_items = getCartItems($conn);
        $cart_total = getCartTotal($conn);
        
        $response['success'] = true;
        $response['message'] = 'Berhasil mengambil data keranjang';
        $response['cart_items'] = $cart_items;
        $response['cart_total'] = $cart_total;
        break;
        
    case 'clear_cart':
        $result = clearCart();
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Keranjang berhasil dikosongkan';
        } else {
            $response['message'] = 'Gagal mengosongkan keranjang';
        }
        break;
        
    default:
        $response['message'] = 'Action tidak valid';
        break;
}

// Tambahkan jumlah item di keranjang ke response
$response['cart_count'] = getCartCount();

// Kembalikan response
echo json_encode($response);
?>
