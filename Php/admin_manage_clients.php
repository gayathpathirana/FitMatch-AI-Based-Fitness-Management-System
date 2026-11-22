<?php
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ADMIN CHECK (SAFE + CLEAN) */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$clients = $conn->query("
    SELECT r.*, p.name AS program_name
    FROM registrations r
    LEFT JOIN programs p ON r.program_id = p.program_id
    WHERE r.approval_status = 'pending'
    ORDER BY r.reg_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Clients</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background: linear-gradient(135deg, #0b0b0b, #1a0000);
    padding:20px;
    color:#fff;
}

/* HEADER */
h2{
    text-align:center;
    padding:18px;
    margin-bottom:25px;
    border-radius:14px;
    background: linear-gradient(135deg, #790000, #ff1a1a);
    color:white;
    font-size:24px;
    font-weight:bold;
    box-shadow:0 4px 20px rgba(255,0,0,0.3);
}

/* CARD */
.card{
    background: rgba(20,20,20,0.95);
    padding:20px;
    margin:15px auto;
    border-radius:14px;
    width:85%;
    box-shadow:0 0 25px rgba(255,0,0,0.15);
    border-left:5px solid #ff1a1a;
    transition:0.3s ease;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 0 35px rgba(255,0,0,0.25);
}

.card b{
    color:#ff4d4d;
}

/* BUTTON */
.btn{
    display:inline-block;
    padding:10px 15px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:13px;
    font-weight:bold;
    margin-top:10px;
    transition:0.3s;
}

/* APPROVE BUTTON */
.approve{
    background: linear-gradient(135deg, #790000, #ff1a1a);
}

.approve:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(255,0,0,0.3);
}

/* EMPTY */
.card h3{
    text-align:center;
    color:#aaa;
}

/* RESPONSIVE */
@media(max-width:768px){
    body{ padding:10px; }
    .card{ width:100%; }
    h2{ font-size:20px; }
    .btn{ width:100%; text-align:center; }
}
</style>

</head>

<body>

<h2>👥 Manage Clients (Payment Approval)</h2>

<?php if ($clients && $clients->num_rows > 0) { ?>

    <?php while ($c = $clients->fetch_assoc()) { ?>

        <div class="card">
            <b>Name:</b> <?= htmlspecialchars($c['fullname']) ?><br>
            <b>Email:</b> <?= htmlspecialchars($c['email']) ?><br>
            <b>Program:</b> <?= htmlspecialchars($c['program_name']) ?><br>
            <b>Status:</b> <?= htmlspecialchars($c['approval_status']) ?><br><br>

            <a href="approve_client.php?id=<?= $c['reg_id'] ?>" class="btn approve">
                💳 Approve Payment
            </a>
        </div>

    <?php } ?>

<?php } else { ?>

    <div class="card">
        <h3>No pending client approvals</h3>
    </div>

<?php } ?>

</body>
</html>