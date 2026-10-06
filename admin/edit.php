<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include "../koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk WHERE id = $id"
);

$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    die("Produk tidak ditemukan.");
}

if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $ukuran = $_POST['ukuran'];
    $warna = $_POST['warna'];

    $gambar_lama = $produk['gambar'];

    if (!empty($_FILES['gambar']['name'])) {

        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];

        move_uploaded_file(
            $tmp,
            "../images/" . $gambar
        );

    } else {

        $gambar = $gambar_lama;

    }

    mysqli_query(
        $koneksi,
        "UPDATE produk SET
        nama='$nama',
        harga='$harga',
        kategori='$kategori',
        deskripsi='$deskripsi',
        gambar='$gambar',
        stok='$stok',
        ukuran='$ukuran',
        warna='$warna'
        WHERE id=$id"
    );

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Produk</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<nav class="navbar">

    <h2>⚙️ ADMIN FASHIONKU</h2>

    <div>

        <a href="index.php">Kembali</a>

    </div>

</nav>

<div class="container">

    <h1>✏️ Edit Produk</h1>

    <form method="POST" enctype="multipart/form-data">

        <label>Nama Produk</label>
        <br>

        <input
            type="text"
            name="nama"
            value="<?php echo htmlspecialchars($produk['nama']); ?>"
            required
        >

        <br><br>


        <label>Harga</label>
        <br>

        <input
            type="number"
            name="harga"
            value="<?php echo $produk['harga']; ?>"
            required
        >

        <br><br>


        <label>Kategori</label>
        <br>

        <select name="kategori" required>

            <option value="Kaos"
                <?php echo $produk['kategori'] == 'Kaos' ? 'selected' : ''; ?>>
                Kaos
            </option>

            <option value="Hoodie"
                <?php echo $produk['kategori'] == 'Hoodie' ? 'selected' : ''; ?>>
                Hoodie
            </option>

            <option value="Kemeja"
                <?php echo $produk['kategori'] == 'Kemeja' ? 'selected' : ''; ?>>
                Kemeja
            </option>

            <option value="Celana"
                <?php echo $produk['kategori'] == 'Celana' ? 'selected' : ''; ?>>
                Celana
            </option>

            <option value="Lainnya"
                <?php echo $produk['kategori'] == 'Lainnya' ? 'selected' : ''; ?>>
                Lainnya
            </option>

        </select>

        <br><br>


        <label>Gambar Produk</label>
        <br>

        <?php if (!empty($produk['gambar'])) { ?>

            <img
                src="../images/<?php echo htmlspecialchars($produk['gambar']); ?>"
                width="150"
                height="150"
                style="object-fit:contain; display:block; margin:10px 0;"
            >

        <?php } ?>

        <input
            type="file"
            name="gambar"
            accept="image/*"
        >

        <br><br>


        <label>Deskripsi</label>
        <br>

        <textarea
            name="deskripsi"
            required
        ><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>

        <br><br>


        <label>Stok</label>
        <br>

        <input
            type="number"
            name="stok"
            value="<?php echo $produk['stok']; ?>"
            min="0"
            required
        >

        <br><br>


        <label>Ukuran</label>
        <br>

        <input
            type="text"
            name="ukuran"
            value="<?php echo htmlspecialchars($produk['ukuran']); ?>"
            placeholder="S,M,L,XL"
        >

        <br><br>


        <label>Warna</label>
        <br>

        <input
            type="text"
            name="warna"
            value="<?php echo htmlspecialchars($produk['warna']); ?>"
            placeholder="Hitam,Putih,Abu"
        >

        <br><br>


        <button
            type="submit"
            name="update"
            class="btn"
        >

            💾 Update Produk

        </button>

    </form>

</div>

</body>

</html>