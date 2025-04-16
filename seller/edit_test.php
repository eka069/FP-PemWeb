<?php
// File ini untuk menguji apakah server dapat menjalankan script PHP di folder seller

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Edit Page</h1>";
echo "<p>Jika Anda dapat melihat halaman ini, server dapat menjalankan script PHP di folder seller.</p>";

// Cek apakah parameter ID diterima
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
echo "<p>ID yang diterima: " . $id . "</p>";

// Cek path relatif
$basePath = dirname(__DIR__);
echo "<p>Base path: " . $basePath . "</p>";

// Cek file penting yang dibutuhkan edit.php
$files = [
    $basePath . '/config/database.php',
    $basePath . '/functions/menu_functions.php',
    $basePath . '/functions/image_functions.php',
    $basePath . '/includes/seller_header.php',
    $basePath . '/includes/footer.php'
];

echo "<h2>Cek File yang Dibutuhkan</h2>";
echo "<ul>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li style='color:green'>" . $file . " - Ada</li>";
    } else {
        echo "<li style='color:red'>" . $file . " - TIDAK DITEMUKAN!</li>";
    }
}
echo "</ul>";

// Link ke edit.php yang benar
echo "<p>Link ke edit.php: <a href='edit.php?id=" . $id . "'>edit.php?id=" . $id . "</a></p>";
echo "<p>Link kembali ke dashboard: <a href='index.php'>Kembali ke Dashboard</a></p>";
?>
