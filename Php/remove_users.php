<?php
include "db.php";

/* GET ALL CLIENTS */
$clients = $conn->query("
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
<title>Remove Users</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#000;
    min-height:100vh;
    color:white;
    overflow-x:hidden;
    position:relative;
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

/* Header */
.header{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,0.08);
    color:#fff;
    padding:20px;
    text-align:center;
    font-size:28px;
    font-weight:bold;
    box-shadow:0 0 25px rgba(121,0,0,0.3);
}

/* Container */
.container{
    width:90%;
    max-width:1200px;
    margin:25px auto;
}

/* User Card */
.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    border-left:5px solid #790000;
    padding:20px;
    margin-bottom:18px;
    border-radius:18px;
    box-shadow:0 0 25px rgba(121,0,0,0.2);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-4px);
    box-shadow:0 0 35px rgba(121,0,0,0.45);
}

/* User Name */
.card b{
    font-size:20px;
    color:#ff4d4d;
}

/* User Info */
.card small{
    color:#cccccc;
    display:block;
    margin-top:4px;
}

/* Remove Button */
.delete-btn{
    float:right;
    padding:10px 18px;
    background:linear-gradient(135deg,#ff4d4d,#790000);
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-size:13px;
    font-weight:bold;
    transition:0.3s;
    box-shadow:0 0 15px rgba(255,0,0,0.25);
}

.delete-btn:hover{
    background:linear-gradient(135deg,#ff1a1a,#500000);
    transform:scale(1.05);
    box-shadow:0 0 20px rgba(255,0,0,0.5);
}

/* Badges */
.badge{
    display:inline-block;
    padding:8px 14px;
    background:#790000;
    color:white;
    border-radius:20px;
    font-size:12px;
    margin-top:10px;
    margin-right:8px;
    font-weight:bold;
}

/* Scrollbar */
::-webkit-scrollbar{
    width:8px;
}

::-webkit-scrollbar-track{
    background:#111;
}

::-webkit-scrollbar-thumb{
    background:#790000;
    border-radius:10px;
}

::-webkit-scrollbar-thumb:hover{
    background:#a00000;
}

/* Mobile */
@media(max-width:768px){

    .container{
        width:95%;
    }

    .delete-btn{
        float:none;
        display:block;
        width:100%;
        text-align:center;
        margin-bottom:15px;
    }

}

</style>
</head>

<body>

<div class="header">❌ Remove Users</div>

<div class="container">

<?php while($c = $clients->fetch_assoc()){ ?>

<div class="card">

<a class="delete-btn"
href="delete_client.php?reg_id=<?= $c['reg_id'] ?>"
onclick="return confirm('Delete this user?')">
Remove
</a>

<b><?= htmlspecialchars($c['fullname'] ?? '') ?></b><br>
<small><?= htmlspecialchars($c['email'] ?? '') ?></small><br>
<small><?= htmlspecialchars($c['phone'] ?? '') ?></small><br><br>

<span class="badge"><?= $c['level'] ?? '' ?></span>
<span class="badge"><?= $c['program_name'] ?? 'No Program' ?></span>

</div>

<?php } ?>

</div>

</body>
</html>