<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


/* =====================================================
   STATISTIK
===================================================== */

$total_produk = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM produk"
    )
)['total'];


$total_pesanan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM pesanan"
    )
)['total'];


$total_menunggu = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM pesanan
         WHERE status='Menunggu'"
    )
)['total'];


$total_selesai = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM pesanan
         WHERE status='Selesai'"
    )
)['total'];


$total_pendapatan = mysqli_fetch_assoc(
    mysqli_query(
        $koneksi,
        "SELECT COALESCE(SUM(total),0) AS total
         FROM pesanan
         WHERE status='Selesai'"
    )
)['total'];


/* =====================================================
   DATA GRAFIK PENJUALAN 7 HARI
===================================================== */

$label_hari = [];
$data_penjualan = [];

for ($i = 6; $i >= 0; $i--) {

    $tanggal = date(
        'Y-m-d',
        strtotime("-$i days")
    );

    $label_hari[] = date(
        'd/m',
        strtotime($tanggal)
    );

    $query = mysqli_query(
        $koneksi,
        "SELECT COALESCE(SUM(total),0) AS total
         FROM pesanan
         WHERE DATE(tanggal)='$tanggal'
         AND status='Selesai'"
    );

    $hasil = mysqli_fetch_assoc($query);

    $data_penjualan[] = (int)$hasil['total'];
}


/* =====================================================
   DATA STATUS PESANAN
===================================================== */

$status_list = [
    'Menunggu',
    'Diproses',
    'Dikirim',
    'Selesai',
    'Dibatalkan'
];

$data_status = [];

foreach ($status_list as $status) {

    $status_db = mysqli_real_escape_string(
        $koneksi,
        $status
    );

    $query = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total
         FROM pesanan
         WHERE status='$status_db'"
    );

    $hasil = mysqli_fetch_assoc($query);

    $data_status[] = (int)$hasil['total'];
}


/* =====================================================
   PRODUK TERLARIS
===================================================== */

$query_terlaris = mysqli_query(
    $koneksi,
    "SELECT
        nama_produk,
        SUM(jumlah) AS total_terjual
     FROM detail_pesanan
     GROUP BY nama_produk
     ORDER BY total_terjual DESC
     LIMIT 5"
);


/* =====================================================
   PESANAN TERBARU
===================================================== */

$pesanan_terbaru = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pesanan
     ORDER BY id DESC
     LIMIT 6"
);


/* =====================================================
   PRODUK TERBARU
===================================================== */

$produk_terbaru = mysqli_query(
    $koneksi,
    "SELECT *
     FROM produk
     ORDER BY id DESC
     LIMIT 5"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin - FASHIONKU</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* =====================================================
   RESET
===================================================== */

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f3f4f6;
    color: #111827;
}


/* =====================================================
   SIDEBAR
===================================================== */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;

    width: 250px;
    height: 100vh;

    background: #111827;
    color: white;

    padding: 25px 18px;

    z-index: 1000;
}

.logo {
    font-size: 23px;
    font-weight: bold;

    padding: 0 12px;
    margin-bottom: 35px;
}

.logo span {
    color: #fbbf24;
}


.menu-title {
    color: #9ca3af;
    font-size: 12px;
    margin: 20px 12px 8px;
    text-transform: uppercase;
}


.sidebar a {
    display: block;

    color: #d1d5db;
    text-decoration: none;

    padding: 12px 14px;

    border-radius: 9px;

    margin-bottom: 5px;

    transition: .2s;
}


.sidebar a:hover {
    background: #1f2937;
    color: white;
}


.sidebar a.active {
    background: #374151;
    color: white;
}


/* =====================================================
   MAIN
===================================================== */

.main {
    margin-left: 250px;
    padding: 25px 30px;
}


/* =====================================================
   TOPBAR
===================================================== */

.topbar {
    background: white;

    padding: 18px 22px;

    border-radius: 14px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 25px;

    box-shadow: 0 4px 15px rgba(0,0,0,.05);
}


.topbar h1 {
    font-size: 24px;
}


.admin-name {
    color: #6b7280;
    font-size: 14px;
}


/* =====================================================
   STAT CARD
===================================================== */

.stats {
    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 18px;

    margin-bottom: 25px;
}


.stat-card {
    background: white;

    padding: 22px;

    border-radius: 15px;

    box-shadow:
        0 4px 15px rgba(0,0,0,.05);

    border: 1px solid #e5e7eb;
}


.stat-card .icon {
    font-size: 27px;
    margin-bottom: 12px;
}


.stat-card h3 {
    color: #6b7280;
    font-size: 13px;
    font-weight: normal;
}


.stat-card .angka {
    font-size: 25px;
    font-weight: bold;
    margin-top: 5px;
}


/* =====================================================
   GRAFIK
===================================================== */

.grafik-grid {
    display: grid;

    grid-template-columns:
        2fr 1fr;

    gap: 20px;

    margin-bottom: 25px;
}


.grafik-card {
    background: white;

    padding: 22px;

    border-radius: 15px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 4px 15px rgba(0,0,0,.05);
}


.grafik-card h2 {
    font-size: 18px;
    margin-bottom: 5px;
}


.grafik-card p {
    color: #6b7280;
    font-size: 13px;
    margin-bottom: 18px;
}


.chart-box {
    position: relative;
    height: 300px;
}


/* =====================================================
   BAWAH
===================================================== */

.bottom-grid {
    display: grid;

    grid-template-columns:
        2fr 1fr;

    gap: 20px;
}


.card {
    background: white;

    padding: 22px;

    border-radius: 15px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 4px 15px rgba(0,0,0,.05);

    margin-bottom: 20px;
}


.card h2 {
    font-size: 18px;
    margin-bottom: 18px;
}


/* =====================================================
   TABLE
===================================================== */

.table-wrapper {
    overflow-x: auto;
}


table {
    width: 100%;
    border-collapse: collapse;
}


th {
    text-align: left;

    background: #111827;

    color: white;

    padding: 12px;

    font-size: 13px;
}


td {
    padding: 12px;

    border-bottom:
        1px solid #e5e7eb;

    font-size: 13px;
}


tr:hover td {
    background: #f9fafb;
}


/* =====================================================
   STATUS
===================================================== */

.status {
    display: inline-block;

    padding: 5px 10px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: bold;
}


.status-menunggu {
    background: #fef3c7;
    color: #92400e;
}


.status-diproses {
    background: #dbeafe;
    color: #1e40af;
}


.status-dikirim {
    background: #e0e7ff;
    color: #3730a3;
}


.status-selesai {
    background: #dcfce7;
    color: #166534;
}


.status-dibatalkan {
    background: #fee2e2;
    color: #991b1b;
}


/* =====================================================
   PRODUK TERLARIS
===================================================== */

.terlaris-item {
    display: flex;

    justify-content: space-between;
    align-items: center;

    padding: 13px 0;

    border-bottom:
        1px solid #e5e7eb;
}


.terlaris-item:last-child {
    border-bottom: none;
}


.nama-produk {
    font-weight: bold;

    font-size: 14px;
}


.jumlah-terjual {
    background: #111827;

    color: white;

    padding: 5px 9px;

    border-radius: 20px;

    font-size: 11px;
}


/* =====================================================
   QUICK ACTION
===================================================== */

.quick-actions {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 10px;
}


.quick-actions a {
    text-decoration: none;

    background: #f9fafb;

    color: #111827;

    padding: 13px;

    border-radius: 9px;

    font-size: 13px;

    text-align: center;

    border: 1px solid #e5e7eb;

    transition: .2s;
}


.quick-actions a:hover {
    background: #111827;
    color: white;
}


/* =====================================================
   MOBILE MENU BUTTON
===================================================== */

.mobile-menu {
    display: none;

    border: none;

    background: #111827;

    color: white;

    font-size: 20px;

    width: 40px;
    height: 40px;

    border-radius: 8px;

    cursor: pointer;
}


/* =====================================================
   OVERLAY
===================================================== */

.overlay {
    display: none;

    position: fixed;

    inset: 0;

    background: rgba(0,0,0,.45);

    z-index: 999;
}


/* =====================================================
   RESPONSIVE TABLET
===================================================== */

@media (max-width: 1000px) {

    .stats {
        grid-template-columns:
            repeat(2, 1fr);
    }


    .grafik-grid {
        grid-template-columns: 1fr;
    }


    .bottom-grid {
        grid-template-columns: 1fr;
    }

}


/* =====================================================
   RESPONSIVE HP
===================================================== */

@media (max-width: 700px) {

    .sidebar {
        transform: translateX(-100%);

        transition: .3s;

        width: 250px;
    }


    .sidebar.open {
        transform: translateX(0);
    }


    .overlay.open {
        display: block;
    }


    .main {
        margin-left: 0;

        padding: 15px;
    }


    .topbar {
        padding: 15px;

        margin-bottom: 18px;
    }


    .topbar h1 {
        font-size: 19px;
    }


    .admin-name {
        display: none;
    }


    .mobile-menu {
        display: block;
    }


    .stats {
        grid-template-columns:
            repeat(2, 1fr);

        gap: 10px;
    }


    .stat-card {
        padding: 16px;
    }


    .stat-card .icon {
        font-size: 22px;
    }


    .stat-card .angka {
        font-size: 19px;
    }


    .stat-card h3 {
        font-size: 11px;
    }


    .grafik-card,
    .card {
        padding: 16px;
    }


    .grafik-card h2,
    .card h2 {
        font-size: 16px;
    }


    .chart-box {
        height: 250px;
    }


    .quick-actions {
        grid-template-columns: 1fr 1fr;
    }


    td,
    th {
        white-space: nowrap;
    }

}


/* =====================================================
   HP KECIL
===================================================== */

@media (max-width: 430px) {

    .stats {
        grid-template-columns: 1fr 1fr;
    }


    .stat-card .angka {
        font-size: 17px;
    }


    .chart-box {
        height: 220px;
    }


    .quick-actions {
        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar" id="sidebar">

    <div class="logo">
        FASHION<span>KU</span>
    </div>


    <div class="menu-title">
        Menu
    </div>


    <a href="index.php" class="active">
        🏠 Dashboard
    </a>


    <a href="pesanan.php">
        📦 Pesanan
    </a>


    <div class="menu-title">
        Produk
    </div>


    <a href="tambah.php">
        ➕ Tambah Produk
    </a>


    <a href="../index.php">
        🛍️ Lihat Toko
    </a>


    <div class="menu-title">
        Akun
    </div>


    <a href="logout.php">
        🚪 Logout
    </a>

</div>


<div class="overlay"
     id="overlay"
     onclick="tutupMenu()">
</div>


<!-- =====================================================
     MAIN
===================================================== -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div style="display:flex;align-items:center;gap:12px;">

            <button
                class="mobile-menu"
                onclick="bukaMenu()"
            >
                ☰
            </button>


            <h1>
                Dashboard
            </h1>

        </div>


        <div class="admin-name">
            👤 Administrator
        </div>

    </div>


    <!-- =================================================
         STATISTIK
    ================================================= -->

    <div class="stats">


        <div class="stat-card">

            <div class="icon">
                🛍️
            </div>

            <h3>
                Total Produk
            </h3>

            <div class="angka">
                <?php echo $total_produk; ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="icon">
                📦
            </div>

            <h3>
                Total Pesanan
            </h3>

            <div class="angka">
                <?php echo $total_pesanan; ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="icon">
                ⏳
            </div>

            <h3>
                Menunggu
            </h3>

            <div class="angka">
                <?php echo $total_menunggu; ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="icon">
                💰
            </div>

            <h3>
                Pendapatan
            </h3>

            <div class="angka"
                 style="font-size:18px;">

                Rp <?php
                echo number_format(
                    $total_pendapatan,
                    0,
                    ',',
                    '.'
                );
                ?>

            </div>

        </div>

    </div>


    <!-- =================================================
         GRAFIK
    ================================================= -->

    <div class="grafik-grid">


        <!-- GRAFIK PENJUALAN -->

        <div class="grafik-card">

            <h2>
                📈 Penjualan
            </h2>

            <p>
                Pendapatan 7 hari terakhir
            </p>

            <div class="chart-box">

                <canvas id="grafikPenjualan"></canvas>

            </div>

        </div>


        <!-- GRAFIK STATUS -->

        <div class="grafik-card">

            <h2>
                📊 Status Pesanan
            </h2>

            <p>
                Jumlah pesanan berdasarkan status
            </p>

            <div class="chart-box">

                <canvas id="grafikStatus"></canvas>

            </div>

        </div>

    </div>


    <!-- =================================================
         BOTTOM
    ================================================= -->

    <div class="bottom-grid">


        <!-- PESANAN TERBARU -->

        <div class="card">

            <h2>
                📦 Pesanan Terbaru
            </h2>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Pembeli
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php while ($p = mysqli_fetch_assoc($pesanan_terbaru)): ?>

                        <?php

                        $status_class =
                            strtolower(
                                $p['status']
                            );

                        ?>

                        <tr>

                            <td>
                                #<?php echo $p['id']; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $p['nama_pembeli']
                                );
                                ?>
                            </td>

                            <td>
                                Rp <?php
                                echo number_format(
                                    $p['total'],
                                    0,
                                    ',',
                                    '.'
                                );
                                ?>
                            </td>

                            <td>

                                <span
                                    class="status status-<?php echo $status_class; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $p['status']
                                    );
                                    ?>

                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- PRODUK TERLARIS -->

        <div class="card">

            <h2>
                🏆 Produk Terlaris
            </h2>


            <?php if (mysqli_num_rows($query_terlaris) > 0): ?>


                <?php while ($t = mysqli_fetch_assoc($query_terlaris)): ?>

                    <div class="terlaris-item">

                        <div class="nama-produk">

                            <?php
                            echo htmlspecialchars(
                                $t['nama_produk']
                            );
                            ?>

                        </div>


                        <div class="jumlah-terjual">

                            <?php
                            echo $t['total_terjual'];
                            ?>
                            terjual

                        </div>

                    </div>

                <?php endwhile; ?>


            <?php else: ?>

                <p style="color:#6b7280;">
                    Belum ada data penjualan.
                </p>

            <?php endif; ?>

        </div>

    </div>


    <!-- =================================================
         QUICK ACTION
    ================================================= -->

    <div class="card">

        <h2>
            ⚡ Aksi Cepat
        </h2>


        <div class="quick-actions">

            <a href="tambah.php">
                ➕ Tambah Produk
            </a>


            <a href="pesanan.php">
                📦 Kelola Pesanan
            </a>


            <a href="../index.php">
                🛍️ Lihat Toko
            </a>


            <a href="logout.php">
                🚪 Logout
            </a>

        </div>

    </div>


</div>


<!-- =====================================================
     JAVASCRIPT MENU MOBILE
===================================================== -->

<script>

function bukaMenu() {

    document
        .getElementById("sidebar")
        .classList
        .add("open");

    document
        .getElementById("overlay")
        .classList
        .add("open");
}


function tutupMenu() {

    document
        .getElementById("sidebar")
        .classList
        .remove("open");

    document
        .getElementById("overlay")
        .classList
        .remove("open");
}


/* =====================================================
   GRAFIK PENJUALAN
===================================================== */

const labelHari = <?php
echo json_encode($label_hari);
?>;


const dataPenjualan = <?php
echo json_encode($data_penjualan);
?>;


new Chart(
    document.getElementById(
        'grafikPenjualan'
    ),
    {

        type: 'line',

        data: {

            labels: labelHari,

            datasets: [{

                label: 'Penjualan',

                data: dataPenjualan,

                tension: 0.4,

                fill: true,

                borderWidth: 3,

                pointRadius: 4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        callback: function(value) {

                            return 'Rp ' +
                                value.toLocaleString(
                                    'id-ID'
                                );

                        }

                    }

                }

            }

        }

    }
);


/* =====================================================
   GRAFIK STATUS
===================================================== */

const dataStatus = <?php
echo json_encode($data_status);
?>;


new Chart(
    document.getElementById(
        'grafikStatus'
    ),
    {

        type: 'doughnut',

        data: {

            labels: [
                'Menunggu',
                'Diproses',
                'Dikirim',
                'Selesai',
                'Dibatalkan'
            ],

            datasets: [{

                data: dataStatus,

                borderWidth: 2

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    position: 'bottom'

                }

            }

        }

    }
);

</script>


</body>

</html>