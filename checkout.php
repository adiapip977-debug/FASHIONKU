<?php

session_start();
include "koneksi.php";

if (empty($_SESSION['keranjang'])) {
    header("Location: index.php");
    exit;
}

$total = 0;

foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['harga'] * $item['jumlah'];
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Checkout</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>
        <a href="index.php">Home</a>
        <a href="keranjang.php">🛒 Keranjang</a>
    </div>

</nav>


<div class="container">

    <h1>Checkout</h1>

    <form action="proses_checkout.php" method="POST">

        <label>Nama Lengkap</label>

        <br>

        <input
            type="text"
            name="nama"
            required
        >

        <br><br>


        <label>Nomor HP</label>

        <br>

        <input
            type="text"
            name="no_hp"
            required
        >

        <br><br>


        <label>Alamat Lengkap</label>

        <br>

        <textarea
            name="alamat"
            required
        ></textarea>

        <br><br>

                <label>Metode Pembayaran</label>

        <br><br>

        <select name="metode_pembayaran" required>

            <option value="">
                -- Pilih Pembayaran --
            </option>

            <option value="COD">
                COD
            </option>

            <option value="Transfer Bank">
                Transfer Bank
            </option>

        </select>

        <br><br>


        <h2>
            Total:
            Rp <?php echo number_format(
                $total,
                0,
                ',',
                '.'
            ); ?>
        </h2>

        <br>

        <button
            type="submit"
            class="btn"
        >
            Buat Pesanan
        </button>

    </form>

</div>

</body>

</html>