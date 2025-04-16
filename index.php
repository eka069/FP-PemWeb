<?php
// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';
require_once $basePath . '/functions/image_functions.php'; // Tambahkan ini

// Ambil semua menu makanan
$foodItems = getAllFoodItems($conn);

// Ambil kategori untuk filter
$categories = getAllCategories($conn);

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container mx-auto px-4 py-8">
  <header class="mb-8">
    <h1 class="text-3xl font-bold text-center mb-2">FAST KANTIN</h1>
    <p class="text-center text-gray-600">Pesan makanan kantin dengan cepat dan mudah</p>
  </header>

  <!-- Filter dan Pencarian -->
  <div class="flex flex-col md:flex-row gap-4 mb-8">
    <div class="relative flex-1">
      <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <input type="text" id="search-input" placeholder="Cari makanan..." class="pl-10 w-full p-2 border rounded-md">
    </div>

    <div class="flex gap-2">
      <select id="category-filter" class="p-2 border rounded-md w-[180px]">
        <option value="all">Semua Kategori</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= htmlspecialchars($category['slug']) ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <button id="filter-button" class="p-2 border rounded-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
      </button>
    </div>
  </div>

  <!-- Daftar Menu Makanan -->
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="food-items-container">
    <?php foreach ($foodItems as $item): ?>
      <div class="food-item border rounded-lg overflow-hidden" data-category="<?= htmlspecialchars($item['category_slug']) ?>">
        <div class="relative h-48 w-full">
          <img src="<?= htmlspecialchars($item['image'] ? $item['image'] : getPlaceholderUrl(300, 200)) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover">
          <span class="absolute top-2 right-2 bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full"><?= htmlspecialchars($item['category_name']) ?>
        </span>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-lg mb-1"><?= htmlspecialchars($item['name']) ?></h3>
          <p class="text-gray-600 text-sm mb-2">Penjual: <?= htmlspecialchars($item['seller_name']) ?></p>
          <p class="font-bold text-lg">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
          <p class="text-sm text-gray-500 mt-1">Stok: <?= htmlspecialchars($item['stock']) ?></p>
        </div>
        <div class="p-4 pt-0">
          <a href="detail.php?id=<?= $item['id'] ?>" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md transition-colors">Detail</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- JavaScript untuk filter dan pencarian -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search-input');
  const categoryFilter = document.getElementById('category-filter');
  const foodItems = document.querySelectorAll('.food-item');

  function filterItems() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedCategory = categoryFilter.value;

    foodItems.forEach(item => {
      const name = item.querySelector('h3').textContent.toLowerCase();
      const category = item.dataset.category;
      const matchesSearch = name.includes(searchTerm);
      const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

      if (matchesSearch && matchesCategory) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  }

  searchInput.addEventListener('input', filterItems);
  categoryFilter.addEventListener('change', filterItems);
  document.getElementById('filter-button').addEventListener('click', filterItems);
});
</script>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
