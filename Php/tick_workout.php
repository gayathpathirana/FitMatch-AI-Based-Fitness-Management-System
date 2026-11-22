<?php
include 'db.php';

$id = intval($_POST['id']);
$reg_id = intval($_POST['reg_id']);

/* GET CURRENT VALUE */
$q = $conn->query("SELECT is_done FROM workout_plan WHERE id=$id");
$row = $q->fetch_assoc();

$current = $row['is_done'] ?? 0;

/* TOGGLE VALUE */
$new = ($current == 1) ? 0 : 1;

/* UPDATE */
$conn->query("
    UPDATE workout_plan 
    SET is_done=$new 
    WHERE id=$id
");

header("Location: client_dashboard.php?reg_id=$reg_id");
exit;
?>