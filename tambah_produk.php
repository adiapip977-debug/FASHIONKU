<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    $query = "INSERT INTO produk (nama, harga, deskripsi)
              VALUES ('$nama', '$harga', '$deskripsi')";

    mysqli_query($koneksi, $query);

    echo "Produk berhasil ditambahkan!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
</head>
<body>

    <h1>Tambah Produk</h1>

    <form method="POST">

        <label>Nama Produk</label><br>
        <input type="text" name="nama" required>

        <br><br>

        <label>Harga</label><br>
        <input type="number" name="harga" required>

        <br><br>

        <label>Deskripsi</label><br>
        <textarea name="deskripsi" required></textarea>

        <br><br>

        <button type="submit" name="simpan">
            Simpan Produk
        </button>

    </form>

</body>
</html>