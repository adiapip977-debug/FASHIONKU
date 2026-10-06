<?php

session_start();
include "../koneksi.php";

if (isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM admin
         WHERE username='$username'
         AND password='$password'"
    );

    if (mysqli_num_rows($query) > 0) {

        $_SESSION['admin'] = $username;

        header("Location: index.php");
        exit;

    } else {

        $error = "Username atau password salah!";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login Admin FASHIONKU</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<div class="container">

    <h1>🔐 Login Admin</h1>

    <?php if ($error != "") { ?>

        <p><?php echo $error; ?></p>

    <?php } ?>

    <form method="POST">

        <label>Username</label>
        <br>

        <input
            type="text"
            name="username"
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

</div>

</body>

</html>