<?php
require "db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

/* ROLE CHECK */
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


$userCount = $conn->query("SELECT COUNT(*) AS t FROM users")->fetch_assoc()['t'];

$coachCount = $conn->query("
    SELECT COUNT(*) AS t 
    FROM users 
    WHERE role='coach'
")->fetch_assoc()['t'];

$clientCount = $conn->query("
    SELECT COUNT(*) AS t 
    FROM registrations
")->fetch_assoc()['t'];

$programCount = $conn->query("
    SELECT COUNT(*) AS t 
    FROM programs
")->fetch_assoc()['t'];

$chatCount = $conn->query("
    SELECT COUNT(*) AS t 
    FROM chat_messages
")->fetch_assoc()['t'];

$progressCount = $conn->query("
    SELECT COUNT(*) AS t 
    FROM progress
")->fetch_assoc()['t'];

$latestClients = $conn->query("
    SELECT 
        r.reg_id,
        r.fullname,
        r.email,
        r.phone,
        r.level,
        p.name AS program_name
    FROM registrations r
    LEFT JOIN programs p ON r.program_id = p.program_id
    WHERE r.fullname IS NOT NULL
    ORDER BY r.reg_id DESC
    LIMIT 4
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>
/* ================= GLOBAL ================= */
body{
    margin:0;
    font-family:Arial;
    background:#000;
    color:#fff;
    overflow-x:hidden;
}

/* glowing background */
body::before,
body::after{
    content:"";
    position:fixed;
    border-radius:50%;
    filter:blur(160px);
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

/* ================= HEADER ================= */
.header{
    position:relative;
    z-index:1;
    padding:20px;
    text-align:center;
    font-size:22px;
    font-weight:bold;
    letter-spacing:2px;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,0.1);
    color:#fff;
}

/* ================= GRID ================= */
.container{
    position:relative;
    z-index:1;
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:15px;
    padding:20px;
}

/* ================= PANELS ================= */
.panel{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:18px;
    padding:18px;
    box-shadow:0 0 25px rgba(121,0,0,0.25);
}

/* headings */
h3{
    color:#fff;
    border-left:4px solid #790000;
    padding-left:10px;
    margin-bottom:15px;
}

h4{
    color:#fff;
    margin-top:20px;
}

/* ================= STATS ================= */
.stat-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.stat{
    background:rgba(255,255,255,0.08);
    padding:12px;
    border-radius:12px;
    text-align:center;
    border:1px solid rgba(255,255,255,0.1);
}

.stat b{
    color:#ff4d4d;
    font-size:18px;
}

/* ================= BUTTONS ================= */
.btn{
    display:block;
    padding:10px;
    margin-top:8px;
    text-align:center;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:13px;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.03);
}

/* main theme buttons */
.blue{
    background:linear-gradient(135deg,#790000,#a00000);
}

.sky{
    background:linear-gradient(135deg,#a00000,#ff1a1a);
}

.red{
    background:linear-gradient(135deg,#ff1a1a,#800000);
}

.green{
    background:linear-gradient(135deg,#b30000,#ff4d4d);
}

/* ================= QUICK ACTION GRID ================= */
.action-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

/* ================= CLIENT CARD ================= */
.program-card{
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:14px;
    padding:12px;
    margin:10px 0;
}

.program-card b{
    color:#fff;
}

.program-card small{
    color:#ccc;
}

/* badges */
.badge{
    display:inline-block;
    padding:4px 10px;
    background:#790000;
    color:#fff;
    font-size:11px;
    border-radius:20px;
    margin-right:5px;
}

/* ================= SYSTEM GRID ================= */
.sys-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.sys{
    background:rgba(255,255,255,0.06);
    padding:10px;
    border-radius:12px;
    text-align:center;
    border:1px solid rgba(255,255,255,0.1);
}

/* online status */
.online{
    color:#ff4d4d;
    font-weight:bold;
}

/* status box */
.status{
    display:flex;
    justify-content:space-between;
    background:rgba(255,255,255,0.06);
    padding:12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,0.1);
}

/* ================= LOGOUT ================= */
.logout-btn{
    position:fixed;
    top:20px;
    right:20px;
    padding:10px 18px;
    background:linear-gradient(135deg,#790000,#ff1a1a);
    color:white;
    text-decoration:none;
    font-weight:bold;
    border-radius:25px;
    box-shadow:0 5px 15px rgba(121,0,0,0.4);
    transition:0.3s;
}

.logout-btn:hover{
    transform:scale(1.05);
}

/* ================= RESPONSIVE ================= */
@media(max-width:1000px){
    .container{
        grid-template-columns:1fr;
    }
}
</style>
</head>

<body>

<div class="header">⚙️ ADMIN DASHBOARD - FITMATCH</div>

<div class="container">

<!-- LEFT PANEL -->
<div class="panel">

<h3>📊 SYSTEM STATS</h3>

<div class="stat-grid">
<div class="stat">Users<br><b><?= $userCount ?></b></div>
<div class="stat">Coaches<br><b><?= $coachCount ?></b></div>
<div class="stat">Clients<br><b><?= $clientCount ?></b></div>
<div class="stat">Programs<br><b><?= $programCount ?></b></div>
</div>

<br>

<h3>⚡ QUICK ACTIONS</h3>
<div class="action-grid">

<a class="btn blue" href="view_users.php">👤 View Users</a>
<a class="btn red" href="remove_users.php">❌ Remove Users</a>

<a class="btn sky" href="admin_view_coaches.php">👨‍🏫 View Coaches</a>
<a href="admin_remove_coach.php"
   style="
   display:inline-block;
   padding:12px 20px;
   background:linear-gradient(135deg,#ff4d4d,#b30000);
   color:white;
   border-radius:10px;
   text-decoration:none;
   font-weight:bold;
   font-size:15px;
   box-shadow:0 5px 15px rgba(255,0,0,0.3);
   text-align:center;
   ">
   ❌ Remove Coaches
</a>
<a href="admin_manage_coaches.php"
   style="
   display:inline-block;
   padding:12px 20px;
   background:linear-gradient(135deg,#e53935,#b71c1c);
   color:white;
   border-radius:10px;
   text-decoration:none;
   font-weight:bold;
   font-size:15px;
   box-shadow:0 4px 12px rgba(229,57,53,0.4);
   text-align:center;
   ">
   🧑‍💼 Manage Coaches
</a>

<a href="admin_manage_clients.php"
   style="
   display:inline-block;
   padding:12px 20px;
   background:linear-gradient(135deg,#ff416c,#ff4b2b);
   color:white;
   border-radius:10px;
   text-decoration:none;
   font-weight:bold;
   font-size:15px;
   box-shadow:0 5px 15px rgba(255,0,0,0.3);
   text-align:center;
   ">
   👥 Manage Clients
</a>

</div>

</div>

<!-- MIDDLE PANEL -->
<div class="panel">

<h3>📦 PROGRAM CONTROL</h3>

<a class="btn blue" href="programs.php">📋 View All Programs</a>
<a class="btn green" href="add_program.php">➕ Add Program</a>

<h4>👤 Latest Clients</h4>

<?php while($c = $latestClients->fetch_assoc()){ ?>

<div class="program-card">

<div class="program-info">

<b><?= htmlspecialchars($c['fullname']) ?></b><br>
<small><?= htmlspecialchars($c['email']) ?></small><br>
<small><?= htmlspecialchars($c['phone']) ?></small><br><br>

<span class="badge"><?= $c['level'] ?></span>
<span class="badge"><?= $c['program_name'] ?></span>

<br><br>



</div>
</div>



<?php } ?>

</div>

<!-- RIGHT PANEL -->
<div class="panel">

<h3>⚙️ SYSTEM OVERVIEW</h3>

<div class="status">
<div>Server Status<br><small>Live Monitoring</small></div>
<div class="online">● ONLINE</div>
</div>

<br>

<div class="sys-grid">
<div class="sys">Users<br><b><?= $userCount ?></b></div>
<div class="sys">Coaches<br><b><?= $coachCount ?></b></div>
<div class="sys">Programs<br><b><?= $programCount ?></b></div>
<div class="sys">Chats<br><b><?= $chatCount ?></b></div>
<div class="sys">Progress<br><b><?= $progressCount ?></b></div>
</div>

<br>

<h3>🧠 SYSTEM MODULES</h3>

<div class="sys">AI Coach Engine</div>
<div class="sys">Live Sync Service</div>
<div class="sys">Notification Engine</div>
<div class="sys">Analytics Core</div>

<br>

<a class="btn blue" href="#" onclick="location.reload()">🔄 Sync</a>
<a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Logout</a>
</div>

</div>
<script>
function confirmLogout(){
    return confirm("Are you sure you want to logout?");
}
</script>

</script>
</body>
</html>