<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include "../koneksi.php";


// ================================
// UPDATE STATUS PESANAN
// ================================

if (isset($_POST['update_status'])) {

    $id = (int) $_POST['id'];
    $status = $_POST['status'];

    $daftar_status = [
        "Menunggu",
        "Diproses",
        "Dikirim",
        "Selesai",
        "Dibatalkan"
    ];

    if (in_array($status, $daftar_status)) {

        // UPDATE STATUS
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE pesanan SET status = ? WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $status,
            $id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);


        // ================================
        // SIMPAN RIWAYAT STATUS
        // ================================

        mysqli_query(
            $koneksi,
            "INSERT INTO riwayat_status
            (pesanan_id, status)
            VALUES
            ($id, '$status')"
        );


        // ================================
        // AMBIL PEMILIK PESANAN
        // ================================

        $hasil_pesanan = mysqli_query(
            $koneksi,
            "SELECT pelanggan_id
             FROM pesanan
             WHERE id = $id"
        );

        $data_pesanan = mysqli_fetch_assoc($hasil_pesanan);


        // ================================
        // BUAT NOTIFIKASI
        // ================================

        if (
            $data_pesanan &&
            !empty($data_pesanan['pelanggan_id'])
        ) {

            $pelanggan_id_notif =
                (int) $data_pesanan['pelanggan_id'];


            $pesan_notif =
                "Pesanan #" . $id .
                " sekarang berstatus: " .
                $status;


            $stmt_notif = mysqli_prepare(
                $koneksi,
                "INSERT INTO notifikasi
                (pelanggan_id, pesanan_id, pesan)
                VALUES (?, ?, ?)"
            );


            if ($stmt_notif) {

                mysqli_stmt_bind_param(
                    $stmt_notif,
                    "iis",
                    $pelanggan_id_notif,
                    $id,
                    $pesan_notif
                );

                mysqli_stmt_execute($stmt_notif);

                mysqli_stmt_close($stmt_notif);
            }
        }
    }


    // KEMBALI KE HALAMAN PESANAN
    header("Location: pesanan.php");
    exit;
}


// ================================
// AMBIL SEMUA PESANAN
// ================================

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pesanan ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Pesanan FASHIONKU</title>

    <link rel="stylesheet" href="../style.css">

    <style>

        .tabel-pesanan {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .tabel-pesanan th,
        .tabel-pesanan td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        .tabel-pesanan th {
            background: #111827;
            color: white;
        }

        .status-form {
            padding: 0;
            margin: 0;
            background: transparent;
        }

        .status-form select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .status-form button {
            padding: 8px 12px;
            background: #111827;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .status-form button:hover {
            opacity: 0.85;
        }

        .detail-btn {
            display: inline-block;
            padding: 8px 12px;
            background: #111827;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .detail-btn:hover {
            opacity: 0.85;
        }

        .bukti-ada {
            color: green;
            font-weight: bold;
        }

        .bukti-belum {
            color: #e11d48;
            font-weight: bold;
        }

        .tabel-wrapper {
            overflow-x: auto;
        }

    </style>

</head>


<body>


<nav class="navbar">

    <h2>📦 PESANAN FASHIONKU</h2>

    <div>

        <a href="index.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>


<div class="container">

    <h1>Daftar Pesanan</h1>

    <p>
        Kelola pesanan yang masuk ke toko kamu.
    </p>

    <br>


    <div class="tabel-wrapper">

        <table class="tabel-pesanan">

            <tr>

                <th>ID</th>

                <th>Pembeli</th>

                <th>No. HP</th>

                <th>Total</th>

                <th>Pembayaran</th>

                <th>Bukti</th>

                <th>Tanggal</th>

                <th>Status</th>

                <th>Detail</th>

            </tr>


            <?php while ($p = mysqli_fetch_assoc($query)) { ?>

                <tr>

                    <td>
                        <?php echo $p['id']; ?>
                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $p['nama_pembeli']
                        );
                        ?>

                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $p['no_hp']
                        );
                        ?>

                    </td>


                    <td>

                        Rp

                        <?php
                        echo number_format(
                            $p['total'],
                            0,
                            ',',
                            '.'
                        );
                        ?>

                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $p['metode_pembayaran']
                        );
                        ?>

                    </td>


                    <td>

                        <?php if (!empty($p['bukti_pembayaran'])) { ?>

                            <span class="bukti-ada">
                                ✅ Ada
                            </span>

                        <?php } else { ?>

                            <span class="bukti-belum">
                                ❌ Belum
                            </span>

                        <?php } ?>

                    </td>


                    <td>

                        <?php echo $p['tanggal']; ?>

                    </td>


                    <td>

                        <form
                            method="POST"
                            class="status-form"
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?php echo $p['id']; ?>"
                            >


                            <select name="status">

                                <?php

                                $status_list = [
                                    "Menunggu",
                                    "Diproses",
                                    "Dikirim",
                                    "Selesai",
                                    "Dibatalkan"
                                ];

                                foreach (
                                    $status_list
                                    as $s
                                ) {

                                ?>

                                    <option
                                        value="<?php echo $s; ?>"
                                        <?php
                                        echo (
                                            $p['status'] == $s
                                        )
                                            ? 'selected'
                                            : '';
                                        ?>
                                    >

                                        <?php echo $s; ?>

                                    </option>

                                <?php } ?>

                            </select>


                            <br><br>


                            <button
                                type="submit"
                                name="update_status"
                            >
                                Simpan
                            </button>

                        </form>

                    </td>


                    <td>

                        <a
                            href="detail_pesanan.php?id=<?php echo $p['id']; ?>"
                            class="detail-btn"
                        >
                            Lihat Detail
                        </a>

                    </td>

                </tr>

            <?php } ?>

        </table>

    </div>

</div>


</body>

</html>