<?php

session_start();

include "koneksi.php";


if (!isset($_SESSION['pelanggan_id'])) {

    header("Location: login.php");
    exit;

}


$pelanggan_id = (int)$_SESSION['pelanggan_id'];


$query = mysqli_query(
    $koneksi,
    "SELECT produk.*
     FROM wishlist
     JOIN produk
     ON wishlist.produk_id = produk.id
     WHERE wishlist.pelanggan_id=$pelanggan_id
     ORDER BY wishlist.id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Wishlist - FASHIONKU</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>

        <a href="index.php">Home</a>

        <a href="pesanan_saya.php">
            📦 Pesanan Saya
        </a>

        <a href="wishlist.php">
            ❤️ Wishlist
        </a>

        <a href="logout_customer.php">
            Logout
        </a>

    </div>

</nav>


<div class="container">

    <h1>❤️ Wishlist Saya</h1>


    <?php if (mysqli_num_rows($query) == 0) { ?>

        <div class="produk">

            <h3>
                Wishlist masih kosong.
            </h3>

            <p>
                Simpan produk yang kamu suka di sini.
            </p>

            <a href="index.php" class="btn">
                🛍️ Belanja
            </a>

        </div>

    <?php } ?>


    <div class="produk-container">


        <?php while ($produk = mysqli_fetch_assoc($query)) { ?>

            <div class="produk">


                <?php if (!empty($produk['gambar'])) { ?>

                    <img
                        src="images/<?php echo htmlspecialchars($produk['gambar']); ?>"
                        width="150"
                        height="150"
                        style="object-fit:contain; display:block; margin:auto;"
                    >

                <?php } ?>


                <h3>

                    <?php echo htmlspecialchars($produk['nama']); ?>

                </h3>


                <div class="harga">

                    Rp <?php

                    echo number_format(
                        $produk['harga'],
                        0,
                        ',',
                        '.'
                    );

                    ?>

                </div>


                <a
                    href="detail_produk.php?id=<?php echo $produk['id']; ?>"
                    class="btn"
                >
                    👁️ Lihat Produk
                </a>


                <a
                    href="wishlist_toggle.php?produk_id=<?php echo $produk['id']; ?>&kembali=wishlist.php"
                    class="btn"
                >
                    💔 Hapus
                </a>


            </div>

        <?php } ?>


    </div>

</div>

</body>

</html>