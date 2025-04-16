<?php
// Start session
session_start();

// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/user_functions.php';
require_once $basePath . '/functions/cart_functions.php';

// Cek apakah user sudah login
if (!isLoggedIn()) {
    // Redirect ke halaman login
    header('Location: login.php');
    exit;
}

// Ambil data user
$user = getCurrentUser();

// Proses form ganti password
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password baru tidak sesuai';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter';
    } else {
        // Ganti password
        $result = changeUserPassword($conn, $user['id'], $current_password, $new_password);
        
        if ($result['success']) {
            $success = true;
        } else {
            $error = $result['message'];
        }
    }
}

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Pengaturan</h1>
    
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
                            <a href="riwayat-pesanan.php" class="flex items-center py-2 px-3 rounded-md hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Riwayat Pesanan
                            </a>
                        </li>
                        <li>
                            <a href="pengaturan.php" class="flex items-center py-2 px-3 rounded-md bg-blue-50 text-blue-600">
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
        
        <!-- Konten Pengaturan -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg border p-6">
                <h2 class="text-xl font-semibold mb-4">Ganti Password</h2>
                
                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <p>Password berhasil diubah!</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <p><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-4">
                    <div>
                        <label for="current_password" class="block font-medium mb-1">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="w-full p-2 border rounded-md" required>
                    </div>
                    
                    <div>
                        <label for="new_password" class="block font-medium mb-1">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="w-full p-2 border rounded-md" required>
                        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block font-medium mb-1">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="w-full p-2 border rounded-md" required>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white rounded-lg border p-6 mt-6">
                <h2 class="text-xl font-semibold mb-4">Notifikasi</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium">Email Notifikasi</h3>
                            <p class="text-sm text-gray-600">Terima notifikasi tentang pesanan dan promo melalui email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium">Notifikasi Promo</h3>
                            <p class="text-sm text-gray-600">Terima informasi tentang promo dan diskon terbaru</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
