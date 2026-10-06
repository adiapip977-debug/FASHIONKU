<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include "../koneksi.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Ambil data pesanan
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM pesanan WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$pesanan = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

// Ambil detail produk yang dibeli
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM detail_pesanan WHERE pesanan_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$detail = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Detail Pesanan</title>

    <link rel="stylesheet" href="../style.css">

    <style>
        .tabel-detail {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .tabel-detail th,
        .tabel-detail td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .tabel-detail th {
            background: #111827;
            color: white;
        }

        .info-pesanan {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .tabel-wrapper {
            overflow-x: auto;
        }
    </style>

</head>

<body>

<nav class="navbar">

    <h2>🛍️ DETAIL PESANAN</h2>

    <div>
        <a href="pesanan.php">Kembali</a>
        <a href="logout.php">Logout</a>
    </div>

</nav>

<div class="container">

    <h1>Pesanan #<?php echo $pesanan['id']; ?></h1>

    <div class="info-pesanan">

        <p>
            <b>Nama:</b>
            <?php echo htmlspecialchars($pesanan['nama_pembeli']); ?>
        </p>

        <br>

        <p>
            <b>No. HP:</b>
            <?php echo htmlspecialchars($pesanan['no_hp']); ?>
        </p>

        <br>

        <p>
            <b>Alamat:</b>
            <?php echo nl2br(htmlspecialchars($pesanan['alamat'])); ?>
        </p>

        <br>

        <p>
            <b>Status:</b>
            <?php echo htmlspecialchars($pesanan['status']); ?>
        </p>

        <br>

        <p>
            <b>Tanggal:</b>
            <?php echo $pesanan['tanggal']; ?>
        </p>

        <p>
    <b>Metode Pembayaran:</b>
    <?php echo htmlspecialchars($pesanan['metode_pembayaran']); ?>
</p>

<?php if (!empty($pesanan['bukti_pembayaran'])) { ?>

<p>
    <b>Bukti Pembayaran:</b>
</p>

<img
    src="../bukti_pembayaran/<?php echo htmlspecialchars($pesanan['bukti_pembayaran']); ?>"
    style="max-width:400px; border-radius:10px;"
>

<?php } else { ?>

<p>
    <b>Bukti Pembayaran:</b>
    Belum ada
</p>

<?php } ?>

    </div>

    <h2>Produk yang Dibeli</h2>

    <br>

    <div class="tabel-wrapper">

        <table class="tabel-detail">

            <tr>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Ukuran</th>
                <th>Warna</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>

            <?php while ($d = mysqli_fetch_assoc($detail)) { ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($d['nama_produk']); ?>
                    </td>

                    <td>
                        Rp <?php echo number_format(
                            $d['harga'],
                            0,
                            ',',
                            '.'
                        ); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($d['ukuran']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($d['warna']); ?>
                    </td>

                    <td>
                        <?php echo $d['jumlah']; ?>
                    </td>

                    <td>
                        Rp <?php echo number_format(
                            $d['subtotal'],
                            0,
                            ',',
                            '.'
                        ); ?>
                    </td>

                </tr>

            <?php } ?>

            <tr>

                <th colspan="5">TOTAL</th>

                <th>
                    Rp <?php echo number_format(
                        $pesanan['total'],
                        0,
                        ',',
                        '.'
                    ); ?>
                </th>

            </tr>

        </table>

    </div>

</div>

</body>

</html>