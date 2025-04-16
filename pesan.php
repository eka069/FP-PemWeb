<?php
// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';
require_once $basePath . '/functions/order_functions.php';

// Ambil ID makanan dari parameter URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil detail makanan berdasarkan ID
$foodItem = getFoodItemById($conn, $id);

// Jika makanan tidak ditemukan atau stok habis, redirect ke halaman utama
if (!$foodItem || $foodItem['stock'] <= 0) {
    header('Location: index.php');
    exit;
}

// Proses form pemesanan jika disubmit
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $customerName = trim($_POST['customer_name'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 1);
    $pickupTime = trim($_POST['pickup_time'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Validasi nama
    if (empty($customerName)) {
        $errors[] = 'Nama pemesan harus diisi';
    }

    // Validasi jumlah
    if ($quantity < 1) {
        $errors[] = 'Jumlah pesanan minimal 1';
    } elseif ($quantity > $foodItem['stock']) {
        $errors[] = 'Jumlah pesanan melebihi stok yang tersedia';
    }

    // Validasi waktu pengambilan
    if (empty($pickupTime)) {
        $errors[] = 'Waktu pengambilan harus dipilih';
    }

    // Jika tidak ada error, simpan pesanan
    if (empty($errors)) {
        $orderId = createOrder($conn, [
            'food_id' => $foodItem['id'],
            'customer_name' => $customerName,
            'quantity' => $quantity,
            'pickup_time' => $pickupTime,
            'notes' => $notes,
            'status' => 'pending'
        ]);

        if ($orderId) {
            // Update stok makanan
            updateFoodStock($conn, $foodItem['id'], $foodItem['stock'] - $quantity);
            $success = true;
        } else {
            $errors[] = 'Gagal menyimpan pesanan. Silakan coba lagi.';
        }
    }
}

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container max-w-2xl mx-auto px-4 py-8">
  <a href="detail.php?id=<?= $foodItem['id'] ?>" class="flex items-center text-gray-600 mb-6 hover:text-gray-900 transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Kembali ke Detail Menu
  </a>

  <div class="bg-white rounded-lg border p-6">
    <h1 class="text-2xl font-bold mb-2">Form Pemesanan</h1>
    <p class="text-gray-600 mb-6">Silakan isi data pemesanan Anda</p>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <p>Pesanan berhasil dibuat! Silakan ambil pesanan Anda pada waktu yang telah ditentukan.</p>
        <p class="mt-2">
          <a href="index.php" class="text-green-800 underline">Kembali ke halaman utama</a>
        </p>
      </div>
    <?php else: ?>
      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="mb-6">
        <h2 class="font-medium mb-2">Detail Pesanan:</h2>
        <div class="bg-gray-100 p-4 rounded-md">
          <p class="font-semibold"><?= htmlspecialchars($foodItem['name']) ?></p>
          <p class="text-gray-600 text-sm">Penjual: <?= htmlspecialchars($foodItem['seller_name']) ?></p>
          <p class="font-medium mt-1">Rp <?= number_format($foodItem['price'], 0, ',', '.') ?></p>
        </div>
      </div>

      <hr class="my-6">

      <form method="POST" action="" class="space-y-6" id="order-form">
        <div class="space-y-2">
          <label for="customer_name" class="block font-medium">Nama Pemesan</label>
          <input type="text" id="customer_name" name="customer_name" placeholder="Masukkan nama lengkap" 
                 class="w-full p-2 border rounded-md" required
                 value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>">
        </div>

        <div class="space-y-2">
          <label for="quantity" class="block font-medium">Jumlah</label>
          <input type="number" id="quantity" name="quantity" min="1" max="<?= $foodItem['stock'] ?>" 
                 class="w-full p-2 border rounded-md" required
                 value="<?= htmlspecialchars($_POST['quantity'] ?? '1') ?>">
          <p class="text-xs text-gray-500">Maksimal: <?= $foodItem['stock'] ?></p>
        </div>

        <div class="space-y-2">
          <label for="pickup_time" class="block font-medium">Waktu Pengambilan</label>
          <select id="pickup_time" name="pickup_time" class="w-full p-2 border rounded-md" required>
            <option value="">Pilih waktu pengambilan</option>
            <?php
            // Generate time slots (every 30 minutes from 8 AM to 5 PM)
            for ($hour = 8; $hour <= 17; $hour++) {
                foreach ([0, 30] as $minute) {
                    if ($hour === 17 && $minute === 30) continue; // Skip 17:30
                    $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                    $formattedMinute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                    $timeSlot = "$formattedHour:$formattedMinute";
                    $selected = ($_POST['pickup_time'] ?? '') === $timeSlot ? 'selected' : '';
                    echo "<option value=\"$timeSlot\" $selected>$timeSlot WIB</option>";
                }
            }
            ?>
          </select>
        </div>

        <div class="space-y-2">
          <label for="notes" class="block font-medium">Catatan (Opsional)</label>
          <input type="text" id="notes" name="notes" placeholder="Tambahkan catatan untuk pesanan Anda" 
                 class="w-full p-2 border rounded-md"
                 value="<?= htmlspecialchars($_POST['notes'] ?? '') ?>">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
          Konfirmasi Pesanan
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<!-- JavaScript untuk validasi form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('order-form');
  if (form) {
    form.addEventListener('submit', function(event) {
      const customerName = document.getElementById('customer_name').value.trim();
      const quantity = parseInt(document.getElementById('quantity').value);
      const pickupTime = document.getElementById('pickup_time').value;
      const maxStock = <?= $foodItem['stock'] ?>;
      
      let isValid = true;
      let errorMessage = '';
      
      if (!customerName) {
        errorMessage += 'Nama pemesan harus diisi\n';
        isValid = false;
      }
      
      if (isNaN(quantity) || quantity < 1) {
        errorMessage += 'Jumlah pesanan minimal 1\n';
        isValid = false;
      } else if (quantity > maxStock) {
        errorMessage += 'Jumlah pesanan melebihi stok yang tersedia\n';
        isValid = false;
      }
      
      if (!pickupTime) {
        errorMessage += 'Waktu pengambilan harus dipilih\n';
        isValid = false;
      }
      
      if (!isValid) {
        alert(errorMessage);
        event.preventDefault();
      }
    });
  }
});
</script>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
