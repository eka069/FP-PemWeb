<?php
// Koneksi ke database
require_once '../config/database.php';
require_once '../functions/menu_functions.php';

// Cek apakah ada ID yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    // Hapus menu
    $success = deleteFoodItem($conn, $id);
    
    // Redirect ke halaman daftar menu
    header('Location: index.php?deleted=' . ($success ? 'true' : 'false'));
    exit;
} else {
    // Jika tidak ada ID, redirect ke halaman daftar menu
    header('Location: index.php');
    exit;
}
?>
