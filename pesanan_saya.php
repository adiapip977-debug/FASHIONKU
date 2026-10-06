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
    "SELECT *
     FROM pesanan
     WHERE pelanggan_id = $pelanggan_id
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Pesanan Saya - FASHIONKU</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .pesanan-card {

            background: white;

            padding: 20px;

            border-radius: 12px;

            margin-top: 20px;

            box-shadow:
                0 4px 12px rgba(0,0,0,0.08);

        }

        .pesanan-card p {

            margin: 8px 0;

        }

        .status-pesanan {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 6px;

            background: #111827;

            color: white;

            font-weight: bold;

        }

        .detail-btn {

            display: inline-block;

            background: #111827;

            color: white;

            text-decoration: none;

            padding: 10px 15px;

            border-radius: 8px;

            margin-top: 10px;

        }

        .kosong {

            background: white;

            padding: 30px;

            border-radius: 12px;

            margin-top: 25px;

            text-align: center;

        }

    </style>

</head>


<body>


<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>

        <a href="index.php">
            Home
        </a>

        <a href="keranjang.php">
            🛒 Keranjang
        </a>

        <a href="pesanan_saya.php">
            📦 Pesanan Saya
        </a>

        <a href="logout_customer.php">
            Logout
        </a>

    </div>

</nav>


<div class="container">

    <h1>📦 Pesanan Saya</h1>

    <p>
        Lihat semua pesanan yang pernah kamu buat.
    </p>


    <?php if (mysqli_num_rows($query) == 0) { ?>


        <div class="kosong">

            <h3>
                Belum ada pesanan.
            </h3>

            <p>
                Yuk mulai belanja di FASHIONKU.
            </p>

            <a
                href="index.php"
                class="btn"
            >
                🛍️ Belanja Sekarang
            </a>

        </div>


    <?php } else { ?>


        <?php while ($pesanan = mysqli_fetch_assoc($query)) { ?>


            <div class="pesanan-card">


                <h3>

                    Pesanan #<?php echo $pesanan['id']; ?>

                </h3>


                <p>

                    <b>Tanggal:</b>

                    <?php echo $pesanan['tanggal']; ?>

                </p>


                <p>

                    <b>Total:</b>

                    Rp

                    <?php

                    echo number_format(
                        $pesanan['total'],
                        0,
                        ',',
                        '.'
                    );

                    ?>

                </p>


                <p>

                    <b>Pembayaran:</b>

                    <?php

                    echo htmlspecialchars(
                        $pesanan['metode_pembayaran']
                    );

                    ?>

                </p>


                <p>

                    <b>Status:</b>

                    <span class="status-pesanan">

                        <?php

                        echo htmlspecialchars(
                            $pesanan['status']
                        );

                        ?>

                    </span>

                </p>


                <a
                    href="detail_pesanan_customer.php?id=<?php echo $pesanan['id']; ?>"
                    class="detail-btn"
                >

                    👁️ Lihat Detail

                </a>


            </div>


        <?php } ?>


    <?php } ?>


</div>


</body>

</html>