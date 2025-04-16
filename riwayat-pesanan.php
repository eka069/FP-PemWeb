<?php
// Start session
session_start();

// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/user_functions.php';
require_once $basePath . '/functions/cart_functions.php';
require_once $basePath . '/functions/order_functions.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    // Redirect ke halaman login
    header('Location: login.php');
    exit;
}

// Ambil data user
$user = getCurrentUser();

// Ambil riwayat pesanan user
$orders = getUserOrders($conn, $user['id']);

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Riwayat Pesanan</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg border p-4">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <div class="ml-4">
                        <h2 class="font-semibold"><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <nav>
                    <ul class="space-y-2">
                        <li>
                            <a href="profil.php" class="flex items-center py-2 px-3 rounded-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil Saya
                            </a>
                        </li>
                        <li>
                            <a href="riwayat-pesanan.php" class="flex items-center py-2 px-3 rounded-md bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Riwayat Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="pengaturan.php" class="flex items-center py-2 px-3 rounded-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Pengaturan
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" class="flex items-center py-2 px-3 rounded-md hover:bg-gray-100 text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- Konten Riwayat Pesanan -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg border overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="font-semibold">Daftar Pesanan</h2>
                </div>
                
                <?php if (empty($orders)): ?>
                    <div class="p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-600 mb-4">Anda belum memiliki riwayat pesanan</p>
                        <a href="index.html" class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
                            Lihat Menu
                        </a>
                    </div>
                <?php else: ?>
                    <div class="divide-y">
                        <?php foreach ($orders as $order): ?>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-medium">Pesanan #<?= $order['id'] ?></h3>
                                        <p class="text-sm text-gray-600"><?= date('d F Y, H:i', strtotime($order['created_at'])) ?></p>
                                    </div>
                                    <span class="<?= getStatusBadgeClass($order['status']) ?>">
                                        <?= getStatusLabel($order['status']) ?>
                                    </span>
                                </div>
                                
                                <div class="flex items-center mt-3">
                                    <div class="h-16 w-16 rounded-md overflow-hidden flex-shrink-0">
                                        <img src="<?= htmlspecialchars($order['image'] ? $order['image'] : getPlaceholderUrl(100, 100)) ?>" alt="<?= htmlspecialchars($order['food_name']) ?>" class="w-full h-full object-cover">
                                    </div>
                                    
                                    <div class="ml-4 flex-grow">
                                        <h4 class="font-medium"><?= htmlspecialchars($order['food_name']) ?></h4>
                                        <p class="text-sm text-gray-600">Jumlah: <?= $order['quantity'] ?> × Rp <?= number_format($order['price'], 0, ',', '.') ?></p>
                                        <p class="text-sm font-medium mt-1">Total: Rp <?= number_format($order['quantity'] * $order['price'], 0, ',', '.') ?></p>
                                    </div>
                                </div>
                                
                                <div class="mt-3 pt-3 border-t">
                                    <p class="text-sm">
                                        <span class="font-medium">Waktu Pengambilan:</span> <?= $order['pickup_time'] ?> WIB
                                    </p>
                                    <?php if (!empty($order['notes'])): ?>
                                        <p class="text-sm mt-1">
                                            <span class="font-medium">Catatan:</span> <?= htmlspecialchars($order['notes']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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
