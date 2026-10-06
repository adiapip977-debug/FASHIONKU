<?php

session_start();
include "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pesanan WHERE id = $id"
);

$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

if ($pesanan['metode_pembayaran'] == 'COD') {
    header("Location: berhasil.php?id=$id");
    exit;
}

if (isset($_POST['upload'])) {

    $bukti = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];

    $nama_file = time() . "_" . $bukti;

    move_uploaded_file(
        $tmp,
        "bukti_pembayaran/" . $nama_file
    );

    mysqli_query(
        $koneksi,
        "UPDATE pesanan
         SET bukti_pembayaran='$nama_file',
             status='Diproses'
         WHERE id=$id"
    );

    header("Location: berhasil.php?id=$id");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Pembayaran - FASHIONKU</title>

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

    <h1>💳 Pembayaran</h1>

    <div class="produk">

        <h3>Transfer Bank</h3>

        <p>
            Silakan transfer ke rekening berikut:
        </p>

        <br>

        <p>
            <b>Bank BCA</b><br>
            No. Rekening: <b>1234567890</b><br>
            A/N: <b>FASHIONKU</b>
        </p>

        <br>

        <p>
            Total pembayaran:
        </p>

        <h2 class="harga">

            Rp <?php echo number_format(
                $pesanan['total'],
                0,
                ',',
                '.'
            ); ?>

        </h2>


        <?php if (empty($pesanan['bukti_pembayaran'])) { ?>

            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <label>
                    Upload Bukti Transfer
                </label>

                <br><br>

                <input
                    type="file"
                    name="bukti"
                    accept="image/*"
                    required
                >

                <br><br>

                <button
                    type="submit"
                    name="upload"
                    class="btn"
                >

                    📸 Upload Bukti Pembayaran

                </button>

            </form>

        <?php } else { ?>

            <p>
                ✅ Bukti pembayaran sudah diupload.
            </p>

        <?php } ?>

    </div>

</div>

</body>

</html>