<?php

session_start();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$total = 0;

foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['harga'] * $item['jumlah'];
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Keranjang Belanja</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>

        <a href="index.php">Home</a>

        <a href="keranjang.php">
            🛒 Keranjang
        </a>

    </div>

</nav>


<div class="container">

    <h1>🛒 Keranjang Belanja</h1>


    <?php if (empty($_SESSION['keranjang'])) { ?>

        <p>Keranjang kamu masih kosong.</p>

        <br>

        <a href="index.php" class="btn">
            Belanja Sekarang
        </a>

    <?php } else { ?>


        <?php foreach ($_SESSION['keranjang'] as $index => $item) { ?>

            <div class="produk">

                <h3>
                    <?php echo $item['nama']; ?>
                </h3>

                <p>
                    Harga:
                    Rp <?php echo number_format(
                        $item['harga'],
                        0,
                        ',',
                        '.'
                    ); ?>
                </p>

                <p>
                    Ukuran:
                    <?php echo $item['ukuran']; ?>
                </p>

                <p>
                    Warna:
                    <?php echo $item['warna']; ?>
                </p>


                <p><b>Jumlah:</b></p>

                <div class="jumlah">

                    <a
                        href="update_jumlah.php?index=<?php echo $index; ?>&aksi=kurang"
                        class="jumlah-btn"
                    >
                        −
                    </a>

                    <span>
                        <?php echo $item['jumlah']; ?>
                    </span>

                    <a
                        href="update_jumlah.php?index=<?php echo $index; ?>&aksi=tambah"
                        class="jumlah-btn"
                    >
                        +
                    </a>

                </div>


                <p>

                    Subtotal:
                    Rp <?php echo number_format(
                        $item['harga'] * $item['jumlah'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </p>


                <br>


                <a
                    href="hapus_keranjang.php?index=<?php echo $index; ?>"
                    class="btn"
                >
                    🗑️ Hapus
                </a>

            </div>

            <br>

        <?php } ?>


        <div class="total">

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

          <a href="checkout.php" class="btn">
    💳 Lanjut Checkout
</a>

        </div>


    <?php } ?>

</div>

</body>

</html>