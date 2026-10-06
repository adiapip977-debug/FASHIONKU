<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['pelanggan_id'])) {
    header("Location: login.php");
    exit;
}

$pelanggan_id = (int) $_SESSION['pelanggan_id'];

$pesanan_id = isset($_GET['pesanan_id'])
    ? (int) $_GET['pesanan_id']
    : 0;

$produk_id = isset($_GET['produk_id'])
    ? (int) $_GET['produk_id']
    : 0;

if ($pesanan_id <= 0 || $produk_id <= 0) {
    die("Data ulasan tidak lengkap.");
}


/* =========================
   CEK PESANAN
========================= */

$cek_pesanan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pesanan
     WHERE id = $pesanan_id
     AND pelanggan_id = $pelanggan_id
     AND status = 'Selesai'"
);

if (mysqli_num_rows($cek_pesanan) == 0) {
    die("Pesanan tidak ditemukan atau belum selesai.");
}


/* =========================
   AMBIL DATA PRODUK
========================= */

$query_produk = mysqli_query(
    $koneksi,
    "SELECT *
     FROM produk
     WHERE id = $produk_id"
);

if (mysqli_num_rows($query_produk) == 0) {
    die("Produk tidak ditemukan.");
}

$data_produk = mysqli_fetch_assoc($query_produk);


/* =========================
   CEK PRODUK ADA DI PESANAN
   PAKAI NAMA PRODUK
========================= */

$nama_produk = mysqli_real_escape_string(
    $koneksi,
    $data_produk['nama']
);

$cek_produk_pesanan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM detail_pesanan
     WHERE pesanan_id = $pesanan_id
     AND nama_produk = '$nama_produk'"
);

if (mysqli_num_rows($cek_produk_pesanan) == 0) {
    die("Produk ini tidak ada di pesanan tersebut.");
}


/* =========================
   CEK SUDAH DIULAS
========================= */

$cek_ulasan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM ulasan
     WHERE pelanggan_id = $pelanggan_id
     AND produk_id = $produk_id
     AND pesanan_id = $pesanan_id"
);

if (mysqli_num_rows($cek_ulasan) > 0) {
    die("Produk ini sudah kamu ulas.");
}


/* =========================
   PROSES ULASAN
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = isset($_POST['rating'])
        ? (int) $_POST['rating']
        : 0;

    $komentar = isset($_POST['komentar'])
        ? trim($_POST['komentar'])
        : '';

    if ($rating < 1 || $rating > 5) {

        $error = "Silakan pilih rating 1 sampai 5 bintang.";

    } elseif ($komentar === '') {

        $error = "Komentar tidak boleh kosong.";

    } else {

        $komentar_db = mysqli_real_escape_string(
            $koneksi,
            $komentar
        );

        $insert = mysqli_query(
            $koneksi,
            "INSERT INTO ulasan
            (
                pelanggan_id,
                produk_id,
                pesanan_id,
                rating,
                komentar
            )
            VALUES
            (
                $pelanggan_id,
                $produk_id,
                $pesanan_id,
                $rating,
                '$komentar_db'
            )"
        );

        if ($insert) {

            header(
                "Location: detail_pesanan_customer.php?id=" . $pesanan_id
            );

            exit;

        } else {

            $error = "Gagal menyimpan ulasan: " . mysqli_error($koneksi);

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Beri Ulasan - FASHIONKU</title>

<link rel="stylesheet" href="style.css">

<style>

.ulasan-container {
    max-width: 650px;
    margin: 50px auto;
}

.ulasan-card {
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
}

.ulasan-card h1 {
    margin-bottom: 8px;
}

.ulasan-subtitle {
    color: #6b7280;
    margin-bottom: 25px;
}


/* PRODUK */

.produk-ulasan {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 25px;
}

.produk-ulasan img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    border-radius: 10px;
    background: white;
}

.produk-ulasan-info h3 {
    margin-bottom: 5px;
}

.produk-ulasan-info p {
    color: #e11d48;
    font-weight: bold;
}


/* RATING */

.rating-title {
    font-weight: bold;
    margin-bottom: 8px;
}

.rating-bintang {
    display: inline-flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 6px;
    margin: 10px 0 25px;
}

.rating-bintang input {
    position: absolute !important;
    opacity: 0 !important;
    width: 1px !important;
    height: 1px !important;
    pointer-events: none !important;
}

.rating-bintang label {
    display: block !important;
    width: auto !important;
    padding: 0 !important;
    margin: 0 !important;
    font-size: 45px !important;
    line-height: 1 !important;
    color: #d1d5db !important;
    cursor: pointer !important;
    user-select: none;
    transition: 0.2s;
}

.rating-bintang label:hover,
.rating-bintang label:hover ~ label {
    color: #fbbf24 !important;
    transform: scale(1.1);
}

.rating-bintang input:checked ~ label {
    color: #fbbf24 !important;
}


/* KOMENTAR */

.komentar-title {
    font-weight: bold;
    margin-bottom: 8px;
}

.ulasan-card textarea {
    width: 100%;
    max-width: 100%;
    min-height: 140px;
    resize: vertical;
    margin-bottom: 15px;
}


/* ERROR */

.error-ulasan {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}


/* BUTTON */

.btn-ulasan {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #111827, #374151);
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.btn-ulasan:hover {
    transform: translateY(-2px);
}

.btn-kembali {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #6b7280;
    text-decoration: none;
}

</style>

</head>

<body>


<nav class="navbar">

    <h2>FASHIONKU</h2>

    <div>

        <a href="index.php">Home</a>

        <a href="keranjang.php">🛒 Keranjang</a>

        <a href="pesanan_saya.php">📦 Pesanan Saya</a>

        <a href="wishlist.php">❤️ Wishlist</a>

        <a href="notifikasi.php">🔔 Notifikasi</a>

    </div>

</nav>


<div class="ulasan-container">

    <div class="ulasan-card">

        <h1>⭐ Beri Ulasan</h1>

        <p class="ulasan-subtitle">
            Bagaimana pengalaman kamu dengan produk ini?
        </p>


        <div class="produk-ulasan">

            <?php if (!empty($data_produk['gambar'])): ?>

                <img
                    src="images/<?php echo htmlspecialchars($data_produk['gambar']); ?>"
                    alt="Produk"
                >

            <?php endif; ?>


            <div class="produk-ulasan-info">

                <h3>
                    <?php echo htmlspecialchars($data_produk['nama']); ?>
                </h3>

                <p>
                    Rp <?php
                    echo number_format(
                        $data_produk['harga'],
                        0,
                        ',',
                        '.'
                    );
                    ?>
                </p>

            </div>

        </div>


        <?php if (isset($error)): ?>

            <div class="error-ulasan">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="rating-title">
                Kasih Rating
            </div>


            <div class="rating-bintang">

                <input
                    type="radio"
                    name="rating"
                    value="5"
                    id="rating5"
                >
                <label for="rating5">★</label>


                <input
                    type="radio"
                    name="rating"
                    value="4"
                    id="rating4"
                >
                <label for="rating4">★</label>


                <input
                    type="radio"
                    name="rating"
                    value="3"
                    id="rating3"
                >
                <label for="rating3">★</label>


                <input
                    type="radio"
                    name="rating"
                    value="2"
                    id="rating2"
                >
                <label for="rating2">★</label>


                <input
                    type="radio"
                    name="rating"
                    value="1"
                    id="rating1"
                    required
                >
                <label for="rating1">★</label>

            </div>


            <div class="komentar-title">
                Komentar
            </div>


            <textarea
                name="komentar"
                placeholder="Tulis pengalaman kamu dengan produk ini..."
                required
            ></textarea>


            <button
                type="submit"
                class="btn-ulasan"
            >
                ⭐ Kirim Ulasan
            </button>


            <a
                href="detail_pesanan_customer.php?id=<?php echo $pesanan_id; ?>"
                class="btn-kembali"
            >
                ← Kembali ke Detail Pesanan
            </a>

        </form>

    </div>

</div>


<footer>

    <p>
        © <?php echo date('Y'); ?> FASHIONKU
    </p>

</footer>


</body>
</html>