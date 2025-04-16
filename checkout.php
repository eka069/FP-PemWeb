<?php
// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';
require_once $basePath . '/functions/cart_functions.php';
require_once $basePath . '/functions/order_functions.php';

// Ambil item keranjang
$cartItems = getCartItems();
$cartTotal = getCartTotal();

// Jika keranjang kosong, redirect ke halaman utama
if (empty($cartItems)) {
    header('Location: index.php');
    exit;
}

// Proses checkout jika form disubmit
$errors = [];
$success = false;
$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $customerName = trim($_POST['customer_name'] ?? '');
    $pickupTime = trim($_POST['pickup_time'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Validasi nama
    if (empty($customerName)) {
        $errors[] = 'Nama pemesan harus diisi';
    }

    // Validasi waktu pengambilan
    if (empty($pickupTime)) {
        $errors[] = 'Waktu pengambilan harus dipilih';
    }

    // Jika tidak ada error, proses pesanan
    if (empty($errors)) {
        // Mulai transaksi database
        mysqli_begin_transaction($conn);
        
        try {
            $orderIds = [];
            
            // Buat pesanan untuk setiap item di keranjang
            foreach ($cartItems as $item) {
                // Cek stok terbaru
                $foodItem = getFoodItemById($conn, $item['id']);
                
                if (!$foodItem || $foodItem['stock'] < $item['quantity']) {
                    throw new Exception('Stok ' . ($foodItem ? $foodItem['name'] : 'menu') . ' tidak mencukupi');
                }
                
                // Buat pesanan
                $orderId = createOrder($conn, [
                    'food_id' => $item['id'],
                    'customer_name' => $customerName,
                    'quantity' => $item['quantity'],
                    'pickup_time' => $pickupTime,
                    'notes' => $notes,
                    'status' => 'pending'
                ]);
                
                if (!$orderId) {
                    throw new Exception('Gagal membuat pesanan');
                }
                
                $orderIds[] = $orderId;
                
                // Update stok
                $newStock = $foodItem['stock'] - $item['quantity'];
                if (!updateFoodStock($conn, $item['id'], $newStock)) {
                    throw new Exception('Gagal memperbarui stok');
                }
            }
            
            // Commit transaksi
            mysqli_commit($conn);
            
            // Kosongkan keranjang
            clearCart();
            
            // Set success
            $success = true;
            $orderId = implode(', ', $orderIds);
            
        } catch (Exception $e) {
            // Rollback transaksi jika ada error
            mysqli_rollback($conn);
            $errors[] = $e->getMessage();
        }
    }
}

// Header
include $basePath . '/includes/header.html';
?>

<!-- Konten Utama -->
<div class="container max-w-2xl mx-auto px-4 py-8">
    <a href="index.php" class="flex items-center text-gray-600 mb-6 hover:text-gray-900 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Daftar Menu
    </a>

    <div class="bg-white rounded-lg border p-6">
        <h1 class="text-2xl font-bold mb-2">Checkout</h1>
        <p class="text-gray-600 mb-6">Selesaikan pesanan Anda</p>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <p>Pesanan berhasil dibuat! Nomor pesanan Anda: <strong>#<?= $orderId ?></strong></p>
                <p class="mt-2">Silakan ambil pesanan Anda pada waktu yang telah ditentukan.</p>
                <p class="mt-4">
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
                <h2 class="font-medium mb-2">Ringkasan Pesanan:</h2>
                <div class="bg-gray-50 p-4 rounded-md">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="flex justify-between items-center py-2 border-b last:border-0">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-sm text-gray-500"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                            </div>
                            <p class="font-medium">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="flex justify-between items-center py-2 mt-2 border-t font-bold">
                        <p>Total</p>
                        <p>Rp <?= number_format($cartTotal, 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>

            <hr class="my-6">

            <form method="POST" action="" class="space-y-6" id="checkout-form">
                <div class="space-y-2">
                    <label for="customer_name" class="block font-medium">Nama Pemesan</label>
                    <input type="text" id="customer_name" name="customer_name" placeholder="Masukkan nama lengkap" 
                            class="w-full p-2 border rounded-md" required
                            value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>">
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
                    <textarea id="notes" name="notes" placeholder="Tambahkan catatan untuk pesanan Anda" 
                            class="w-full p-2 border rounded-md" rows="3"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-md transition-colors">
                    Konfirmasi Pesanan
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript untuk validasi form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', function(event) {
            const customerName = document.getElementById('customer_name').value.trim();
            const pickupTime = document.getElementById('pickup_time').value;
            
            let isValid = true;
            let errorMessage = '';
            
            if (!customerName) {
                errorMessage += 'Nama pemesan harus diisi\n';
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
