<?php
// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';

// Ambil ID makanan dari parameter URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil detail makanan berdasarkan ID
$foodItem = getFoodItemById($conn, $id);

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
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Kembali ke Daftar Menu
  </a>

  <div class="grid md:grid-cols-2 gap-8">
    <div class="relative aspect-square overflow-hidden rounded-lg">
      <img src="<?= htmlspecialchars($foodItem['image'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($foodItem['name']) ?>" class="w-full h-full object-cover">
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

      <?php if ($foodItem['stock'] > 0): ?>
        <a href="pesan.php?id=<?= $foodItem['id'] ?>" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-md transition-colors">
          Pesan Sekarang
        </a>
      <?php else: ?>
        <button disabled class="block w-full bg-gray-400 text-white text-center py-3 px-4 rounded-md cursor-not-allowed">
          Stok Habis
        </button>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
// Footer
include $basePath . '/includes/footer.php';
?>
