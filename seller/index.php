<?php
// Pastikan path relatif benar
$basePath = dirname(__DIR__); // Mendapatkan path absolut dari direktori parent

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';
require_once $basePath . '/functions/order_functions.php';

// Ambil semua menu makanan milik penjual
function getAllOrders($conn) {
    $query = "SELECT o.*, f.name AS food_name, f.price, c.name AS category_name 
              FROM orders o 
              JOIN food_items f ON o.food_id = f.id 
              JOIN categories c ON f.category_id = c.id 
              ORDER BY o.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Untuk MVP, kita asumsikan semua menu adalah milik penjual yang sedang login
$foodItems = getAllFoodItems($conn);

// Ambil semua pesanan
$orders = getAllOrders($conn);

// Header
include $basePath . '/includes/seller_header.html';
?>

<!-- Konten Utama -->
<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-8">
    <div>
      <h1 class="text-3xl font-bold">Dashboard Penjual</h1>
      <p class="text-gray-600">Kelola menu dan pesanan Anda</p>
    </div>
    <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md flex items-center transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      Tambah Menu Baru
    </a>
  </div>

  <!-- Tab Navigation -->
  <div class="mb-8">
    <div class="border-b">
      <ul class="flex flex-wrap -mb-px">
        <li class="mr-2">
          <a href="#" class="inline-block p-4 border-b-2 border-blue-600 text-blue-600 font-medium tab-link active" data-tab="menu">
            Menu Makanan
          </a>
        </li>
        <li class="mr-2">
          <a href="#" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 font-medium tab-link" data-tab="orders">
            Pesanan Masuk
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Tab Content -->
  <div id="menu-tab" class="tab-content">
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded-lg">
        <thead>
          <tr class="bg-gray-100 text-gray-600 uppercase text-sm">
            <th class="py-3 px-4 text-left">Menu</th>
            <th class="py-3 px-4 text-left">Kategori</th>
            <th class="py-3 px-4 text-left">Harga</th>
            <th class="py-3 px-4 text-left">Stok</th>
            <th class="py-3 px-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($foodItems as $item): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-md overflow-hidden">
                    <img src="<?= htmlspecialchars($item['image'] ? '../' . $item['image'] : '../assets/images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover">
                  </div>
                  <span class="font-medium"><?= htmlspecialchars($item['name']) ?></span>
                </div>
              </td>
              <td class="py-3 px-4">
                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full"><?= htmlspecialchars($item['category_name']) ?></span>
              </td>
              <td class="py-3 px-4">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
              <td class="py-3 px-4"><?= $item['stock'] ?></td>
              <td class="py-3 px-4 text-right">
                <div class="flex justify-end gap-2">
                  <a href="edit.php?id=<?= $item['id'] ?>" class="text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </a>
                  <button class="text-red-600 hover:text-red-900 delete-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name']) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="orders-tab" class="tab-content hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded-lg">
        <thead>
          <tr class="bg-gray-100 text-gray-600 uppercase text-sm">
            <th class="py-3 px-4 text-left">ID Pesanan</th>
            <th class="py-3 px-4 text-left">Nama Pemesan</th>
            <th class="py-3 px-4 text-left">Menu</th>
            <th class="py-3 px-4 text-left">Jumlah</th>
            <th class="py-3 px-4 text-left">Waktu Ambil</th>
            <th class="py-3 px-4 text-left">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="py-3 px-4 font-medium">#<?= $order['id'] ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($order['customer_name']) ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($order['food_name']) ?></td>
              <td class="py-3 px-4"><?= $order['quantity'] ?></td>
              <td class="py-3 px-4"><?= $order['pickup_time'] ?> WIB</td>
              <td class="py-3 px-4">
                <span class="<?= getStatusBadgeClass($order['status']) ?>">
                  <?= getStatusLabel($order['status']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg p-6 max-w-md w-full">
    <h3 class="text-lg font-bold mb-4">Hapus Menu</h3>
    <p id="delete-message" class="mb-6">Apakah Anda yakin ingin menghapus menu ini?</p>
    <div class="flex justify-end gap-4">
      <button id="cancel-delete" class="px-4 py-2 border rounded-md hover:bg-gray-100 transition-colors">
        Batal
      </button>
      <form id="delete-form" method="POST" action="hapus.php">
        <input type="hidden" id="delete-id" name="id" value="">
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
          Hapus
        </button>
      </form>
    </div>
  </div>
</div>

<!-- JavaScript untuk tab dan modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Tab functionality
  const tabLinks = document.querySelectorAll('.tab-link');
  const tabContents = document.querySelectorAll('.tab-content');

  tabLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Remove active class from all tabs
      tabLinks.forEach(tab => {
        tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
        tab.classList.add('border-transparent');
      });
      
      // Add active class to clicked tab
      this.classList.add('active', 'border-blue-600', 'text-blue-600');
      this.classList.remove('border-transparent');
      
      // Hide all tab contents
      tabContents.forEach(content => {
        content.classList.add('hidden');
      });
      
      // Show the selected tab content
      const tabId = this.getAttribute('data-tab');
      document.getElementById(tabId + '-tab').classList.remove('hidden');
    });
  });

  // Delete modal functionality
  const deleteButtons = document.querySelectorAll('.delete-btn');
  const deleteModal = document.getElementById('delete-modal');
  const deleteForm = document.getElementById('delete-form');
  const deleteId = document.getElementById('delete-id');
  const deleteMessage = document.getElementById('delete-message');
  const cancelDelete = document.getElementById('cancel-delete');

  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      const name = this.getAttribute('data-name');
      
      deleteId.value = id;
      deleteMessage.textContent = `Apakah Anda yakin ingin menghapus menu "${name}"? Tindakan ini tidak dapat dibatalkan.`;
      deleteModal.classList.remove('hidden');
    });
  });

  cancelDelete.addEventListener('click', function() {
    deleteModal.classList.add('hidden');
  });

  // Close modal when clicking outside
  deleteModal.addEventListener('click', function(e) {
    if (e.target === deleteModal) {
      deleteModal.classList.add('hidden');
    }
  });
});
</script>

<?php
// Footer
include $basePath . '/includes/footer.html';

// Helper functions for order status
function getStatusLabel($status) {
  switch ($status) {
    case 'pending':
      return 'Menunggu';
    case 'processing':
      return 'Diproses';
    case 'completed':
      return 'Selesai';
    default:
      return 'Unknown';
  }
}

function getStatusBadgeClass($status) {
  switch ($status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full';
    case 'processing':
      return 'bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full';
    case 'completed':
      return 'bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full';
    default:
      return 'bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full';
  }
}
?>
