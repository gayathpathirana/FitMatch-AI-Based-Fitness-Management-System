<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
?>