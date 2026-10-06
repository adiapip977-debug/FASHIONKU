<?php

session_start();

$index = $_GET['index'];

if (isset($_SESSION['keranjang'][$index])) {
    unset($_SESSION['keranjang'][$index]);
}

$_SESSION['keranjang'] = array_values($_SESSION['keranjang']);

header("Location: keranjang.php");
exit;

?>