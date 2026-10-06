<?php

session_start();

include "koneksi.php";


/* ==============================
   CEK LOGIN
============================== */

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


/* ==============================
   DATA PESANAN
============================== */

$query_pesanan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pesanan
     WHERE id = $id
     AND pelanggan_id = $pelanggan_id"
);


if (mysqli_num_rows($query_pesanan) == 0) {

    die("Pesanan tidak ditemukan.");

}


$pesanan = mysqli_fetch_assoc(
    $query_pesanan
);


/* ==============================
   DETAIL PESANAN
============================== */

$query_detail = mysqli_query(
    $koneksi,
    "SELECT *
     FROM detail_pesanan
     WHERE pesanan_id = $id"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    Invoice #<?php echo $pesanan['id']; ?>
</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    padding: 30px;

    background: #f3f4f6;

    font-family: Arial, Helvetica, sans-serif;

    color: #111827;

}


.invoice {

    max-width: 800px;

    margin: auto;

    background: white;

    padding: 40px;

    border-radius: 16px;

    box-shadow:
        0 8px 30px rgba(0,0,0,.08);

}


.header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    padding-bottom: 25px;

    border-bottom:
        2px solid #111827;

}


.logo {

    font-size: 28px;

    font-weight: bold;

}


.logo span {

    color: #e11d48;

}


.invoice-title {

    text-align: right;

}


.invoice-title h1 {

    margin: 0;

    font-size: 26px;

}


.invoice-title p {

    margin: 5px 0;

    color: #6b7280;

}


.info {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 30px;

    margin: 30px 0;

}


.info-box {

    background: #f9fafb;

    padding: 18px;

    border-radius: 12px;

}


.info-box h3 {

    margin-top: 0;

    font-size: 14px;

    color: #6b7280;

}


.info-box p {

    margin: 7px 0;

}


table {

    width: 100%;

    border-collapse: collapse;

    margin-top: 20px;

}


th {

    background: #111827;

    color: white;

    padding: 13px;

    text-align: left;

    font-size: 13px;

}


td {

    padding: 13px;

    border-bottom:
        1px solid #e5e7eb;

    font-size: 14px;

}


.total {

    display: flex;

    justify-content: flex-end;

    margin-top: 25px;

}


.total-box {

    width: 300px;

}


.total-row {

    display: flex;

    justify-content: space-between;

    padding: 8px 0;

}


.total-final {

    border-top:
        2px solid #111827;

    margin-top: 8px;

    padding-top: 12px;

    font-size: 20px;

    font-weight: bold;

}


.status {

    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    background: #e5e7eb;

    font-size: 12px;

    font-weight: bold;

}


.footer {

    margin-top: 35px;

    padding-top: 20px;

    border-top:
        1px solid #e5e7eb;

    text-align: center;

    color: #6b7280;

    font-size: 13px;

}


.buttons {

    max-width: 800px;

    margin: 20px auto;

    display: flex;

    gap: 10px;

}


.btn {

    flex: 1;

    padding: 13px;

    border: none;

    border-radius: 9px;

    background: #111827;

    color: white;

    text-decoration: none;

    text-align: center;

    cursor: pointer;

    font-weight: bold;

}


.btn:hover {

    opacity: .9;

}


@media print {

    body {

        background: white;

        padding: 0;

    }


    .invoice {

        box-shadow: none;

        border-radius: 0;

        max-width: none;

    }


    .buttons {

        display: none;

    }

}


@media(max-width:600px) {

    body {

        padding: 10px;

    }


    .invoice {

        padding: 20px;

    }


    .header {

        flex-direction: column;

        gap: 15px;

    }


    .invoice-title {

        text-align: left;

    }


    .info {

        grid-template-columns: 1fr;

        gap: 10px;

    }


    table {

        font-size: 12px;

    }


    th,
    td {

        padding: 8px;

    }


    .total-box {

        width: 100%;

    }

}

</style>

</head>


<body>


<div class="invoice">


    <!-- HEADER -->

    <div class="header">


        <div class="logo">

            FASHION<span>KU</span>

        </div>


        <div class="invoice-title">

            <h1>
                INVOICE
            </h1>

            <p>
                #<?php echo $pesanan['id']; ?>
            </p>

            <p>

                <?php

                echo date(
                    'd/m/Y H:i',
                    strtotime($pesanan['tanggal'])
                );

                ?>

            </p>

        </div>


    </div>


    <!-- INFO -->

    <div class="info">


        <div class="info-box">

            <h3>
                INFORMASI PEMBELI
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


        <div class="info-box">

            <h3>
                INFORMASI PESANAN
            </h3>


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

                <span class="status">

                    <?php
                    echo htmlspecialchars(
                        $pesanan['status']
                    );
                    ?>

                </span>

            </p>

        </div>


    </div>


    <!-- PRODUK -->

    <table>

        <thead>

            <tr>

                <th>
                    Produk
                </th>

                <th>
                    Ukuran
                </th>

                <th>
                    Warna
                </th>

                <th>
                    Qty
                </th>

                <th>
                    Subtotal
                </th>

            </tr>

        </thead>


        <tbody>

        <?php while (
            $detail = mysqli_fetch_assoc(
                $query_detail
            )
        ): ?>

            <tr>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $detail['nama_produk']
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $detail['ukuran']
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $detail['warna']
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo $detail['jumlah'];
                    ?>
                </td>

                <td>

                    Rp

                    <?php

                    echo number_format(
                        $detail['subtotal'],
                        0,
                        ',',
                        '.'
                    );

                    ?>

                </td>

            </tr>

        <?php endwhile; ?>

        </tbody>

    </table>


    <!-- TOTAL -->

    <div class="total">

        <div class="total-box">

            <div class="total-row">

                <span>
                    Total Pesanan
                </span>

                <span>
                    <b>
                        Rp
                        <?php

                        echo number_format(
                            $pesanan['total'],
                            0,
                            ',',
                            '.'
                        );

                        ?>
                    </b>
                </span>

            </div>


            <div class="total-row total-final">

                <span>
                    TOTAL
                </span>

                <span>

                    Rp

                    <?php

                    echo number_format(
                        $pesanan['total'],
                        0,
                        ',',
                        '.'
                    );

                    ?>

                </span>

            </div>

        </div>

    </div>


    <div class="footer">

        Terima kasih sudah berbelanja di
        <b>FASHIONKU</b> ❤️

    </div>


</div>


<div class="buttons">

    <button
        class="btn"
        onclick="window.print()"
    >
        🖨️ Cetak Invoice
    </button>


    <a
        href="detail_pesanan_customer.php?id=<?php echo $id; ?>"
        class="btn"
    >
        ← Kembali
    </a>

</div>


</body>

</html>