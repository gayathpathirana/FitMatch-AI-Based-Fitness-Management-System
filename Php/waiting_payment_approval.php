<?php
include "db.php";

if(!isset($_GET['reg_id']) || $_GET['reg_id'] == ''){
    die("No registration ID found. Please login again.");
}

$reg_id = intval($_GET['reg_id']);

$stmt = $conn->prepare("SELECT * FROM registrations WHERE reg_id = ?");
$stmt->bind_param("i", $reg_id);
$stmt->execute();
$result = $stmt->get_result();

$reg = $result->fetch_assoc();

if(!$reg){
    die("Invalid registration");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Waiting Approval</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#000;
    color:white;
    text-align:center;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
}

/* glowing background */
body::before,
body::after{
    content:"";
    position:fixed;
    border-radius:50%;
    filter:blur(140px);
    z-index:0;
}

body::before{
    width:500px;
    height:500px;
    background:#790000;
    top:-120px;
    left:-120px;
    opacity:0.6;
}

body::after{
    width:450px;
    height:450px;
    background:#a00000;
    bottom:-120px;
    right:-120px;
    opacity:0.4;
}

/* main box */
.box{
    position:relative;
    z-index:1;
    width:400px;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    color:white;
    padding:30px;
    border-radius:16px;
    border:1px solid rgba(255,255,255,0.1);
    box-shadow:0 0 30px rgba(121,0,0,0.4);
}

/* heading */
h2{
    color:#ff4d4d;
    margin-bottom:15px;
}

/* text */
p{
    color:#ddd;
    font-size:14px;
    margin:8px 0;
}

/* button */
button{
    padding:10px 18px;
    border:none;
    background:linear-gradient(135deg,#790000,#ff1a1a);
    color:white;
    border-radius:10px;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

button:hover{
    transform:scale(1.05);
}
</style>
</head>

<body>

<div class="box">

<h2>⏳ Waiting for Admin Approval</h2>

<p><b>Name:</b> <?= $reg['fullname'] ?></p>
<p>Your request is pending approval.</p>

<button onclick="window.location.href='login.php'">
Go Back
</button>

</div>

</body>
</html>