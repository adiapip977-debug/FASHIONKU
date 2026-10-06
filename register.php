<?php

session_start();
include "koneksi.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);

    if ($nama == "" || $email == "" || $password == "" || $no_hp == "" || $alamat == "") {

        $error = "Semua data wajib diisi.";

    } else {

        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM pelanggan WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $cek,
            "s",
            $email
        );

        mysqli_stmt_execute($cek);

        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $error = "Email sudah terdaftar.";

        } else {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO pelanggan
                (nama, email, password, no_hp, alamat)
                VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $nama,
                $email,
                $password_hash,
                $no_hp,
                $alamat
            );

            if (mysqli_stmt_execute($stmt)) {

                $success = "Registrasi berhasil! Silakan login.";

            } else {

                $error = "Registrasi gagal.";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Register - FASHIONKU</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>

        <a href="index.php">Home</a>

        <a href="login.php">Login</a>

    </div>

</nav>


<div class="container">

    <h1>👤 Daftar Akun</h1>

    <p>Buat akun customer FASHIONKU.</p>


    <?php if ($error != "") { ?>

        <p style="color:#e11d48; font-weight:bold;">
            ❌ <?php echo $error; ?>
        </p>

    <?php } ?>


    <?php if ($success != "") { ?>

        <p style="color:green; font-weight:bold;">
            ✅ <?php echo $success; ?>
        </p>

        <a href="login.php" class="btn">
            Login Sekarang
        </a>

    <?php } else { ?>


        <form method="POST">

            <label>Nama Lengkap</label>

            <br>

            <input
                type="text"
                name="nama"
                required
            >

            <br><br>


            <label>Email</label>

            <br>

            <input
                type="email"
                name="email"
                required
            >

            <br><br>


            <label>Password</label>

            <br>

            <input
                type="password"
                name="password"
                minlength="6"
                required
            >

            <br><br>


            <label>No. HP</label>

            <br>

            <input
                type="text"
                name="no_hp"
                required
            >

            <br><br>


            <label>Alamat</label>

            <br>

            <textarea
                name="alamat"
                required
            ></textarea>

            <br><br>


            <button
                type="submit"
                name="register"
                class="btn"
            >
                📝 Daftar
            </button>

        </form>


        <p style="margin-top:20px;">

            Sudah punya akun?

            <a href="login.php">
                Login di sini
            </a>

        </p>


    <?php } ?>

</div>

</body>

</html>