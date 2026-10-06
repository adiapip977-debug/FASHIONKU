<?php

session_start();
include "koneksi.php";

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM pelanggan WHERE email = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $email
    );

    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);

    $pelanggan = mysqli_fetch_assoc($hasil);

    if (
        $pelanggan &&
        password_verify(
            $password,
            $pelanggan['password']
        )
    ) {

        $_SESSION['pelanggan_id'] = $pelanggan['id'];
        $_SESSION['pelanggan_nama'] = $pelanggan['nama'];
        $_SESSION['pelanggan_email'] = $pelanggan['email'];

        header("Location: index.php");
        exit;

    } else {

        $error = "Email atau password salah.";

    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login - FASHIONKU</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <h2>👕 FASHIONKU</h2>

    <div>

        <a href="index.php">Home</a>

        <a href="register.php">Register</a>

    </div>

</nav>


<div class="container">

    <h1>🔐 Login Customer</h1>

    <p>Login ke akun FASHIONKU kamu.</p>


    <?php if ($error != "") { ?>

        <p style="color:#e11d48; font-weight:bold;">

            ❌ <?php echo $error; ?>

        </p>

    <?php } ?>


    <form method="POST">

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
            required
        >

        <br><br>


        <button
            type="submit"
            name="login"
            class="btn"
        >
            🔐 Login
        </button>

    </form>


    <p style="margin-top:20px;">

        Belum punya akun?

        <a href="register.php">
            Daftar sekarang
        </a>

    </p>

</div>

</body>

</html>