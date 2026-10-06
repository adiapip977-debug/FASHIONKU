<?php

session_start();

session_unset();
session_destroy();

header("Location: /toko_online/admin/login.php");
exit;

?>