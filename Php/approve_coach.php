<?php
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    die("Admin only");
}

$id = intval($_GET['id'] ?? 0);

if($id <= 0){
    die("Invalid ID");
}

$stmt = $conn->prepare("
    UPDATE users 
    SET coach_status='approved', is_verified=1 
    WHERE user_id = ?
");

$stmt->bind_param("i", $id);

/* ================= EXECUTE ================= */
if($stmt->execute()){

    if($stmt->affected_rows > 0){

        header("Location: admin_view_coaches.php?msg=approved");
        exit();

    } else {
        die("No update happened (already approved or wrong ID)");
    }

} else {
    die("Database error: " . $stmt->error);
}
?>