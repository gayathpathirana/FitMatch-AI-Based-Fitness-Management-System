<?php
include "db.php";

$users = $conn->query("
SELECT 
r.reg_id,
r.fullname,
r.email,
r.phone,
r.level,
p.name AS program_name
FROM registrations r
LEFT JOIN programs p ON r.program_id = p.program_id
ORDER BY r.reg_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Users</title>
</head>
<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#000;
    color:white;
    min-height:100vh;
    padding:30px;
    position:relative;
    overflow-x:hidden;
}

/* Background Glow */
body::before{
    content:"";
    position:fixed;
    width:700px;
    height:700px;
    background:#790000;
    filter:blur(180px);
    top:-250px;
    left:-250px;
    opacity:0.45;
    z-index:-2;
}

body::after{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    background:#790000;
    filter:blur(180px);
    bottom:-250px;
    right:-250px;
    opacity:0.35;
    z-index:-2;
}

/* Page Title */
h2{
    text-align:center;
    margin-bottom:30px;
    font-size:32px;
    color:#fff;
    text-shadow:0 0 15px rgba(121,0,0,0.7);
}

/* User Card */
.user-card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:20px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 0 25px rgba(121,0,0,0.25);
    transition:0.3s;
}

.user-card:hover{
    transform:translateY(-4px);
    box-shadow:0 0 35px rgba(121,0,0,0.45);
}

/* Name */
.user-name{
    font-size:22px;
    font-weight:bold;
    color:#ff4d4d;
    margin-bottom:10px;
}

/* Info */
.info{
    color:#ddd;
    margin:5px 0;
    font-size:15px;
}

/* Badges */
.badge{
    display:inline-block;
    padding:8px 14px;
    background:#790000;
    color:white;
    border-radius:20px;
    margin-top:10px;
    margin-right:8px;
    font-size:13px;
    font-weight:bold;
}

/* Mobile */
@media(max-width:768px){

    body{
        padding:15px;
    }

    h2{
        font-size:26px;
    }

    .user-card{
        padding:15px;
    }

}

</style>
<body>

<h2>Clients List</h2>

<?php while($u = $users->fetch_assoc()){ ?>

<div style="border:1px solid #ccc; padding:10px; margin:10px;">

<b><?= $u['fullname'] ?? '' ?></b><br>
<?= $u['email'] ?? '' ?><br>
<?= $u['phone'] ?? '' ?><br><br>

Level: <?= $u['level'] ?? '' ?><br>
Program: <?= $u['program_name'] ?? '' ?>

</div>

<?php } ?>

</body>
</html>