<?php
include "db.php";

/* ONLY ADMIN */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

/* GET ID SAFELY */
$coach_id = intval($_GET['coach_id'] ?? 0);

if($coach_id <= 0){
    die("Invalid Coach ID");
}

/* CHECK COACH EXISTS */
$check = $conn->query("
    SELECT user_id FROM users 
    WHERE user_id = $coach_id AND role = 'coach'
");

if($check->num_rows == 0){
    die("Coach not found");
}

/* DELETE COACH PROGRAMS FIRST */
$conn->query("
    DELETE FROM coach_programs 
    WHERE coach_id = $coach_id
");

/* DELETE COACH USER */
$conn->query("
    DELETE FROM users 
    WHERE user_id = $coach_id AND role = 'coach'
");

/* REDIRECT BACK */
header("Location: admin_view_coaches.php?msg=Coach deleted successfully");
exit();
?>