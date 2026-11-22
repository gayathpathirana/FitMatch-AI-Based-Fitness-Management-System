<?php
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coach'){
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM programs");
?>

<h2>Select Program (Coach)</h2>

<?php while($row = $result->fetch_assoc()){ ?>

    <div style="border:1px solid #ccc; padding:15px; margin:10px;">

        <h3><?= $row['name'] ?></h3>

        <a href="coach_dashboard.php?program_id=<?= $row['program_id'] ?>"
           style="padding:8px 12px;background:green;color:white;text-decoration:none;">
            Select Program
        </a>

    </div>

<?php } ?>