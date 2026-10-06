<?php

session_start();

unset($_SESSION['keranjang']);

header("Location: keranjang.php");
exit;

?>