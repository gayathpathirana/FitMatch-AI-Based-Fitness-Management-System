<?php
include "db.php";
$id = intval($_GET['id']);
$conn->query("DELETE FROM programs WHERE program_id=$id");
header("Location: admin_dashboard.php");