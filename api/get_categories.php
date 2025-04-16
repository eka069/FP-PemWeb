<?php
// Koneksi ke database
require_once '../config/database.php';
require_once '../functions/menu_functions.php';

// Set header untuk JSON
header('Content-Type: application/json');

// Ambil semua kategori
$categories = getAllCategories($conn);

// Kembalikan sebagai JSON
echo json_encode($categories);
?>
