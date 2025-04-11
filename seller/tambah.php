<?php
// Koneksi ke database
require_once '../config/database.php';
require_once '../functions/menu_functions.php';

// Ambil semua kategori
$categories = getAllCategories($conn);

// Proses form jika disubmit
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    // Validasi nama
    if (empty($name)) {
        $errors[] = 'Nama menu harus diisi';
    }
    
    // Validasi kategori
    if ($category_id <= 0) {
        $errors[] = 'Kategori harus dipilih';
    }
    
    // Validasi harga
    if ($price <= 0) {
        $errors[] = 'Harga harus lebih dari 0';
    }
    
    // Validasi stok
    if ($stock < 0) {
        $errors[] = 'Stok tidak boleh negatif';
    }
    
    // Validasi deskripsi
    if (empty($description)) {
        $errors[] = 'Deskripsi harus diisi';
    }
    
    // Upload gambar jika ada
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.';
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = 'Ukuran gambar terlalu besar. Maksimal 2MB.';
        } else {
            $upload_dir = '../assets/images/menu/';
            
            // Buat direktori jika belum ada
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'assets/images/menu/' . $filename;
            } else {
                $errors[] = 'Gagal mengupload gambar. Silakan coba lagi.';
            }
        }
    }
    
    // Jika tidak ada error, simpan menu
    if (empty($errors)) {
        $menuId = createFoodItem($conn, [
            'name' => $name,
            'category_id' => $category_id,
            'price' => $price,
            'stock' => $stock,
            'description' => $description,
            'image' => $image_path,
            'seller_id' => 1 // Untuk MVP, kita asumsikan seller_id = 1
        ]);
        
        if ($menuId) {
            $success = true;
        } else {
            $errors[] = 'Gagal menyimpan menu. Silakan coba lagi.';
        }
    }
}

// Header
include '../includes/seller_header.php';
?>

<!-- Konten Utama -->
<div class="container max-w-2xl mx-auto px-4 py-8">
  <a href="index.php" class="flex items-center text-gray-600 mb-6 hover:text-gray-900 transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Kembali ke Dashboard
  </a>

  <div class="bg-white rounded-lg border p-6">
    <h1 class="text-2xl font-bold mb-2">Tambah Menu Baru</h1>
    <p class="text-gray-600 mb-6">Isi detail menu yang ingin ditambahkan</p>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <p>Menu berhasil ditambahkan!</p>
        <p class="mt-2">
          <a href="index.php" class="text-green-800 underline">Kembali ke daftar menu</a>
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

      <form method="POST" action="" enctype="multipart/form-data" class="space-y-6" id="menu-form">
        <div class="space-y-2">
          <label for="name" class="block font-medium">Nama Menu</label>
          <input type="text" id="name" name="name" placeholder="Masukkan nama menu" 
                 class="w-full p-2 border rounded-md" required
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="space-y-2">
          <label for="category_id" class="block font-medium">Kategori</label>
          <select id="category_id" name="category_id" class="w-full p-2 border rounded-md" required>
            <option value="">Pilih kategori</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>" <?= (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="space-y-2">
          <label for="price" class="block font-medium">Harga (Rp)</label>
          <input type="number" id="price" name="price" min="0" placeholder="Masukkan harga" 
                 class="w-full p-2 border rounded-md" required
                 value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
        </div>

        <div class="space-y-2">
          <label for="stock" class="block font-medium">Stok</label>
          <input type="number" id="stock" name="stock" min="0" placeholder="Masukkan jumlah stok" 
                 class="w-full p-2 border rounded-md" required
                 value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>">
        </div>

        <div class="space-y-2">
          <label for="description" class="block font-medium">Deskripsi</label>
          <textarea id="description" name="description" placeholder="Masukkan deskripsi menu" 
                    rows="4" class="w-full p-2 border rounded-md" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="space-y-2">
          <label for="image" class="block font-medium">Gambar</label>
          <input type="file" id="image" name="image" accept="image/*" class="w-full p-2 border rounded-md">
          <p class="text-xs text-gray-500 mt-1">Format yang didukung: JPG, PNG, GIF. Ukuran maksimal: 2MB</p>
        </div>

        <div class="flex gap-4">
          <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors">
            Simpan Menu
          </button>
          <a href="index.php" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-center transition-colors">
            Batal
          </a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<!-- JavaScript untuk validasi form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('menu-form');
  if (form) {
    form.addEventListener('submit', function(event) {
      const name = document.getElementById('name').value.trim();
      const category = document.getElementById('category_id').value;
      const price = parseInt(document.getElementById('price').value);
      const stock = parseInt(document.getElementById('stock').value);
      const description = document.getElementById('description').value.trim();
      
      let isValid = true;
      let errorMessage = '';
      
      if (!name) {
        errorMessage += 'Nama menu harus diisi\n';
        isValid = false;
      }
      
      if (!category) {
        errorMessage += 'Kategori harus dipilih\n';
        isValid = false;
      }
      
      if (isNaN(price) || price <= 0) {
        errorMessage += 'Harga harus lebih dari 0\n';
        isValid = false;
      }
      
      if (isNaN(stock) || stock < 0) {
        errorMessage += 'Stok tidak boleh negatif\n';
        isValid = false;
      }
      
      if (!description) {
        errorMessage += 'Deskripsi harus diisi\n';
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
include '../includes/footer.php';
?>
