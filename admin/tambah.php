<?php

include "../koneksi.php";

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $ukuran = $_POST['ukuran'];
    $warna = $_POST['warna'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if ($gambar != "") {

        move_uploaded_file(
            $tmp,
            "../images/" . $gambar
        );

    }

    mysqli_query($koneksi, "INSERT INTO produk
        (nama, harga, kategori, deskripsi, gambar, stok, ukuran, warna)
        VALUES
        ('$nama', '$harga', '$kategori', '$deskripsi', '$gambar', '$stok', '$ukuran', '$warna')
    ");

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Tambah Produk</title>

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

    <h1>➕ Tambah Produk</h1>


    <form
        method="POST"
        enctype="multipart/form-data"
    >

        <label>Nama Produk</label>

        <br>

        <input
            type="text"
            name="nama"
            required
        >

        <br><br>


        <label>Harga</label>

        <br>

        <input
            type="number"
            name="harga"
            required
        >

        <br><br>


        <label>Kategori</label>

        <br>

        <select name="kategori" required>

            <option value="">-- Pilih Kategori --</option>

            <option value="Kaos">
                Kaos
            </option>

            <option value="Hoodie">
                Hoodie
            </option>

            <option value="Kemeja">
                Kemeja
            </option>

            <option value="Celana">
                Celana
            </option>

            <option value="Lainnya">
                Lainnya
            </option>

        </select>

        <br><br>


        <label>Gambar Produk</label>

        <br>

        <input
            type="file"
            name="gambar"
            accept="image/*"
            required
        >

        <br><br>


        <label>Deskripsi</label>

        <br>

        <textarea
            name="deskripsi"
            required
        ></textarea>

        <br><br>


        <label>Stok</label>

        <br>

        <input
            type="number"
            name="stok"
            required
        >

        <br><br>


        <label>Ukuran</label>

        <br>

        <input
            type="text"
            name="ukuran"
            placeholder="S,M,L,XL"
        >

        <br><br>


        <label>Warna</label>

        <br>

        <input
            type="text"
            name="warna"
            placeholder="Hitam,Putih,Abu"
        >

        <br><br>


        <button
            type="submit"
            name="simpan"
            class="btn"
        >

            💾 Simpan Produk

        </button>

    </form>

</div>

</body>

</html>