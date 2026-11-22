<?php
include "db.php";

/* ONLY ADMIN */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$coaches = $conn->query("
    SELECT 
        u.user_id,
        u.email,
        c.id AS coach_program_id,
        c.experience,
        c.speciality,
        c.notes,
        p.name AS program_name
    FROM users u
    INNER JOIN coach_programs c ON u.user_id = c.coach_id
    LEFT JOIN programs p ON p.program_id = c.program_id
    WHERE u.role = 'coach'
    ORDER BY c.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Remove Coaches</title>

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
    padding:20px;
    min-height:100vh;
    overflow-x:hidden;
    position:relative;
}

/* RED GLOW BACKGROUND */
body::before{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    background:#790000;
    filter:blur(150px);
    top:-150px;
    left:-150px;
    opacity:.45;
    z-index:-2;
}

body::after{
    content:"";
    position:fixed;
    width:500px;
    height:500px;
    background:#790000;
    filter:blur(150px);
    bottom:-150px;
    right:-150px;
    opacity:.35;
    z-index:-2;
}

/* PAGE TITLE */
h2{
    text-align:center;
    padding:18px;
    margin-bottom:25px;
    border-radius:18px;
    color:white;
    font-size:24px;
    font-weight:bold;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    box-shadow:0 0 30px rgba(121,0,0,.4);
}

/* COACH CARD */
.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    border-left:5px solid #790000;
    padding:20px;
    margin-bottom:18px;
    border-radius:18px;
    box-shadow:0 0 25px rgba(121,0,0,.25);
    transition:.3s;
}

.card:hover{
    transform:translateY(-4px);
    box-shadow:0 0 35px rgba(121,0,0,.45);
}

/* LABELS */
.card b{
    color:#ff8080;
}

/* PROGRAM BADGE */
.badge{
    display:inline-block;
    padding:7px 14px;
    border-radius:25px;
    background:linear-gradient(135deg,#790000,#b30000);
    color:white;
    font-size:12px;
    font-weight:bold;
    box-shadow:0 0 12px rgba(121,0,0,.5);
}

/* REMOVE BUTTON */
.remove-btn{
    display:inline-block;
    margin-top:15px;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    color:white;
    background:linear-gradient(135deg,#ff4d4d,#b30000);
    box-shadow:0 5px 15px rgba(255,0,0,.35);
    transition:.3s;
}

.remove-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(255,0,0,.5);
}

/* RESPONSIVE */
@media(max-width:768px){

    body{
        padding:10px;
    }

    h2{
        font-size:20px;
    }

    .card{
        padding:15px;
    }

    .badge{
        display:block;
        width:fit-content;
        margin-top:10px;
    }

    .remove-btn{
        width:100%;
        text-align:center;
    }
}

</style>
</head>

<body>

<h2>❌ Remove Coaches</h2>

<?php while($c = $coaches->fetch_assoc()){ ?>

<div class="card">

    <b>Coach ID:</b> <?= $c['user_id'] ?><br>
    <b>Email:</b> <?= $c['email'] ?><br><br>

    <span class="badge">
        Program: <?= $c['program_name'] ?>
    </span><br><br>

    <b>Experience:</b> <?= $c['experience'] ?><br>
    <b>Speciality:</b> <?= $c['speciality'] ?><br>
    <b>Notes:</b> <?= $c['notes'] ?><br>

    <!-- DELETE BUTTON -->
    <a class="remove-btn"
       href="delete_coach.php?coach_id=<?= $c['user_id'] ?>"
       onclick="return confirm('❌ Delete this coach?')">
        ❌ Remove Coach
    </a>

</div>

<?php } ?>

</body>
</html>