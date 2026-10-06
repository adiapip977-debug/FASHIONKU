<?php

session_start();

include "koneksi.php";

if (isset($_SESSION['pelanggan_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email == "" || $password == "") {

        $error = "Email dan password wajib diisi.";

    } else {

        $email_aman = mysqli_real_escape_string($koneksi, $email);

        $query = mysqli_query(
            $koneksi,
            "SELECT * FROM pelanggan
             WHERE email='$email_aman'
             LIMIT 1"
        );

        if (mysqli_num_rows($query) == 1) {

            $data = mysqli_fetch_assoc($query);

            /*
             * Kalau password database kamu masih berupa
             * password biasa, bagian ini bisa dipakai.
             */

            if (
                password_verify($password, $data['password']) ||
                $password === $data['password']
            ) {

                $_SESSION['pelanggan_id'] = $data['id'];

                $_SESSION['pelanggan_nama'] =
                    $data['nama'];

                $_SESSION['pelanggan_email'] =
                    $data['email'];

                header("Location: index.php");
                exit;

            } else {

                $error = "Email atau password salah.";

            }

        } else {

            $error = "Email atau password salah.";

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

<title>Login - FASHIONKU</title>

<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    min-height: 100vh;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #6c2bd9,
            #ec4899,
            #ff7a18
        );

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 20px;

}

.login-wrapper {

    width: 100%;

    max-width: 1000px;

    min-height: 580px;

    display: grid;

    grid-template-columns: 1fr 1fr;

    background: white;

    border-radius: 28px;

    overflow: hidden;

    box-shadow:
        0 25px 70px
        rgba(0,0,0,.25);

}

.left-side {

    position: relative;

    padding: 50px;

    color: white;

    display: flex;

    flex-direction: column;

    justify-content: center;

    overflow: hidden;

    background:
        linear-gradient(
            145deg,
            #6d28d9,
            #db2777
        );

}

.left-side::before {

    content: "";

    position: absolute;

    width: 280px;

    height: 280px;

    background: rgba(255,255,255,.12);

    border-radius: 50%;

    top: -100px;

    right: -80px;

}

.left-side::after {

    content: "";

    position: absolute;

    width: 220px;

    height: 220px;

    background: rgba(255,255,255,.10);

    border-radius: 50%;

    bottom: -90px;

    left: -70px;

}

.logo {

    font-size: 34px;

    font-weight: 800;

    margin-bottom: 25px;

    position: relative;

    z-index: 2;

}

.left-side h1 {

    font-size: 42px;

    line-height: 1.15;

    margin: 0 0 20px;

    position: relative;

    z-index: 2;

}

.left-side p {

    font-size: 16px;

    line-height: 1.7;

    opacity: .9;

    max-width: 400px;

    position: relative;

    z-index: 2;

}

.fashion-icon {

    font-size: 100px;

    margin-top: 30px;

    position: relative;

    z-index: 2;

}

.right-side {

    padding: 55px 50px;

    display: flex;

    flex-direction: column;

    justify-content: center;

}

.right-side h2 {

    margin: 0;

    font-size: 32px;

    color: #18181b;

}

.subtitle {

    margin: 10px 0 30px;

    color: #71717a;

}

.form-group {

    margin-bottom: 20px;

}

.form-group label {

    display: block;

    margin-bottom: 8px;

    font-size: 14px;

    font-weight: 700;

    color: #27272a;

}

.form-group input {

    width: 100%;

    padding: 15px 16px;

    border: 1px solid #e4e4e7;

    border-radius: 12px;

    font-size: 15px;

    outline: none;

    transition: .2s;

    background: #fafafa;

}

.form-group input:focus {

    border-color: #a855f7;

    background: white;

    box-shadow:
        0 0 0 4px
        rgba(168,85,247,.12);

}

.error {

    background: #fef2f2;

    color: #dc2626;

    border: 1px solid #fecaca;

    padding: 12px 14px;

    border-radius: 10px;

    margin-bottom: 20px;

    font-size: 14px;

}

.login-button {

    width: 100%;

    border: none;

    padding: 15px;

    border-radius: 12px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #db2777
        );

    color: white;

    font-size: 16px;

    font-weight: 700;

    cursor: pointer;

    transition: .2s;

    box-shadow:
        0 8px 20px
        rgba(124,58,237,.25);

}

.login-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 12px 25px
        rgba(124,58,237,.35);

}

.register-text {

    text-align: center;

    margin-top: 25px;

    color: #71717a;

    font-size: 14px;

}

.register-text a {

    color: #7c3aed;

    font-weight: 700;

    text-decoration: none;

}

.register-text a:hover {

    text-decoration: underline;

}

@media (max-width: 700px) {

    body {

        padding: 15px;

    }

    .login-wrapper {

        grid-template-columns: 1fr;

        min-height: auto;

        border-radius: 22px;

    }

    .left-side {

        padding: 35px 25px;

        min-height: 240px;

        text-align: center;

        align-items: center;

    }

    .logo {

        font-size: 27px;

        margin-bottom: 12px;

    }

    .left-side h1 {

        font-size: 29px;

        margin-bottom: 10px;

    }

    .left-side p {

        font-size: 14px;

        margin: 0;

    }

    .fashion-icon {

        font-size: 55px;

        margin-top: 15px;

    }

    .right-side {

        padding: 35px 25px;

    }

    .right-side h2 {

        font-size: 27px;

    }

}

</style>

</head>

<body>

<div class="login-wrapper">

    <div class="left-side">

        <div class="logo">
            👕 FASHIONKU
        </div>

        <h1>
            Style Kamu,<br>
            Cara Kamu.
        </h1>

        <p>
            Temukan koleksi fashion favorit
            dan tampil lebih percaya diri
            dengan gaya kamu sendiri.
        </p>

        <div class="fashion-icon">
            👕 👟 🧢
        </div>

    </div>


    <div class="right-side">

        <h2>
            Selamat Datang 👋
        </h2>

        <p class="subtitle">
            Login untuk mulai belanja di FASHIONKU.
        </p>


        <?php if ($error != "") { ?>

            <div class="error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Masukkan email kamu"
                    required
                >

            </div>


            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>


            <button
                type="submit"
                class="login-button"
            >
                🔐 Login Sekarang
            </button>

        </form>


        <div class="register-text">

            Belum punya akun?

            <a href="register.php">
                Daftar sekarang
            </a>

        </div>

    </div>

</div>

</body>

</html>