<?php
include "db.php";

$reg_id = $_GET['reg_id'] ?? null;

if(!$reg_id){
    die("❌ Invalid Request - No User ID");
}

/* DELETE ONLY */
$stmt = $conn->prepare("DELETE FROM registrations WHERE reg_id = ?");
$stmt->bind_param("i", $reg_id);

if($stmt->execute()){
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "❌ Delete Failed";
}
?>