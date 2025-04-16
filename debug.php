<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debugging Fast Kantin</h1>";

// Cek koneksi database
echo "<h2>Cek Koneksi Database</h2>";
require_once 'config/database.php';

if ($conn) {
   echo "<p style='color:green'>Koneksi database berhasil!</p>";
   
   // Cek tabel food_items
   $query = "SELECT COUNT(*) as total FROM food_items";
   $result = mysqli_query($conn, $query);
   
   if ($result) {
       $row = mysqli_fetch_assoc($result);
       echo "<p>Jumlah menu dalam database: " . $row['total'] . "</p>";
       
       // Tampilkan beberapa item menu untuk debugging
       $query = "SELECT id, name FROM food_items LIMIT 5";
       $result = mysqli_query($conn, $query);
       
       if ($result && mysqli_num_rows($result) > 0) {
           echo "<ul>";
           while ($row = mysqli_fetch_assoc($result)) {
               echo "<li>ID: " . $row['id'] . " - " . $row['name'] . "</li>";
           }
           echo "</ul>";
       } else {
           echo "<p style='color:red'>Tidak ada menu atau error query: " . mysqli_error($conn) . "</p>";
       }
   } else {
       echo "<p style='color:red'>Error query: " . mysqli_error($conn) . "</p>";
   }
   
   // Cek tabel categories
   $query = "SELECT COUNT(*) as total FROM categories";
   $result = mysqli_query($conn, $query);
   
   if ($result) {
       $row = mysqli_fetch_assoc($result);
       echo "<p>Jumlah kategori dalam database: " . $row['total'] . "</p>";
   } else {
       echo "<p style='color:red'>Error query: " . mysqli_error($conn) . "</p>";
   }
   
   // Cek tabel sellers
   $query = "SELECT COUNT(*) as total FROM sellers";
   $result = mysqli_query($conn, $query);
   
   if ($result) {
       $row = mysqli_fetch_assoc($result);
       echo "<p>Jumlah penjual dalam database: " . $row['total'] . "</p>";
   } else {
       echo "<p style='color:red'>Error query: " . mysqli_error($conn) . "</p>";
   }
} else {
   echo "<p style='color:red'>Koneksi database gagal: " . mysqli_connect_error() . "</p>";
}

// Cek fungsi-fungsi
echo "<h2>Cek Fungsi</h2>";
require_once 'functions/menu_functions.php';

echo "<h3>getAllFoodItems()</h3>";
$foodItems = getAllFoodItems($conn);
echo "<p>Jumlah menu yang dikembalikan: " . count($foodItems) . "</p>";
if (count($foodItems) > 0) {
   echo "<p>Menu pertama: " . $foodItems[0]['name'] . " (ID: " . $foodItems[0]['id'] . ")</p>";
}

echo "<h3>getFoodItemById()</h3>";
if (count($foodItems) > 0) {
   $id = $foodItems[0]['id'];
   $item = getFoodItemById($conn, $id);
   if ($item) {
       echo "<p style='color:green'>Menu dengan ID $id ditemukan: " . $item['name'] . "</p>";
   } else {
       echo "<p style='color:red'>Menu dengan ID $id tidak ditemukan!</p>";
   }
} else {
   echo "<p>Tidak ada menu untuk diuji</p>";
}

// Cek API endpoints
echo "<h2>Cek API Endpoints</h2>";

// Cek get_food_items.php
echo "<h3>get_food_items.php</h3>";
$api_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/api/get_food_items.php';
echo "<p>URL API: " . $api_url . "</p>";

// Cek file-file penting
echo "<h2>Cek File</h2>";
$files = [
   'index.php',
   'index.html',
   'detail.php',
   'config/database.php',
   'functions/menu_functions.php',
   'functions/image_functions.php',
   'includes/header.php',
   'includes/footer.php',
   'api/get_food_items.php',
   'api/get_categories.php'
];

foreach ($files as $file) {
   if (file_exists($file)) {
       echo "<p style='color:green'>File $file ada</p>";
   } else {
       echo "<p style='color:red'>File $file tidak ditemukan!</p>";
   }
}

// Tambahkan link untuk kembali ke beranda
echo "<p><a href='index.html' style='display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 5px;'>Kembali ke Beranda</a></p>";
?>
