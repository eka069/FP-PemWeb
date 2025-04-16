<?php
// Koneksi ke database
require_once '../config/database.php';
require_once '../functions/menu_functions.php';

// Set header untuk JSON
header('Content-Type: application/json');

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Ambil semua menu makanan
    $foodItems = getAllFoodItems($conn);
    
    // Tambahkan debugging
    error_log("Jumlah menu yang ditemukan: " . count($foodItems));
    
    // Kembalikan sebagai JSON
    echo json_encode($foodItems);
} catch (Exception $e) {
    // Log error
    error_log("Error di get_food_items.php: " . $e->getMessage());
    
    // Kembalikan error sebagai JSON
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
