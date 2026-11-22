<?php
include "db.php";

$reg_id = $_GET['reg_id'] ?? null;

if(!$reg_id){
    die("Invalid Request");
}

// Delete related data first to avoid foreign key constraints
$conn->query("DELETE FROM workout_plan WHERE reg_id=$reg_id");
$conn->query("DELETE FROM progress WHERE reg_id=$reg_id");
$conn->query("DELETE FROM chat_messages WHERE reg_id=$reg_id");

$conn->query("DELETE FROM registrations WHERE reg_id=$reg_id");

header("Location: remove_users.php");
exit();
?>