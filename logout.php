<?php
// Start session
session_start();

// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/user_functions.php';

// Logout user
logoutUser();

// Redirect ke halaman login
header('Location: login.php');
exit;
?>
