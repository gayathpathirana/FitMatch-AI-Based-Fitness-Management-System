<?php
include "db.php";


if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    die("Access denied");
}

$id = intval($_GET['id'] ?? 0);

if($id == 0){
    die("Invalid request");
}

$update = $conn->query("
    UPDATE registrations 
    SET approval_status = 'approved'
    WHERE reg_id = $id
");

if($update){
    echo "<h2 style='color:green;text-align:center;margin-top:50px'>
    Payment Approved Successfully
    </h2>";

    echo "<script>
        setTimeout(function(){
            window.location.href='admin_dashboard.php';
        }, 1500);
    </script>";
}else{
    echo "Error: " . $conn->error;
}
?>