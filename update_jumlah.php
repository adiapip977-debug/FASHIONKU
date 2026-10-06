<?php

session_start();

$index = $_GET['index'];
$aksi = $_GET['aksi'];

if (isset($_SESSION['keranjang'][$index])) {

    if ($aksi == "tambah") {
        $_SESSION['keranjang'][$index]['jumlah']++;
    }

    if ($aksi == "kurang") {

        $_SESSION['keranjang'][$index]['jumlah']--;

        if ($_SESSION['keranjang'][$index]['jumlah'] <= 0) {
            unset($_SESSION['keranjang'][$index]);
            $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
        }
    }
}

header("Location: keranjang.php");
exit;

?>