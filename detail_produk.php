<?php

session_start();

include "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk WHERE id = $id"
);

$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    die("Produk tidak ditemukan.");
}


/* ========================= */
/* CEK WISHLIST */
/* ========================= */

$is_wishlist = false;

if (isset($_SESSION['pelanggan_id'])) {

    $pelanggan_id = (int)$_SESSION['pelanggan_id'];

    $cek_wishlist = mysqli_query(
        $koneksi,
        "SELECT id
         FROM wishlist
         WHERE pelanggan_id = $pelanggan_id
         AND produk_id = $id"
    );

    if (mysqli_num_rows($cek_wishlist) > 0) {
        $is_wishlist = true;
    }

}


/* ========================= */
/* DATA ULASAN */
/* ========================= */

$ulasan_query = mysqli_query(
    $koneksi,
    "SELECT ulasan.*, pelanggan.nama
     FROM ulasan
     JOIN pelanggan
     ON ulasan.pelanggan_id = pelanggan.id
     WHERE ulasan.produk_id = $id
     ORDER BY ulasan.id DESC"
);

$jumlah_ulasan = mysqli_num_rows($ulasan_query);

$total_rating = 0;

$semua_ulasan = [];

while ($u = mysqli_fetch_assoc($ulasan_query)) {

    $semua_ulasan[] = $u;

    $total_rating += (int)$u['rating'];

}

if ($jumlah_ulasan > 0) {

    $rata_rating = $total_rating / $jumlah_ulasan;

} else {

    $rata_rating = 0;

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>
        <?php echo htmlspecialchars($produk['nama']); ?> - FASHIONKU
    </title>

    <link rel="stylesheet" href="style.css">

    <script src="script.js"></script>


    <style>

        .detail-produk {

            background: white;

            padding: 30px;

            border-radius: 15px;

            box-shadow:
                0 4px 15px rgba(0,0,0,0.08);

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 40px;

            margin-top: 30px;

        }


        .detail-gambar {

            width: 100%;

            max-width: 450px;

            height: 450px;

            object-fit: contain;

            display: block;

            margin: auto;

        }


        .detail-info h1 {

            margin-bottom: 15px;

        }


        .detail-harga {

            color: #e11d48;

            font-size: 28px;

            font-weight: bold;

            margin: 15px 0;

        }


        .detail-deskripsi {

            line-height: 1.6;

            margin-bottom: 20px;

        }


        .kembali {

            display: inline-block;

            margin-top: 20px;

            text-decoration: none;

            color: #111827;

            font-weight: bold;

        }


        .wishlist-btn {

            display: inline-block;

            padding: 10px 15px;

            border-radius: 8px;

            text-decoration: none;

            margin-top: 10px;

            background: #111827;

            color: white;

        }


        .rating-box {

            background: white;

            padding: 25px;

            border-radius: 15px;

            box-shadow:
                0 4px 15px rgba(0,0,0,0.08);

            margin-top: 25px;

        }


        .rating-rangkuman {

            font-size: 24px;

            font-weight: bold;

            margin-top: 10px;

        }


        .ulasan-item {

            background: #f5f5f5;

            padding: 15px;

            border-radius: 10px;

            margin-top: 15px;

        }


        .ulasan-nama {

            font-weight: bold;

            margin-bottom: 5px;

        }


        .ulasan-bintang {

            margin-bottom: 8px;

        }


        .ulasan-tanggal {

            color: #777;

            font-size: 13px;

        }


        .stok-habis {

            display: inline-block;

            margin-top: 15px;

            padding: 10px 15px;

            background: #eee;

            color: #777;

            border-radius: 8px;

            font-weight: bold;

        }


        @media (max-width: 700px) {

            .detail-produk {

                grid-template-columns: 1fr;

                padding: 20px;

            }


            .detail-gambar {

                height: 300px;

            }

        }

    </style>

</head>


<body>


<!-- ========================= -->
<!-- NAVBAR -->
<!-- ========================= -->

<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>

        <a href="index.php">
            Home
        </a>


        <a href="keranjang.php">

            🛒 Keranjang (

            <?php

            echo isset($_SESSION['keranjang'])
                ? count($_SESSION['keranjang'])
                : 0;

            ?>

            )

        </a>


        <?php if (isset($_SESSION['pelanggan_id'])) { ?>


            <a href="pesanan_saya.php">

                📦 Pesanan Saya

            </a>


            <a href="wishlist.php">

                ❤️ Wishlist

            </a>


            <a href="notifikasi.php">

                🔔 Notifikasi

            </a>


            <span style="margin-left:20px;">

                👤

                <?php

                echo htmlspecialchars(
                    $_SESSION['pelanggan_nama']
                );

                ?>

            </span>


            <a href="logout_customer.php">

                Logout

            </a>


        <?php } else { ?>


            <a href="login.php">

                🔐 Login

            </a>


            <a href="register.php">

                📝 Register

            </a>


        <?php } ?>

    </div>

</nav>



<!-- ========================= -->
<!-- DETAIL PRODUK -->
<!-- ========================= -->

<div class="container">


    <div class="detail-produk">


        <!-- GAMBAR -->

        <div>

            <?php if (!empty($produk['gambar'])) { ?>

                <img
                    src="images/<?php echo htmlspecialchars($produk['gambar']); ?>"
                    class="detail-gambar"
                >

            <?php } ?>

        </div>



        <!-- INFORMASI -->

        <div class="detail-info">


            <h1>

                <?php

                echo htmlspecialchars(
                    $produk['nama']
                );

                ?>

            </h1>



            <div class="detail-harga">

                Rp

                <?php

                echo number_format(
                    $produk['harga'],
                    0,
                    ',',
                    '.'
                );

                ?>

            </div>



            <!-- RATING SINGKAT -->

            <p>

                ⭐

                <?php

                echo number_format(
                    $rata_rating,
                    1
                );

                ?>

                / 5

                (<?php echo $jumlah_ulasan; ?> ulasan)

            </p>



            <p class="detail-deskripsi">

                <?php

                echo htmlspecialchars(
                    $produk['deskripsi']
                );

                ?>

            </p>



            <p>

                <b>Kategori:</b>

                <?php

                echo htmlspecialchars(
                    $produk['kategori']
                );

                ?>

            </p>



            <p>

                <b>Stok:</b>

                <?php echo $produk['stok']; ?>

            </p>



            <!-- WISHLIST -->

            <?php if (isset($_SESSION['pelanggan_id'])) { ?>


                <?php if ($is_wishlist) { ?>

                    <a
                        href="wishlist_toggle.php?produk_id=<?php echo $produk['id']; ?>&kembali=detail_produk.php?id=<?php echo $produk['id']; ?>"
                        class="wishlist-btn"
                    >

                        💔 Hapus dari Wishlist

                    </a>


                <?php } else { ?>


                    <a
                        href="wishlist_toggle.php?produk_id=<?php echo $produk['id']; ?>&kembali=detail_produk.php?id=<?php echo $produk['id']; ?>"
                        class="wishlist-btn"
                    >

                        ❤️ Tambah ke Wishlist

                    </a>


                <?php } ?>


            <?php } else { ?>


                <p>

                    <a href="login.php">

                        Login

                    </a>

                    untuk menambahkan wishlist.

                </p>


            <?php } ?>



            <!-- PILIHAN -->

            <div class="pilihan">


                <!-- UKURAN -->

                <p>

                    <b>Ukuran:</b>

                </p>


                <div class="ukuran">

                    <?php

                    $ukuran = explode(
                        ",",
                        $produk['ukuran']
                    );

                    foreach ($ukuran as $u) {

                        $u = trim($u);

                        if ($u == '') {
                            continue;
                        }

                    ?>


                        <button
                            type="button"
                            class="pilihan-btn ukuran-btn"

                            onclick="pilihUkuran(
                                this,
                                <?php echo $produk['id']; ?>,
                                '<?php echo htmlspecialchars($u, ENT_QUOTES); ?>'
                            )"
                        >

                            <?php

                            echo htmlspecialchars($u);

                            ?>

                        </button>


                    <?php } ?>

                </div>



                <!-- WARNA -->

                <p>

                    <b>Warna:</b>

                </p>


                <div class="warna">

                    <?php

                    $warna = explode(
                        ",",
                        $produk['warna']
                    );

                    foreach ($warna as $w) {

                        $w = trim($w);

                        if ($w == '') {
                            continue;
                        }

                    ?>


                        <button
                            type="button"
                            class="pilihan-btn warna-btn"

                            onclick="pilihWarna(
                                this,
                                <?php echo $produk['id']; ?>,
                                '<?php echo htmlspecialchars($w, ENT_QUOTES); ?>'
                            )"
                        >

                            <?php

                            echo htmlspecialchars($w);

                            ?>

                        </button>


                    <?php } ?>

                </div>


            </div>



            <!-- PILIHAN TERPILIH -->

            <p
                id="pilihan-<?php echo $produk['id']; ?>"
            >

                Pilih ukuran dan warna

            </p>



            <!-- KERANJANG -->

            <form
                action="tambah_keranjang.php"
                method="POST"

                onsubmit="return cekPilihan(
                    <?php echo $produk['id']; ?>
                )"
            >


                <input
                    type="hidden"
                    name="nama"
                    value="<?php echo htmlspecialchars($produk['nama']); ?>"
                >


                <input
                    type="hidden"
                    name="harga"
                    value="<?php echo $produk['harga']; ?>"
                >


                <input
                    type="hidden"
                    id="ukuran-<?php echo $produk['id']; ?>"
                    name="ukuran"
                    value=""
                >


                <input
                    type="hidden"
                    id="warna-<?php echo $produk['id']; ?>"
                    name="warna"
                    value=""
                >


                <?php if ($produk['stok'] > 0) { ?>


                    <button
                        type="submit"
                        class="btn"
                    >

                        🛒 Tambah ke Keranjang

                    </button>


                <?php } else { ?>


                    <button
                        type="button"
                        class="stok-habis"
                        disabled
                    >

                        ❌ Stok Habis

                    </button>


                <?php } ?>


            </form>



            <br>


            <a
                href="index.php"
                class="kembali"
            >

                ← Kembali ke Produk

            </a>


        </div>

    </div>



    <!-- ========================= -->
    <!-- ULASAN -->
    <!-- ========================= -->

    <div class="rating-box">


        <h2>

            ⭐ Ulasan Produk

        </h2>


        <div class="rating-rangkuman">

            ⭐

            <?php

            echo number_format(
                $rata_rating,
                1
            );

            ?>

            / 5

        </div>


        <p>

            Berdasarkan

            <?php echo $jumlah_ulasan; ?>

            ulasan pelanggan.

        </p>



        <?php if ($jumlah_ulasan == 0) { ?>


            <p style="margin-top:20px;">

                Belum ada ulasan untuk produk ini.

            </p>


        <?php } else { ?>


            <?php foreach ($semua_ulasan as $u) { ?>


                <div class="ulasan-item">


                    <div class="ulasan-nama">

                        👤

                        <?php

                        echo htmlspecialchars(
                            $u['nama']
                        );

                        ?>

                    </div>


                    <div class="ulasan-bintang">

                        <?php

                        echo str_repeat(
                            "⭐",
                            (int)$u['rating']
                        );

                        ?>

                    </div>


                    <p>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $u['komentar']
                            )
                        );

                        ?>

                    </p>


                    <div class="ulasan-tanggal">

                        <?php

                        echo $u['tanggal'];

                        ?>

                    </div>


                </div>


            <?php } ?>


        <?php } ?>


    </div>


</div>


</body>

</html>