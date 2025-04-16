<?php
// Buat gambar placeholder
$width = isset($_GET['width']) ? (int)$_GET['width'] : 300;
$height = isset($_GET['height']) ? (int)$_GET['height'] : 300;

// Buat gambar baru
$image = imagecreatetruecolor($width, $height);

// Warna latar belakang (abu-abu muda)
$bg_color = imagecolorallocate($image, 240, 240, 240);
// Warna teks (abu-abu tua)
$text_color = imagecolorallocate($image, 120, 120, 120);
// Warna garis (abu-abu sedang)
$line_color = imagecolorallocate($image, 200, 200, 200);

// Isi latar belakang
imagefill($image, 0, 0, $bg_color);

// Gambar garis diagonal
imageline($image, 0, 0, $width, $height, $line_color);
imageline($image, 0, $height, $width, 0, $line_color);

// Tambahkan teks
$text = "Placeholder {$width}x{$height}";
$font_size = 5; // Ukuran font (1-5)
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);

// Posisikan teks di tengah
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;

// Tambahkan teks ke gambar
imagestring($image, $font_size, $x, $y, $text, $text_color);

// Output gambar sebagai PNG
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
