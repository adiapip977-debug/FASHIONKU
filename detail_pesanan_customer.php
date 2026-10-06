<?php

session_start();

include "koneksi.php";


if (!isset($_SESSION['pelanggan_id'])) {

    header("Location: login.php");
    exit;

}


$pelanggan_id = (int)$_SESSION['pelanggan_id'];

$id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($id <= 0) {

    die("Pesanan tidak ditemukan.");

}


/* =========================
   PESANAN
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pesanan
     WHERE id = $id
     AND pelanggan_id = $pelanggan_id"
);


if (mysqli_num_rows($query) == 0) {

    die("Pesanan tidak ditemukan.");

}


$pesanan = mysqli_fetch_assoc($query);


/* =========================
   DETAIL
========================= */

$detail = mysqli_query(
    $koneksi,
    "SELECT *
     FROM detail_pesanan
     WHERE pesanan_id = $id"
);


/* =========================
   RIWAYAT STATUS
========================= */

$riwayat = mysqli_query(
    $koneksi,
    "SELECT *
     FROM riwayat_status
     WHERE pesanan_id = $id
     ORDER BY tanggal ASC"
);


/* =========================
   STATUS
========================= */

$status_sekarang = $pesanan['status'];

$status_aktif = [
    'Menunggu',
    'Diproses',
    'Dikirim',
    'Selesai'
];

$posisi_status = array_search(
    $status_sekarang,
    $status_aktif
);

if ($posisi_status === false) {
    $posisi_status = -1;
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    Detail Pesanan #<?php echo $id; ?>
</title>


<link rel="stylesheet"
      href="style.css">


<style>

/* =========================
   CONTAINER
========================= */

.detail-container {

    max-width: 1000px;

    margin: 40px auto;

}


.detail-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

}


.detail-header h1 {

    margin: 0;

}


.invoice-btn {

    display: inline-block;

    background: #111827;

    color: white;

    text-decoration: none;

    padding: 11px 16px;

    border-radius: 9px;

    font-weight: bold;

}


/* =========================
   INFO
========================= */

.info-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 18px;

    margin-bottom: 25px;

}


.info-card {

    background: white;

    padding: 22px;

    border-radius: 15px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 5px 18px rgba(0,0,0,.06);

}


.info-card h3 {

    margin-bottom: 15px;

}


.info-card p {

    margin: 8px 0;

    color: #4b5563;

}


/* =========================
   TIMELINE
========================= */

.timeline-card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 5px 18px rgba(0,0,0,.06);

    margin-bottom: 25px;

}


.timeline {

    position: relative;

    margin-top: 25px;

}


.timeline::before {

    content: "";

    position: absolute;

    left: 15px;

    top: 10px;

    bottom: 10px;

    width: 3px;

    background: #e5e7eb;

}


.timeline-item {

    position: relative;

    padding-left: 50px;

    padding-bottom: 28px;

}


.timeline-item:last-child {

    padding-bottom: 0;

}


.timeline-dot {

    position: absolute;

    left: 7px;

    top: 2px;

    width: 19px;

    height: 19px;

    border-radius: 50%;

    background: #d1d5db;

    border: 4px solid white;

    box-shadow:
        0 0 0 2px #d1d5db;

    z-index: 2;

}


.timeline-item.aktif
.timeline-dot {

    background: #111827;

    box-shadow:
        0 0 0 2px #111827;

}


.timeline-item.selesai
.timeline-dot {

    background: #16a34a;

    box-shadow:
        0 0 0 2px #16a34a;

}


.timeline-item h4 {

    margin: 0 0 5px;

}


.timeline-item p {

    margin: 0;

    color: #6b7280;

    font-size: 13px;

}


/* =========================
   TABLE
========================= */

.table-card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 5px 18px rgba(0,0,0,.06);

    overflow-x: auto;

}


.table-card table {

    width: 100%;

    border-collapse: collapse;

}


.table-card th {

    background: #111827;

    color: white;

    padding: 12px;

    text-align: left;

}


.table-card td {

    padding: 12px;

    border-bottom:
        1px solid #e5e7eb;

}


/* =========================
   TOTAL
========================= */

.total-pesanan {

    text-align: right;

    font-size: 20px;

    font-weight: bold;

    margin-top: 20px;

}


/* =========================
   ULASAN
========================= */

.btn-ulasan {

    display: inline-block;

    background: #fbbf24;

    color: #111827;

    text-decoration: none;

    padding: 8px 12px;

    border-radius: 8px;

    font-size: 12px;

    font-weight: bold;

}


/* =========================
   MOBILE
========================= */

@media(max-width:700px) {

    .detail-container {

        width: 94%;

        margin: 25px auto;

    }


    .detail-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 12px;

    }


    .info-grid {

        grid-template-columns: 1fr;

    }


    .timeline-card,
    .table-card,
    .info-card {

        padding: 18px;

    }

}

</style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar">

    <h2>
        👕 FASHIONKU
    </h2>


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

    </div>

</nav>


<div class="detail-container">


    <!-- HEADER -->

    <div class="detail-header">

        <h1>

            📦 Pesanan
            #<?php echo $id; ?>

        </h1>


        <a
            href="invoice.php?id=<?php echo $id; ?>"
            class="invoice-btn"
        >
            🧾 Lihat Invoice
        </a>

    </div>


    <!-- INFO -->

    <div class="info-grid">


        <div class="info-card">

            <h3>
                👤 Informasi Pembeli
            </h3>


            <p>

                <b>Nama:</b>

                <?php
                echo htmlspecialchars(
                    $pesanan['nama_pembeli']
                );
                ?>

            </p>


            <p>

                <b>No. HP:</b>

                <?php
                echo htmlspecialchars(
                    $pesanan['no_hp']
                );
                ?>

            </p>


            <p>

                <b>Alamat:</b><br>

                <?php
                echo nl2br(
                    htmlspecialchars(
                        $pesanan['alamat']
                    )
                );
                ?>

            </p>

        </div>


        <div class="info-card">

            <h3>
                💳 Informasi Pesanan
            </h3>


            <p>

                <b>Tanggal:</b>

                <?php

                echo date(
                    'd/m/Y H:i',
                    strtotime(
                        $pesanan['tanggal']
                    )
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

                <?php
                echo htmlspecialchars(
                    $pesanan['status']
                );
                ?>

            </p>

        </div>

    </div>


    <!-- =========================
         TIMELINE
    ========================= -->

    <div class="timeline-card">

        <h2>
            📍 Status Pesanan
        </h2>


        <div class="timeline">


        <?php

        $status_default = [
            'Menunggu',
            'Diproses',
            'Dikirim',
            'Selesai'
        ];


        $riwayat_data = [];

        while (
            $r = mysqli_fetch_assoc($riwayat)
        ) {

            $riwayat_data[
                $r['status']
            ] = $r['tanggal'];

        }


        foreach (
            $status_default
            as $index => $status
        ):

            $class = "";


            if (
                isset(
                    $riwayat_data[$status]
                )
            ) {

                $class = "selesai";

            }


            if (
                $status === $status_sekarang
            ) {

                $class = "aktif";

            }

        ?>


            <div
                class="timeline-item <?php echo $class; ?>"
            >

                <div class="timeline-dot"></div>


                <h4>

                    <?php

                    if ($status === 'Menunggu') {
                        echo "🕐 Pesanan Dibuat";
                    }

                    elseif ($status === 'Diproses') {
                        echo "⚙️ Pesanan Diproses";
                    }

                    elseif ($status === 'Dikirim') {
                        echo "🚚 Pesanan Dikirim";
                    }

                    elseif ($status === 'Selesai') {
                        echo "✅ Pesanan Selesai";
                    }

                    ?>

                </h4>


                <?php if (
                    isset(
                        $riwayat_data[$status]
                    )
                ): ?>

                    <p>

                        <?php

                        echo date(
                            'd/m/Y H:i',
                            strtotime(
                                $riwayat_data[$status]
                            )
                        );

                        ?>

                    </p>

                <?php elseif (
                    $status === $status_sekarang
                ): ?>

                    <p>
                        Status saat ini
                    </p>

                <?php else: ?>

                    <p>
                        Belum
                    </p>

                <?php endif; ?>


            </div>


        <?php endforeach; ?>


        <?php if (
            $status_sekarang === 'Dibatalkan'
        ): ?>


            <div
                class="timeline-item aktif"
            >

                <div class="timeline-dot"></div>

                <h4>
                    ❌ Pesanan Dibatalkan
                </h4>

                <p>
                    Pesanan ini dibatalkan.
                </p>

            </div>


        <?php endif; ?>


        </div>

    </div>


    <!-- =========================
         PRODUK
    ========================= -->

    <div class="table-card">

        <h2>
            🛍️ Produk Pesanan
        </h2>


        <table>

            <thead>

                <tr>

                    <th>
                        Produk
                    </th>

                    <th>
                        Harga
                    </th>

                    <th>
                        Ukuran
                    </th>

                    <th>
                        Warna
                    </th>

                    <th>
                        Jumlah
                    </th>

                    <th>
                        Subtotal
                    </th>

                    <?php if (
                        $pesanan['status']
                        === 'Selesai'
                    ): ?>

                        <th>
                            Ulasan
                        </th>

                    <?php endif; ?>

                </tr>

            </thead>


            <tbody>


            <?php while (
                $d = mysqli_fetch_assoc($detail)
            ): ?>


                <tr>

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $d['nama_produk']
                        );

                        ?>

                    </td>


                    <td>

                        Rp

                        <?php

                        echo number_format(
                            $d['harga'],
                            0,
                            ',',
                            '.'
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $d['ukuran']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $d['warna']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo $d['jumlah'];

                        ?>

                    </td>


                    <td>

                        Rp

                        <?php

                        echo number_format(
                            $d['subtotal'],
                            0,
                            ',',
                            '.'
                        );

                        ?>

                    </td>


                    <?php if (
                        $pesanan['status']
                        === 'Selesai'
                    ): ?>


                        <td>

                        <?php

                        /*
                         * Cari produk berdasarkan
                         * nama karena detail_pesanan
                         * kamu memakai nama_produk.
                         */

                        $nama_produk_db =
                            mysqli_real_escape_string(
                                $koneksi,
                                $d['nama_produk']
                            );


                        $cari_produk =
                            mysqli_query(
                                $koneksi,
                                "SELECT id
                                 FROM produk
                                 WHERE nama =
                                 '$nama_produk_db'
                                 LIMIT 1"
                            );


                        if (
                            mysqli_num_rows(
                                $cari_produk
                            ) > 0
                        ) {

                            $produk_data =
                                mysqli_fetch_assoc(
                                    $cari_produk
                                );

                            $produk_id =
                                $produk_data['id'];


                            $cek_ulasan =
                                mysqli_query(
                                    $koneksi,
                                    "SELECT id
                                     FROM ulasan
                                     WHERE pelanggan_id =
                                     $pelanggan_id
                                     AND produk_id =
                                     $produk_id
                                     AND pesanan_id =
                                     $id"
                                );


                            if (
                                mysqli_num_rows(
                                    $cek_ulasan
                                ) > 0
                            ) {

                                echo "✅ Sudah Diulas";

                            } else {

                        ?>

                                <a
                                    href="ulasan.php?pesanan_id=<?php echo $id; ?>&produk_id=<?php echo $produk_id; ?>"
                                    class="btn-ulasan"
                                >
                                    ⭐ Beri Ulasan
                                </a>

                        <?php

                            }

                        } else {

                            echo "-";

                        }

                        ?>

                        </td>


                    <?php endif; ?>


                </tr>


            <?php endwhile; ?>


            </tbody>

        </table>


        <div class="total-pesanan">

            Total:

            Rp

            <?php

            echo number_format(
                $pesanan['total'],
                0,
                ',',
                '.'
            );

            ?>

        </div>

    </div>


</div>


<footer>

    © <?php echo date('Y'); ?> FASHIONKU

</footer>


</body>

</html>