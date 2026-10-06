<?php

$id = $_GET['id'];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Pesanan Berhasil</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>🎉 Pesanan Berhasil!</h1>

    <p>
        Terima kasih sudah berbelanja di FASHIONKU.
    </p>

    <p>
        Nomor pesanan kamu:
        <b>#<?php echo $id; ?></b>
    </p>

    <br>

    <a href="index.php" class="btn">
        Kembali Belanja
    </a>

</div>

</body>

</html>