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

// Proses form login
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        // Login user
        $result = loginUser($conn, $email, $password);

        if ($result && isset($result['success']) && $result['success']) {
            $_SESSION['user'] = $result['user'];
            header('Location: profil.php');
            exit;
        } else {
            $error = $result['message'] ?? 'Login gagal. Coba lagi.';
        }
    }
}

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container max-w-md mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">Masuk ke Akun</h1>
        <p class="text-gray-600 mt-2">Masukkan email dan password Anda untuk melanjutkan</p>
    </div>
    
    <div class="bg-white rounded-lg border p-6">
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="email" class="block font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                       class="w-full p-2 border rounded-md" required>
            </div>
            
            <div>
                <label for="password" class="block font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full p-2 border rounded-md" required>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                </div>
                
                <a href="#" class="text-sm text-blue-600 hover:underline">Lupa password?</a>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
                    Masuk
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Belum punya akun? <a href="daftar.php" class="text-blue-600 hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
