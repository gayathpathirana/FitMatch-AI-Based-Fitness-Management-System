<?php
include "db.php";

/* GET DATA */
$reg_id = intval($_POST['reg_id'] ?? 0);
$message = trim($_POST['message'] ?? '');
$sender  = $_POST['sender'] ?? 'user';

/* VALIDATION */
if ($reg_id == 0 || $message == '') {
    die("Invalid request");
}

/* SAFE INSERT */
$stmt = $conn->prepare("
    INSERT INTO chat_messages (reg_id, sender, message, created_at)
    VALUES (?, ?, ?, NOW())
");

$stmt->bind_param("iss", $reg_id, $sender, $message);
$stmt->execute();

echo "SUCCESS";
?>