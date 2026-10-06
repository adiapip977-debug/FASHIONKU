<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include "../koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

mysqli_query(
    $koneksi,
    "DELETE FROM produk WHERE id=$id"
);

header("Location: index.php");
exit;

?>