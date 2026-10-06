<?php 

session_start(); 

/* =========================
   WAJIB LOGIN
========================= */

if (!isset($_SESSION['pelanggan_id'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php"; 


$cari = isset($_GET['cari']) ? $_GET['cari'] : ''; 
$harga_min = isset($_GET['harga_min']) ? $_GET['harga_min'] : ''; 
$harga_max = isset($_GET['harga_max']) ? $_GET['harga_max'] : ''; 
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : ''; 


$sql = "SELECT * FROM produk WHERE 1=1"; 


if ($cari != '') { 

    $cari_aman = mysqli_real_escape_string(
        $koneksi, 
        $cari
    ); 

    $sql .= " AND (
        nama LIKE '%$cari_aman%' 
        OR deskripsi LIKE '%$cari_aman%'
    )"; 

} 


if ($harga_min != '' && is_numeric($harga_min)) { 

    $sql .= " AND harga >= " . (int)$harga_min; 

} 


if ($harga_max != '' && is_numeric($harga_max)) { 

    $sql .= " AND harga <= " . (int)$harga_max; 

} 


if ($kategori != '') { 

    $kategori_aman = mysqli_real_escape_string(
        $koneksi, 
        $kategori
    ); 

    $sql .= " AND kategori = '$kategori_aman'"; 

} 


$sql .= " ORDER BY id DESC"; 

$query = mysqli_query($koneksi, $sql); 



/* ========================= 
   PRODUK TERLARIS 
========================= */ 

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

?> 


<!DOCTYPE html> 

<html> 

<head> 

    <title>FASHIONKU - Toko Fashion Online</title> 

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css"> 

    <script src="script.js"></script> 


    <style> 


        /* ================================
           HOMEPAGE
        ================================= */

        .hero { 

            margin-top: 25px; 

            border-radius: 22px; 

            padding: 55px; 

            min-height: 300px; 

            background: 
                linear-gradient( 
                    135deg, 
                    rgba(17,24,39,.96), 
                    rgba(55,65,81,.92) 
                ); 

            color: white; 

            display: flex; 

            align-items: center; 

            justify-content: space-between; 

            overflow: hidden; 

            position: relative; 

            box-shadow: 0 12px 35px rgba(0,0,0,.15); 

        } 


        .hero::after { 

            content: "FASHION"; 

            position: absolute; 

            right: -20px; 

            bottom: -45px; 

            font-size: 130px; 

            font-weight: bold; 

            opacity: .05; 

        } 


        .hero-text { 

            max-width: 650px; 

            position: relative; 

            z-index: 2; 

        } 


        .hero-text small { 

            display: inline-block; 

            background: #e11d48; 

            padding: 7px 13px; 

            border-radius: 20px; 

            font-weight: bold; 

            margin-bottom: 15px; 

        } 


        .hero-text h1 { 

            color: white; 

            font-size: 48px; 

            line-height: 1.1; 

            margin-bottom: 15px; 

        } 


        .hero-text p { 

            color: #d1d5db; 

            font-size: 17px; 

            margin-bottom: 25px; 

        } 


        .hero-btn { 

            display: inline-block; 

            background: white; 

            color: #111827; 

            padding: 13px 22px; 

            border-radius: 10px; 

            text-decoration: none; 

            font-weight: bold; 

            transition: .2s; 

        } 


        .hero-btn:hover { 

            transform: translateY(-3px); 

            background: #f3f4f6; 

        } 



        /* ================================
           SECTION
        ================================= */

        .section-title { 

            display: flex; 

            justify-content: space-between; 

            align-items: end; 

            margin-top: 45px; 

            margin-bottom: 20px; 

        } 


        .section-title h2 { 

            font-size: 27px; 

            color: #111827; 

        } 


        .section-title p { 

            color: #6b7280; 

        } 



        /* ================================
           KATEGORI
        ================================= */

        .kategori-container { 

            display: grid; 

            grid-template-columns: 
                repeat(auto-fit, minmax(160px, 1fr)); 

            gap: 15px; 

        } 


        .kategori-card { 

            background: white; 

            border-radius: 15px; 

            padding: 25px 15px; 

            text-align: center; 

            text-decoration: none; 

            color: #111827; 

            border: 1px solid #e5e7eb; 

            box-shadow: 0 5px 15px rgba(0,0,0,.05); 

            transition: .25s; 

        } 


        .kategori-card:hover { 

            transform: translateY(-5px); 

            background: #111827; 

            color: white; 

            box-shadow: 0 10px 25px rgba(0,0,0,.15); 

        } 


        .kategori-icon { 

            font-size: 35px; 

            margin-bottom: 8px; 

        } 


        .kategori-card h3 { 

            font-size: 16px; 

        } 



        /* ================================
           FILTER
        ================================= */

        .filter-box { 

            background: white; 

            padding: 20px; 

            border-radius: 16px; 

            border: 1px solid #e5e7eb; 

            box-shadow: 0 5px 15px rgba(0,0,0,.05); 

            margin-top: 25px; 

        } 


        .filter-box form { 

            box-shadow: none; 

            border: none; 

            padding: 0; 

            margin: 0; 

        } 



        /* ================================
           PRODUCT CARD
        ================================= */

        .produk-container { 

            margin-top: 10px; 

        } 


        .produk { 

            position: relative; 

        } 


        .produk-label { 

            position: absolute; 

            top: 12px; 

            left: 12px; 

            background: #e11d48; 

            color: white; 

            padding: 5px 9px; 

            border-radius: 7px; 

            font-size: 11px; 

            font-weight: bold; 

            z-index: 5; 

        } 


        .produk-info { 

            margin-top: 10px; 

        } 


        .kategori-mini { 

            font-size: 12px; 

            color: #6b7280; 

            margin-bottom: 5px; 

        } 


        .stok-aman { 

            color: #16a34a !important; 

            font-weight: bold; 

        } 


        .stok-habis { 

            color: #dc2626 !important; 

            font-weight: bold; 

        } 



        /* ================================
           PRODUK TERLARIS
        ================================= */

        .terlaris-section {

            margin-top: 45px;

        }


        .terlaris-header {

            display: flex;

            justify-content: space-between;

            align-items: end;

            margin-bottom: 20px;

        }


        .terlaris-header h2 {

            font-size: 27px;

            color: #111827;

            margin: 0;

        }


        .terlaris-header p {

            color: #6b7280;

            margin: 5px 0 0;

        }


        .terlaris-list {

            display: grid;

            grid-template-columns: repeat(5, 1fr);

            gap: 15px;

        }


        .terlaris-card {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 20px;

            text-align: center;

            box-shadow: 0 5px 15px rgba(0,0,0,.05);

            transition: .25s;

            position: relative;

        }


        .terlaris-card:hover {

            transform: translateY(-5px);

            box-shadow: 0 10px 25px rgba(0,0,0,.10);

        }


        .terlaris-rank {

            font-size: 30px;

            margin-bottom: 10px;

        }


        .terlaris-card h3 {

            font-size: 16px;

            color: #111827;

            margin: 5px 0 10px;

        }


        .terjual {

            display: inline-block;

            background: #f3f4f6;

            color: #374151;

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;

        }


        .terlaris-kosong {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 25px;

            text-align: center;

            color: #6b7280;

        }



        /* ================================
           FOOTER
        ================================= */

        .footer { 

            margin-top: 70px; 

            background: #111827; 

            color: white; 

            padding: 45px 5%; 

        } 


        .footer-container { 

            max-width: 1200px; 

            margin: auto; 

            display: grid; 

            grid-template-columns: 
                2fr 1fr 1fr; 

            gap: 40px; 

        } 


        .footer h2 { 

            margin-bottom: 12px; 

        } 


        .footer h3 { 

            margin-bottom: 12px; 

        } 


        .footer p, 

        .footer a { 

            color: #9ca3af; 

            font-size: 14px; 

        } 


        .footer a { 

            display: block; 

            text-decoration: none; 

            margin-bottom: 7px; 

        } 


        .footer a:hover { 

            color: white; 

        } 


        .footer-bottom { 

            max-width: 1200px; 

            margin: 35px auto 0; 

            padding-top: 20px; 

            border-top: 1px solid #374151; 

            text-align: center; 

            color: #9ca3af; 

            font-size: 13px; 

        } 



        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 1000px) {

            .terlaris-list {

                grid-template-columns: repeat(3, 1fr);

            }

        }


        @media (max-width: 768px) { 

            .hero { 

                padding: 35px 25px; 

                min-height: 280px; 

            } 


            .hero-text h1 { 

                font-size: 35px; 

            } 


            .footer-container { 

                grid-template-columns: 1fr; 

            }


            .terlaris-list {

                grid-template-columns: repeat(2, 1fr);

            }


            .terlaris-header {

                display: block;

            }

        }


        @media (max-width: 480px) {

            .terlaris-list {

                grid-template-columns: 1fr;

            }

        }


        /* ================================
           MOBILE HOMEPAGE TAMBAHAN
        ================================= */

        @media (max-width: 600px) {

            .hero {

                margin-top: 15px;

                padding: 30px 20px;

                min-height: 260px;

                border-radius: 18px;

            }


            .hero-text {

                max-width: 100%;

            }


            .hero-text h1 {

                font-size: 32px;

            }


            .hero-text p {

                font-size: 14px;

                line-height: 1.6;

            }


            .hero::after {

                font-size: 70px;

                bottom: -20px;

            }


            .kategori-container {

                grid-template-columns: repeat(2, 1fr);

                gap: 10px;

            }


            .kategori-card {

                padding: 18px 10px;

            }


            .kategori-icon {

                font-size: 28px;

            }


            .section-title {

                margin-top: 30px;

            }


            .section-title h2,

            .terlaris-header h2 {

                font-size: 22px;

            }


            .section-title p,

            .terlaris-header p {

                font-size: 13px;

            }


            .filter-box {

                padding: 15px;

            }


            .filter-box form {

                display: flex;

                flex-direction: column;

                gap: 10px;

            }


            .filter-box input,

            .filter-box select,

            .filter-box button,

            .filter-box a {

                width: 100% !important;

                min-height: 45px;

            }


            .terlaris-list {

                grid-template-columns: repeat(2, 1fr);

                gap: 10px;

            }


            .terlaris-card {

                padding: 15px 10px;

            }


            .terlaris-rank {

                font-size: 25px;

            }


            .terlaris-card h3 {

                font-size: 14px;

                word-break: break-word;

            }


            .produk-container {

                display: grid;

                grid-template-columns: repeat(2, minmax(0, 1fr));

                gap: 10px;

            }


            .produk {

                min-width: 0;

                overflow: hidden;

            }


            .produk-label {

                top: 8px;

                left: 8px;

                font-size: 9px;

                padding: 4px 6px;

            }


            .produk-info {

                padding: 0 5px 10px;

            }


            .produk-info h3 {

                font-size: 14px;

                line-height: 1.3;

            }


            .produk-info p {

                font-size: 12px;

                line-height: 1.5;

            }


            .kategori-mini {

                font-size: 10px;

            }


            .harga {

                font-size: 16px !important;

            }


            .pilihan-btn {

                font-size: 11px !important;

                padding: 6px 8px !important;

            }


            .produk-info .btn {

                width: 100%;

                font-size: 12px;

                padding: 10px 6px;

            }


            .footer {

                margin-top: 45px;

                padding: 35px 20px;

            }


            .footer-container {

                gap: 25px;

            }

        }

    </style> 

</head> 


<body> 


<!-- ================================
     NAVBAR
================================= --> 

<nav class="navbar"> 

    <h2>👕 FASHIONKU</h2> 

    <div> 

        <a href="index.php"> 
            🏠 Home 
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
                📦 Pesanan 
            </a> 

            <a href="wishlist.php"> 
                ❤️ Wishlist 
            </a> 

            <a href="notifikasi.php"> 
                🔔 Notifikasi 
            </a> 

            <span style="margin-left:10px;"> 
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



<div class="container"> 


<!-- ================================
     HERO
================================= --> 

<section class="hero"> 

    <div class="hero-text"> 

        <small>🔥 NEW COLLECTION</small> 

        <h1> 

            Style Kamu,<br> 

            Cara Kamu. 

        </h1> 

        <p> 

            Temukan koleksi fashion terbaru 

            dengan gaya yang cocok buat kamu. 

        </p> 

        <a 
            href="#produk" 
            class="hero-btn" 
        > 

            🛍️ Belanja Sekarang 

        </a> 

    </div> 

</section> 



<!-- ================================
     KATEGORI
================================= --> 

<div class="section-title"> 

    <div> 

        <h2> 

            Jelajahi Kategori 

        </h2> 

        <p> 

            Pilih style favorit kamu 

        </p> 

    </div> 

</div> 


<div class="kategori-container"> 


    <a 
        href="index.php?kategori=Kaos" 
        class="kategori-card" 
    > 

        <div class="kategori-icon"> 
            👕 
        </div> 

        <h3> 
            Kaos 
        </h3> 

    </a> 


    <a 
        href="index.php?kategori=Hoodie" 
        class="kategori-card" 
    > 

        <div class="kategori-icon"> 
            🧥 
        </div> 

        <h3> 
            Hoodie 
        </h3> 

    </a> 


    <a 
        href="index.php?kategori=Kemeja" 
        class="kategori-card" 
    > 

        <div class="kategori-icon"> 
            👔 
        </div> 

        <h3> 
            Kemeja 
        </h3> 

    </a> 


    <a 
        href="index.php?kategori=Celana" 
        class="kategori-card" 
    > 

        <div class="kategori-icon"> 
            👖 
        </div> 

        <h3> 
            Celana 
        </h3> 

    </a> 


    <a 
        href="index.php?kategori=Lainnya" 
        class="kategori-card" 
    > 

        <div class="kategori-icon"> 
            🛍️ 
        </div> 

        <h3> 
            Lainnya 
        </h3> 

    </a> 

</div> 



<!-- ================================
     PRODUK TERLARIS
================================= -->

<section class="terlaris-section">

    <div class="terlaris-header">

        <div>

            <h2>
                🔥 Produk Terlaris
            </h2>

            <p>
                Produk yang paling banyak dibeli pelanggan
            </p>

        </div>

    </div>


    <?php if (mysqli_num_rows($query_terlaris) > 0) { ?>

        <div class="terlaris-list">

            <?php

            $ranking = 1;

            while ($terlaris = mysqli_fetch_assoc($query_terlaris)) {

                if ($ranking == 1) {

                    $icon = "🥇";

                } elseif ($ranking == 2) {

                    $icon = "🥈";

                } elseif ($ranking == 3) {

                    $icon = "🥉";

                } else {

                    $icon = "🏅";

                }

            ?>

                <div class="terlaris-card">

                    <div class="terlaris-rank">

                        <?php echo $icon; ?>

                    </div>

                    <h3>

                        <?php

                        echo htmlspecialchars(

                            $terlaris['nama_produk']

                        );

                        ?>

                    </h3>

                    <span class="terjual">

                        🔥

                        <?php

                        echo $terlaris['total_terjual'];

                        ?>

                        terjual

                    </span>

                </div>

            <?php

                $ranking++;

            }

            ?>

        </div>

    <?php } else { ?>

        <div class="terlaris-kosong">

            📦 Belum ada data penjualan.

        </div>

    <?php } ?>

</section> 



<!-- ================================
     PRODUK SECTION
================================= --> 

<div 
    class="section-title" 
    id="produk" 
> 

    <div> 

        <h2> 

            🔥 Koleksi Terbaru 

        </h2> 

        <p> 

            Produk pilihan dari FASHIONKU 

        </p> 

    </div> 

</div> 



<!-- ================================
     FILTER
================================= --> 

<div class="filter-box"> 

    <form 
        method="GET" 
        class="filter-form" 
    > 

        <input 
            type="text" 
            name="cari" 
            placeholder="🔎 Cari produk..." 
            value="<?php 

                echo htmlspecialchars($cari); 

            ?>" 
        > 


        <select name="kategori"> 

            <option value=""> 
                Semua Kategori 
            </option> 


            <option 
                value="Kaos" 
                <?php 

                echo ($kategori == 'Kaos') 

                    ? 'selected' 

                    : ''; 

                ?> 
            > 

                👕 Kaos 

            </option> 


            <option 
                value="Hoodie" 
                <?php 

                echo ($kategori == 'Hoodie') 

                    ? 'selected' 

                    : ''; 

                ?> 
            > 

                🧥 Hoodie 

            </option> 


            <option 
                value="Kemeja" 
                <?php 

                echo ($kategori == 'Kemeja') 

                    ? 'selected' 

                    : ''; 

                ?> 
            > 

                👔 Kemeja 

            </option> 


            <option 
                value="Celana" 
                <?php 

                echo ($kategori == 'Celana') 

                    ? 'selected' 

                    : ''; 

                ?> 
            > 

                👖 Celana 

            </option> 


            <option 
                value="Lainnya" 
                <?php 

                echo ($kategori == 'Lainnya') 

                    ? 'selected' 

                    : ''; 

                ?> 
            > 

                🛍️ Lainnya 

            </option> 

        </select> 


        <input 
            type="number" 
            name="harga_min" 
            placeholder="Harga minimum" 
            value="<?php 

                echo htmlspecialchars($harga_min); 

            ?>" 
        > 


        <input 
            type="number" 
            name="harga_max" 
            placeholder="Harga maksimum" 
            value="<?php 

                echo htmlspecialchars($harga_max); 

            ?>" 
        > 


        <button 
            type="submit" 
            class="btn" 
        > 

            🔎 Cari 

        </button> 


        <a 
            href="index.php" 
            class="btn" 
        > 

            🔄 Reset 

        </a> 

    </form> 

</div> 



<!-- ================================
     PRODUCT
================================= --> 

<div class="produk-container"> 


<?php if (mysqli_num_rows($query) == 0) { ?> 

    <div class="card"> 

        <h3> 

            😕 Produk tidak ditemukan 

        </h3> 

        <p> 

            Coba gunakan kata pencarian atau 
            kategori lainnya. 

        </p> 

    </div> 

<?php } ?> 



<?php while ($produk = mysqli_fetch_assoc($query)) { ?> 


    <div class="produk"> 


        <?php if ($produk['stok'] > 0) { ?> 

            <div class="produk-label"> 

                🔥 TERSEDIA 

            </div> 

        <?php } else { ?> 

            <div class="produk-label"> 

                HABIS 

            </div> 

        <?php } ?> 



        <!-- GAMBAR --> 

        <?php if (!empty($produk['gambar'])) { ?> 

            <a 
                href="detail_produk.php?id=<?php 

                    echo $produk['id']; 

                ?>" 
            > 

                <img 
                    class="gambar-produk" 
                    src="images/<?php 

                        echo htmlspecialchars( 

                            $produk['gambar'] 

                        ); 

                    ?>" 

                    alt="<?php 

                        echo htmlspecialchars( 

                            $produk['nama'] 

                        ); 

                    ?>" 
                > 

            </a> 

        <?php } ?> 



        <div class="produk-info"> 


            <div class="kategori-mini"> 

                <?php 

                echo htmlspecialchars( 

                    $produk['kategori'] 

                ); 

                ?> 

            </div> 



            <h3> 

                <a 
                    href="detail_produk.php?id=<?php 

                        echo $produk['id']; 

                    ?>" 

                    style=" 
                        text-decoration:none; 
                        color:#111827; 
                    " 
                > 

                    <?php 

                    echo htmlspecialchars( 

                        $produk['nama'] 

                    ); 

                    ?> 

                </a> 

            </h3> 



            <div class="harga"> 

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



            <p> 

                <?php 

                echo htmlspecialchars( 

                    $produk['deskripsi'] 

                ); 

                ?> 

            </p> 



            <p> 

                <b>Stok:</b> 

                <?php if ($produk['stok'] > 0) { ?> 

                    <span class="stok-aman"> 

                        <?php 

                        echo $produk['stok']; 

                        ?> 

                        tersedia 

                    </span> 

                <?php } else { ?> 

                    <span class="stok-habis"> 

                        Stok habis 

                    </span> 

                <?php } ?> 

            </p> 



            <!-- UKURAN & WARNA --> 

            <div class="pilihan"> 


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
                                <?php 

                                echo $produk['id']; 

                                ?>, 

                                '<?php 

                                echo htmlspecialchars( 

                                    $u, 

                                    ENT_QUOTES 

                                ); 

                                ?>' 
                            )" 
                        > 

                            <?php 

                            echo htmlspecialchars($u); 

                            ?> 

                        </button> 

                    <?php } ?> 

                </div> 



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
                                <?php 

                                echo $produk['id']; 

                                ?>, 

                                '<?php 

                                echo htmlspecialchars( 

                                    $w, 

                                    ENT_QUOTES 

                                ); 

                                ?>' 
                            )" 
                        > 

                            <?php 

                            echo htmlspecialchars($w); 

                            ?> 

                        </button> 

                    <?php } ?> 

                </div> 


            </div> 



            <p 
                id="pilihan-<?php 

                    echo $produk['id']; 

                ?>" 
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

                    value="<?php 

                        echo htmlspecialchars( 

                            $produk['nama'] 

                        ); 

                    ?>" 
                > 



                <input 
                    type="hidden" 
                    name="harga" 

                    value="<?php 

                        echo $produk['harga']; 

                    ?>" 
                > 



                <input 
                    type="hidden" 

                    id="ukuran-<?php 

                        echo $produk['id']; 

                    ?>" 

                    name="ukuran" 

                    value="" 
                > 



                <input 
                    type="hidden" 

                    id="warna-<?php 

                        echo $produk['id']; 

                    ?>" 

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
                        class="btn" 
                        disabled 

                        style=" 
                            opacity:.5; 
                            cursor:not-allowed; 
                        " 
                    > 

                        ❌ Stok Habis 

                    </button> 

                <?php } ?> 


            </form> 


        </div> 

    </div> 


<?php } ?> 


</div> 


</div> 



<!-- ================================
     FOOTER
================================= --> 

<footer class="footer"> 


    <div class="footer-container"> 


        <div> 

            <h2> 
                👕 FASHIONKU 
            </h2> 

            <p> 

                Toko fashion online dengan koleksi 
                pakaian kekinian untuk melengkapi 
                gaya kamu setiap hari. 

            </p> 

        </div> 



        <div> 

            <h3> 
                Menu 
            </h3> 


            <a href="index.php"> 
                🏠 Home 
            </a> 


            <a href="keranjang.php"> 
                🛒 Keranjang 
            </a> 


            <a href="wishlist.php"> 
                ❤️ Wishlist 
            </a> 

        </div> 



        <div> 

            <h3> 
                Kategori 
            </h3> 


            <a href="index.php?kategori=Kaos"> 
                Kaos 
            </a> 


            <a href="index.php?kategori=Hoodie"> 
                Hoodie 
            </a> 


            <a href="index.php?kategori=Kemeja"> 
                Kemeja 
            </a> 


            <a href="index.php?kategori=Celana"> 
                Celana 
            </a> 

        </div> 


    </div> 



    <div class="footer-bottom"> 

        © <?php echo date('Y'); ?> 
        FASHIONKU — All Rights Reserved. 

    </div> 


</footer> 


</body> 

</html>