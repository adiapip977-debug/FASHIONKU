<?php

session_start();

include "koneksi.php";


if (!isset($_SESSION['pelanggan_id'])) {

    header("Location: login.php");
    exit;

}


$pelanggan_id = (int)$_SESSION['pelanggan_id'];


// TANDAI SEMUA SUDAH DIBACA
mysqli_query(
    $koneksi,
    "UPDATE notifikasi
     SET sudah_dibaca=1
     WHERE pelanggan_id=$pelanggan_id"
);


$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM notifikasi
     WHERE pelanggan_id=$pelanggan_id
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Notifikasi - FASHIONKU</title>

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

    </div>

</nav>


<div class="container">

    <h1>
        🔔 Notifikasi
    </h1>


    <?php if (mysqli_num_rows($query) == 0) { ?>

        <div class="produk">

            Belum ada notifikasi.

        </div>

    <?php } ?>


    <?php while ($n = mysqli_fetch_assoc($query)) { ?>

        <div class="produk">

            <p>

                <?php

                echo htmlspecialchars(
                    $n['pesan']
                );

                ?>

            </p>


            <small>

                <?php echo $n['tanggal']; ?>

            </small>


            <?php if (!empty($n['pesanan_id'])) { ?>

                <br><br>

                <a
                    href="detail_pesanan_customer.php?id=<?php echo $n['pesanan_id']; ?>"
                    class="btn"
                >

                    📦 Lihat Pesanan

                </a>

            <?php } ?>

        </div>

    <?php } ?>


</div>

</body>

</html>