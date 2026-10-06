<?php

session_start();

include "koneksi.php";


if (!isset($_SESSION['pelanggan_id'])) {

    header("Location: login.php");
    exit;

}


$pelanggan_id = (int)$_SESSION['pelanggan_id'];

$produk_id = isset($_GET['produk_id'])
    ? (int)$_GET['produk_id']
    : 0;


if ($produk_id <= 0) {

    header("Location: index.php");
    exit;

}


$cek = mysqli_query(
    $koneksi,
    "SELECT id FROM wishlist
     WHERE pelanggan_id=$pelanggan_id
     AND produk_id=$produk_id"
);


if (mysqli_num_rows($cek) > 0) {

    mysqli_query(
        $koneksi,
        "DELETE FROM wishlist
         WHERE pelanggan_id=$pelanggan_id
         AND produk_id=$produk_id"
    );

} else {

    mysqli_query(
        $koneksi,
        "INSERT INTO wishlist
        (pelanggan_id, produk_id)
        VALUES
        ($pelanggan_id, $produk_id)"
    );

}


$kembali = isset($_GET['kembali'])
    ? $_GET['kembali']
    : 'index.php';


header("Location: " . $kembali);
exit;

?>