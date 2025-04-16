<?php
// Start session
session_start();

// Pastikan path relatif benar
$basePath = __DIR__; // Mendapatkan path absolut dari direktori saat ini

// Koneksi ke database
require_once $basePath . '/config/database.php';
require_once $basePath . '/functions/menu_functions.php';
require_once $basePath . '/functions/cart_functions.php';
require_once $basePath . '/functions/image_functions.php';

// Ambil item keranjang
$cart_items = getCartItems($conn);
$cart_total = getCartTotal($conn);

// Header
include $basePath . '/includes/header.php';
?>

<!-- Konten Utama -->
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Keranjang Belanja</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="bg-white rounded-lg border p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h2 class="text-xl font-semibold mb-2">Keranjang Anda Kosong</h2>
            <p class="text-gray-600 mb-6">Anda belum menambahkan menu apapun ke keranjang.</p>
            <a href="index.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors inline-block">
                Lihat Menu
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Daftar Item Keranjang -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border overflow-hidden">
                    <div class="p-4 border-b bg-gray-50">
                        <h2 class="font-semibold">Daftar Menu</h2>
                    </div>
                    
                    <ul class="divide-y" id="cart-items-list">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="p-4 cart-item" data-id="<?= $item['id'] ?>">
                                <div class="flex items-center">
                                    <div class="h-16 w-16 rounded-md overflow-hidden flex-shrink-0">
                                        <img src="<?= htmlspecialchars($item['image'] ? $item['image'] : getPlaceholderUrl(100, 100)) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover">
                                    </div>
                                    
                                    <div class="ml-4 flex-grow">
                                        <h3 class="font-medium"><?= htmlspecialchars($item['name']) ?></h3>
                                        <p class="text-sm text-gray-600">Penjual: <?= htmlspecialchars($item['seller_name']) ?></p>
                                        <p class="text-sm font-medium mt-1">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                                    </div>
                                    
                                    <div class="flex items-center ml-4">
                                        <button class="decrease-quantity p-1 rounded-full hover:bg-gray-100" data-id="<?= $item['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        
                                        <input type="number" class="item-quantity w-12 mx-2 text-center border rounded-md" 
                                               value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" 
                                               data-id="<?= $item['id'] ?>" data-stock="<?= $item['stock'] ?>">
                                        
                                        <button class="increase-quantity p-1 rounded-full hover:bg-gray-100" data-id="<?= $item['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="ml-6 text-right">
                                        <p class="font-medium item-subtotal">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                                        
                                        <button class="remove-item text-red-600 hover:text-red-800 text-sm mt-1" data-id="<?= $item['id'] ?>">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Ringkasan Pesanan -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border p-4 sticky top-4">
                    <h2 class="font-semibold mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Item:</span>
                            <span class="font-medium" id="cart-count"><?= getCartCount() ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Harga:</span>
                            <span class="font-medium" id="cart-total">Rp <?= number_format($cart_total, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <a href="#" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md transition-colors mb-2">
                        Lanjutkan ke Pembayaran
                    </a>
                    
                    <button id="clear-cart" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 text-center py-2 px-4 rounded-md transition-colors">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript untuk keranjang -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk memformat angka
    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    // Fungsi untuk memperbarui tampilan keranjang
    function updateCartDisplay() {
        fetch('api/cart_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_cart'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update jumlah item
                document.getElementById('cart-count').textContent = data.cart_count;
                
                // Update total harga
                document.getElementById('cart-total').textContent = 'Rp ' + formatNumber(data.cart_total);
                
                // Update header cart count
                const headerCartCount = document.getElementById('header-cart-count');
                if (headerCartCount) {
                    headerCartCount.textContent = data.cart_count;
                    headerCartCount.style.display = data.cart_count > 0 ? 'flex' : 'none';
                }
                
                // Jika keranjang kosong, reload halaman
                if (data.cart_items.length === 0) {
                    window.location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Event listener untuk tombol tambah jumlah
    const increaseButtons = document.querySelectorAll('.increase-quantity');
    increaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const quantityInput = document.querySelector(`.item-quantity[data-id="${id}"]`);
            const stock = parseInt(quantityInput.getAttribute('data-stock'));
            let quantity = parseInt(quantityInput.value);
            
            if (quantity < stock) {
                quantity++;
                quantityInput.value = quantity;
                
                // Update di server
                updateCartItem(id, quantity);
            }
        });
    });
    
    // Event listener untuk tombol kurang jumlah
    const decreaseButtons = document.querySelectorAll('.decrease-quantity');
    decreaseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const quantityInput = document.querySelector(`.item-quantity[data-id="${id}"]`);
            let quantity = parseInt(quantityInput.value);
            
            if (quantity > 1) {
                quantity--;
                quantityInput.value = quantity;
                
                // Update di server
                updateCartItem(id, quantity);
            }
        });
    });
    
    // Event listener untuk input jumlah
    const quantityInputs = document.querySelectorAll('.item-quantity');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const stock = parseInt(this.getAttribute('data-stock'));
            let quantity = parseInt(this.value);
            
            // Validasi input
            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
            } else if (quantity > stock) {
                quantity = stock;
            }
            
            this.value = quantity;
            
            // Update di server
            updateCartItem(id, quantity);
        });
    });
    
    // Event listener untuk tombol hapus
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            // Konfirmasi hapus
            if (confirm('Apakah Anda yakin ingin menghapus menu ini dari keranjang?')) {
                // Hapus dari server
                fetch('api/cart_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&item_id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hapus dari tampilan
                        const item = document.querySelector(`.cart-item[data-id="${id}"]`);
                        if (item) {
                            item.remove();
                        }
                        
                        // Update tampilan keranjang
                        updateCartDisplay();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
    
    // Event listener untuk tombol kosongkan keranjang
    const clearCartButton = document.getElementById('clear-cart');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', function() {
            // Konfirmasi kosongkan
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                // Kosongkan di server
                fetch('api/cart_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_cart'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload halaman
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }
    
    // Fungsi untuk update item di keranjang
    function updateCartItem(id, quantity) {
        fetch('api/cart_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update&item_id=${id}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update subtotal
                const item = document.querySelector(`.cart-item[data-id="${id}"]`);
                if (item) {
                    // Ambil harga dari server
                    fetch('api/cart_actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=get_cart'
                    })
                    .then(response => response.json())
                    .then(cartData => {
                        if (cartData.success) {
                            // Cari item di cart
                            const cartItem = cartData.cart_items.find(item => item.id == id);
                            if (cartItem) {
                                // Update subtotal
                                const subtotalElement = item.querySelector('.item-subtotal');
                                if (subtotalElement) {
                                    subtotalElement.textContent = 'Rp ' + formatNumber(cartItem.subtotal);
                                }
                            }
                            
                            // Update tampilan keranjang
                            updateCartDisplay();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});
</script>

<?php
// Footer
include $basePath . '/includes/footer.html';
?>
