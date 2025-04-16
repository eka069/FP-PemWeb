<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Include cart functions if not already included
if (!function_exists('getCartCount')) {
  require_once __DIR__ . '/../functions/cart_functions.php';
}

// Get cart count
$cart_count = function_exists('getCartCount') ? getCartCount() : 0;

// Determine if we're in a subdirectory
$isSubdirectory = strpos($_SERVER['PHP_SELF'], '/seller/') !== false || 
                 strpos($_SERVER['PHP_SELF'], '/admin/') !== false;

// Set the home URL based on directory level
$homeUrl = $isSubdirectory ? '../index.html' : 'index.html';
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>FAST KANTIN - Pesan Makanan Kantin dengan Cepat</title>
 <meta name="description" content="Aplikasi pemesanan makanan kantin untuk kampus">
 
 <!-- Tailwind CSS -->
 <script src="https://cdn.tailwindcss.com"></script>
 
 <!-- Font -->
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
 
 <style>
     body {
         font-family: 'Inter', sans-serif;
     }
     .cart-badge {
         position: absolute;
         top: -8px;
         right: -8px;
         display: <?= $cart_count > 0 ? 'flex' : 'none' ?>;
         align-items: center;
         justify-content: center;
         width: 18px;
         height: 18px;
         background-color: #ef4444;
         color: white;
         font-size: 10px;
         font-weight: 600;
         border-radius: 9999px;
     }
 </style>
</head>
<body class="bg-gray-50">
 <header class="border-b bg-white">
     <div class="container mx-auto px-4 py-3 flex items-center justify-between">
         <a href="<?= $homeUrl ?>" class="font-bold text-xl">FAST KANTIN</a>
         
         <nav class="flex items-center gap-4">
             <a href="<?= $homeUrl ?>" class="text-sm font-medium hover:text-blue-600 transition-colors">Menu</a>
             <a href="<?= $isSubdirectory ? 'index.php' : 'seller/index.php' ?>" class="text-sm font-medium hover:text-blue-600 transition-colors">Penjual</a>
             
             <div class="flex items-center gap-2">
                 <a href="<?= $isSubdirectory ? '../keranjang.php' : 'keranjang.php' ?>" class="p-2 hover:bg-gray-100 rounded-full relative">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                     </svg>
                     <span class="cart-badge" id="header-cart-count"><?= $cart_count ?></span>
                     <span class="sr-only">Keranjang</span>
                 </a>
                 <div class="relative">
                     <button id="profile-button" class="p-2 hover:bg-gray-100 rounded-full">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                         </svg>
                         <span class="sr-only">Profil</span>
                     </button>
                     <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                         <a href="<?= $isSubdirectory ? '../profil.php' : 'profil.php' ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                         <a href="<?= $isSubdirectory ? '../riwayat-pesanan.php' : 'riwayat-pesanan.php' ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Riwayat Pesanan</a>
                         <a href="<?= $isSubdirectory ? '../pengaturan.php' : 'pengaturan.php' ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pengaturan</a>
                         <hr class="my-1">
                         <a href="<?= $isSubdirectory ? '../login.php' : 'login.php' ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Masuk</a>
                         <a href="<?= $isSubdirectory ? '../daftar.php' : 'daftar.php' ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Daftar</a>
                     </div>
                 </div>
             </div>
         </nav>
     </div>
 </header>

 <!-- Tambahkan script untuk dropdown profil -->
 <script>
 document.addEventListener('DOMContentLoaded', function() {
     // Toggle dropdown profil
     const profileButton = document.getElementById('profile-button');
     const profileDropdown = document.getElementById('profile-dropdown');
     
     if (profileButton && profileDropdown) {
         profileButton.addEventListener('click', function(e) {
             e.stopPropagation(); // Mencegah event klik menyebar ke document
             profileDropdown.classList.toggle('hidden');
             console.log('Profile button clicked, dropdown visibility:', !profileDropdown.classList.contains('hidden'));
         });
         
         // Tutup dropdown jika klik di luar
         document.addEventListener('click', function(event) {
             if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                 profileDropdown.classList.add('hidden');
             }
         });
     } else {
         console.error('Profile button or dropdown not found in DOM');
     }
 });
 </script>
