<?php
// Start session
session_start();

// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';
require_once $basePath . '/functions/image_functions.php';
require_once $basePath . '/functions/cart_functions.php';

// Ambil ID makanan dari parameter URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Debug
echo "<!-- Debug: ID yang diterima = $id -->";

// Ambil detail makanan berdasarkan ID
$foodItem = getFoodItemById($conn, $id);

// Debug
if (!$foodItem) {
   echo "<!-- Debug: Menu tidak ditemukan untuk ID = $id -->";
}

// Jika makanan tidak ditemukan, redirect ke halaman utama
if (!$foodItem) {
   header('Location: index.php');
   exit;
}

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container mx-auto px-4 py-8">
 <a href="index.php" class="flex items-center text-gray-600 mb-6 hover:text-gray-900 transition-colors">
   <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
     <path stroke-linecap="round" stroke  fill="none" viewBox="0 0 24 24" stroke="currentColor">
     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
   </svg>
   Kembali ke Daftar Menu
 </a>

 <div class="grid md:grid-cols-2 gap-8">
   <div class="relative aspect-square overflow-hidden rounded-lg">
     <img src="<?= htmlspecialchars($foodItem['image'] ? $foodItem['image'] : getPlaceholderUrl(600, 600)) ?>" alt="<?= htmlspecialchars($foodItem['name']) ?>" class="w-full h-full object-cover">
     <span class="absolute top-4 right-4 bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full"><?= htmlspecialchars($foodItem['category_name']) ?></span>
   </div>

   <div>
     <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($foodItem['name']) ?></h1>
     <p class="text-gray-600 mb-4">Penjual: <?= htmlspecialchars($foodItem['seller_name']) ?></p>

     <div class="flex items-center gap-4 mb-6">
       <span class="text-2xl font-bold">Rp <?= number_format($foodItem['price'], 0, ',', '.') ?></span>
       <span class="<?= $foodItem['stock'] > 0 ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800' ?> px-3 py-1 rounded-full text-sm">
         <?= $foodItem['stock'] > 0 ? "Stok: {$foodItem['stock']}" : "Habis" ?>
       </span>
     </div>

     <hr class="my-6">

     <div class="mb-6">
       <h2 class="font-semibold text-lg mb-2">Deskripsi</h2>
       <p class="text-gray-600"><?= nl2br(htmlspecialchars($foodItem['description'])) ?></p>
     </div>

     <div class="flex gap-3">
       <?php if ($foodItem['stock'] > 0): ?>
         <a href="pesan.php?id=<?= $foodItem['id'] ?>" class="flex-1 block bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-md transition-colors">
           Pesan Sekarang
         </a>
         <button id="add-to-cart" class="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md transition-colors flex items-center justify-center">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
           </svg>
           Tambah ke Keranjang
         </button>
       <?php else: ?>
         <button disabled class="flex-1 block bg-gray-400 text-white text-center py-3 px-4 rounded-md cursor-not-allowed">
           Stok Habis
         </button>
       <?php endif; ?>
     </div>
   </div>
 </div>
</div>

<!-- JavaScript untuk tambah ke keranjang -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const addToCartButton = document.getElementById('add-to-cart');
  
  if (addToCartButton) {
    addToCartButton.addEventListener('click', function() {
      // Tambahkan ke keranjang
      fetch('api/cart_actions.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&item_id=<?= $foodItem['id'] ?>&quantity=1'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update tampilan keranjang
          const headerCartCount = document.getElementById('header-cart-count');
          if (headerCartCount) {
            headerCartCount.textContent = data.cart_count;
            headerCartCount.style.display = data.cart_count > 0 ? 'flex' : 'none';
          }
          
          // Tampilkan pesan sukses
          alert('Menu berhasil ditambahkan ke keranjang!');
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan ke keranjang.');
      });
    });
  }
});
</script>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
