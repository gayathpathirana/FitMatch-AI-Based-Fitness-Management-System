<?php
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ADMIN CHECK */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("Please login as admin.");
}

$coaches = $conn->query("
    SELECT 
        u.user_id,
        u.email,
        u.coach_status,
        c.program_id,
        c.experience,
        c.speciality,
        c.notes,
        p.name AS program_name
    FROM users u
    LEFT JOIN coach_programs c ON u.user_id = c.coach_id
    LEFT JOIN programs p ON p.program_id = c.program_id
    WHERE u.role = 'coach'
    ORDER BY u.user_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Coaches</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#000;
    color:#fff;
    padding:20px;
    min-height:100vh;
    position:relative;
    overflow-x:hidden;
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
    z-index:-1;
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
    z-index:-1;
}

/* TITLE */
h2{
    text-align:center;
    padding:18px;
    margin-bottom:25px;
    border-radius:18px;
    font-size:24px;
    font-weight:bold;
    color:white;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    box-shadow:0 0 30px rgba(121,0,0,.4);
}

/* CARD */
.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    border-left:5px solid #790000;
    padding:18px;
    margin:15px auto;
    width:80%;
    border-radius:18px;
    box-shadow:0 0 25px rgba(121,0,0,.25);
    transition:.3s;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 0 35px rgba(121,0,0,.45);
}

/* TEXT */
.card b{
    color:#ff8080;
}

/* BADGE */
.badge{
    display:inline-block;
    padding:7px 14px;
    border-radius:25px;
    background:linear-gradient(135deg,#790000,#b30000);
    color:white;
    font-size:12px;
    font-weight:bold;
    box-shadow:0 0 12px rgba(121,0,0,.4);
}

/* STATUS */
.approved{
    color:#22c55e;
    font-weight:bold;
    font-size:14px;
}

/* BUTTON */
.card a{
    display:inline-block;
    margin-top:10px;
    padding:10px 16px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    color:white;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    transition:.3s;
}

.card a:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(0,255,0,0.2);
}

/* EMPTY TEXT */
h3{
    text-align:center;
    margin-top:20px;
}

/* MOBILE */
@media(max-width:768px){

    body{
        padding:10px;
    }

    .card{
        width:100%;
    }

    h2{
        font-size:20px;
    }
}

</style>
</head>

<body>

<h2>🧑‍💼 Manage Coaches</h2>

<?php if($coaches && $coaches->num_rows > 0){ ?>

<?php while($c = $coaches->fetch_assoc()){ ?>

<div class="card">

    <p><b>Email:</b> <?= htmlspecialchars($c['email']) ?></p>

    <p><b>Status:</b>
        <?= htmlspecialchars($c['coach_status'] ?? 'pending') ?>
    </p>

    <p><b>Program:</b>
        <span class="badge">
            <?= htmlspecialchars($c['program_name'] ?? 'Not Selected') ?>
        </span>
    </p>

    <p><b>Experience:</b> <?= htmlspecialchars($c['experience'] ?? '-') ?></p>
    <p><b>Speciality:</b> <?= htmlspecialchars($c['speciality'] ?? '-') ?></p>
    <p><b>Notes:</b> <?= htmlspecialchars($c['notes'] ?? '-') ?></p>

    <br>

    <?php if(($c['coach_status'] ?? '') !== 'approved'){ ?>

        <a href="approve_coach.php?id=<?= $c['user_id'] ?>"
           style="display:inline-block;background:green;color:white;padding:8px 12px;border-radius:6px;text-decoration:none;">
           ✅ Approve Coach
        </a>

    <?php } else { ?>

        <span class="approved">✔ APPROVED</span>

    <?php } ?>

</div>

<?php } ?>

<?php } else { ?>

<h3>No coaches found.</h3>

<?php } ?>

</body>
</html>