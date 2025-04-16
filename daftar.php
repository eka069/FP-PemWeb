<?php
// Start session
session_start();

// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/user_functions.php';

// Cek apakah user sudah login
if (isLoggedIn()) {
    // Redirect ke halaman profil
    header('Location: profil.php');
    exit;
}

// Proses form registrasi
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Nama, email, dan password harus diisi';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi password tidak sesuai';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        // Register user
        $result = registerUser($conn, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password
        ]);
        
        if ($result['success']) {
            // Redirect ke halaman profil
            header('Location: profil.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container max-w-md mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Daftar Akun Baru</h1>
        <p class="text-gray-600 mt-2">Isi form di bawah untuk membuat akun baru</p>
    </div>
    
    <div class="bg-white rounded-lg border p-6">
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="name" class="block font-medium mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                       class="w-full p-2 border rounded-md" required>
            </div>
            
            <div>
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                       class="w-full p-2 border rounded-md" required>
            </div>
            
            <div>
                <label for="phone" class="block font-medium mb-1">Nomor Telepon (Opsional)</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" 
                       class="w-full p-2 border rounded-md">
            </div>
            
            <div>
                <label for="password" class="block font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full p-2 border rounded-md" required>
                <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>
            
            <div>
                <label for="password_confirm" class="block font-medium mb-1">Konfirmasi Password</label>
                <input type="password" id="password_confirm" name="password_confirm" 
                       class="w-full p-2 border rounded-md" required>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
                    Daftar
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</div>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
