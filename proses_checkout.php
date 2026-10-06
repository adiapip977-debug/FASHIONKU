<?php

session_start();

include "koneksi.php";


// CUSTOMER WAJIB LOGIN
if (!isset($_SESSION['pelanggan_id'])) {

    header("Location: login.php");
    exit;

}


// CEK KERANJANG
if (
    !isset($_SESSION['keranjang']) ||
    count($_SESSION['keranjang']) == 0
) {

    header("Location: keranjang.php");
    exit;

}


// AMBIL DATA CHECKOUT
$nama = trim($_POST['nama']);
$no_hp = trim($_POST['no_hp']);
$alamat = trim($_POST['alamat']);
$metode_pembayaran = $_POST['metode_pembayaran'];

$pelanggan_id = $_SESSION['pelanggan_id'];


// HITUNG TOTAL
$total = 0;

foreach ($_SESSION['keranjang'] as $item) {

    $jumlah = isset($item['jumlah'])
        ? (int)$item['jumlah']
        : 1;

    $harga = (int)$item['harga'];

    $total += $harga * $jumlah;

}


// CEK METODE PEMBAYARAN
if (
    $metode_pembayaran != "COD" &&
    $metode_pembayaran != "Transfer Bank"
) {

    die("Metode pembayaran tidak valid.");

}


// MULAI TRANSAKSI
mysqli_begin_transaction($koneksi);

try {


    // CEK STOK SEMUA BARANG
    foreach ($_SESSION['keranjang'] as $item) {

        $nama_produk = $item['nama'];

        $jumlah = isset($item['jumlah'])
            ? (int)$item['jumlah']
            : 1;


        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT stok FROM produk WHERE nama = ? FOR UPDATE"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_produk
        );

        mysqli_stmt_execute($stmt);

        $hasil = mysqli_stmt_get_result($stmt);

        $produk = mysqli_fetch_assoc($hasil);

        mysqli_stmt_close($stmt);


        if (!$produk) {

            throw new Exception(
                "Produk tidak ditemukan: " . $nama_produk
            );

        }


        if ((int)$produk['stok'] < $jumlah) {

            throw new Exception(
                "Stok produk " . $nama_produk . " tidak mencukupi."
            );

        }

    }


    // INSERT PESANAN
    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO pesanan
        (
            pelanggan_id,
            nama_pembeli,
            no_hp,
            alamat,
            total,
            metode_pembayaran
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "isssis",
        $pelanggan_id,
        $nama,
        $no_hp,
        $alamat,
        $total,
        $metode_pembayaran
    );


    if (!mysqli_stmt_execute($stmt)) {

        throw new Exception(
            "Gagal membuat pesanan."
        );

    }


    $pesanan_id = mysqli_insert_id($koneksi);

    $pesan_notifikasi =
    "Pesanan #" . $pesanan_id .
    " berhasil dibuat dan sedang menunggu diproses.";


mysqli_query(
    $koneksi,
    "INSERT INTO riwayat_status
    (pesanan_id, status)
    VALUES
    ($pesanan_id, 'Menunggu')"
);


$stmt_notif = mysqli_prepare(
    $koneksi,
    "INSERT INTO notifikasi
    (pelanggan_id, pesanan_id, pesan)
    VALUES (?, ?, ?)"
);


mysqli_stmt_bind_param(
    $stmt_notif,
    "iis",
    $pelanggan_id,
    $pesanan_id,
    $pesan_notifikasi
);


mysqli_stmt_execute($stmt_notif);

mysqli_stmt_close($stmt_notif);

    mysqli_stmt_close($stmt);


    // INSERT DETAIL + KURANGI STOK
    foreach ($_SESSION['keranjang'] as $item) {

        $nama_produk = $item['nama'];
        $harga = (int)$item['harga'];

        $ukuran = isset($item['ukuran'])
            ? $item['ukuran']
            : "";

        $warna = isset($item['warna'])
            ? $item['warna']
            : "";

        $jumlah = isset($item['jumlah'])
            ? (int)$item['jumlah']
            : 1;

        $subtotal = $harga * $jumlah;


        // DETAIL PESANAN
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO detail_pesanan
            (
                pesanan_id,
                nama_produk,
                harga,
                ukuran,
                warna,
                jumlah,
                subtotal
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "isissii",
            $pesanan_id,
            $nama_produk,
            $harga,
            $ukuran,
            $warna,
            $jumlah,
            $subtotal
        );


        if (!mysqli_stmt_execute($stmt)) {

            throw new Exception(
                "Gagal menyimpan detail pesanan."
            );

        }


        mysqli_stmt_close($stmt);


        // KURANGI STOK
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE produk
             SET stok = stok - ?
             WHERE nama = ?"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $jumlah,
            $nama_produk
        );


        if (!mysqli_stmt_execute($stmt)) {

            throw new Exception(
                "Gagal mengurangi stok."
            );

        }


        mysqli_stmt_close($stmt);

    }


    // SIMPAN SEMUA
    mysqli_commit($koneksi);


    // KOSONGKAN KERANJANG
    $_SESSION['keranjang'] = [];


    // ARAHKAN SESUAI PEMBAYARAN
    if ($metode_pembayaran == "Transfer Bank") {

        header(
            "Location: pembayaran.php?id=" . $pesanan_id
        );

    } else {

        header(
            "Location: berhasil.php?id=" . $pesanan_id
        );

    }

    exit;


} catch (Exception $e) {


    mysqli_rollback($koneksi);

    die(
        "Checkout gagal: " .
        htmlspecialchars($e->getMessage())
    );

}

?>