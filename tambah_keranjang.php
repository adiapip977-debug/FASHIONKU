<?php

session_start();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$nama = $_POST['nama'];
$harga = $_POST['harga'];
$ukuran = $_POST['ukuran'];
$warna = $_POST['warna'];

$_SESSION['keranjang'][] = [
    'nama' => $nama,
    'harga' => $harga,
    'ukuran' => $ukuran,
    'warna' => $warna,
    'jumlah' => 1
];

header("Location: keranjang.php");
exit;

?>