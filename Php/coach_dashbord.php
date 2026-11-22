<?php
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coach') {
    header("Location: login.php");
    exit();
}


$coach_id = $_SESSION['user_id'];

/* ================= GET LATEST COACH PROGRAM ================= */
$coachProgram = $conn->query("
    SELECT program_id, status 
    FROM coach_programs 
    WHERE coach_id = $coach_id 
    ORDER BY id DESC 
    LIMIT 1
");

$program_id = 0;
$program_status = 'pending';

if ($coachProgram && $coachProgram->num_rows > 0) {
    $data = $coachProgram->fetch_assoc();
    $program_id = $data['program_id'] ?? 0;
    $program_status = $data['status'] ?? 'pending';
}

/* ================= SAFE CLIENT QUERY ================= */
if ($program_id > 0) {

    $registrations = $conn->query("
        SELECT r.*, p.name AS program_name
        FROM registrations r
        LEFT JOIN programs p ON r.program_id = p.program_id
        WHERE r.program_id = $program_id
        ORDER BY r.reg_id DESC
    ");

} else {
    $registrations = false;
}

/* ================= COUNTS ================= */
$userCount = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='user'")->fetch_assoc()['t'];
$coachCount = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='coach'")->fetch_assoc()['t'];
$programCount = $conn->query("SELECT COUNT(*) AS t FROM programs")->fetch_assoc()['t'];
$progressCount = $conn->query("SELECT COUNT(*) AS t FROM progress")->fetch_assoc()['t'];

/* ================= MESSAGES ================= */
if ($program_id > 0) {

    $messages = $conn->query("
        SELECT c.message, c.created_at, r.fullname, c.sender
        FROM chat_messages c
        LEFT JOIN registrations r ON r.reg_id = c.reg_id
        WHERE r.program_id = $program_id
        ORDER BY c.message_id DESC
    ");

} else {
    $messages = false;
}


/* COUNTS (SAFE) */
$userCount = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='user'")->fetch_assoc()['t'];
$coachCount = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='coach'")->fetch_assoc()['t'];
$programCount = $conn->query("SELECT COUNT(*) AS t FROM programs")->fetch_assoc()['t'];
$progressCount = $conn->query("SELECT COUNT(*) AS t FROM progress")->fetch_assoc()['t'];

/* MESSAGES SAFE */
if($program_id > 0){

    $messages = $conn->query("
        SELECT c.message, c.created_at, r.fullname, c.sender
        FROM chat_messages c
        LEFT JOIN registrations r ON r.reg_id = c.reg_id
        WHERE r.program_id = $program_id
        ORDER BY c.message_id DESC
    ");

} else {
    $messages = false;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Coach Dashboard Ultra Pro</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
/* ================= GLOBAL ================= */
body{
    margin:0;
    font-family:'Poppins', Arial, sans-serif;
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
    width:520px;
    height:520px;
    background:#790000;
    top:-140px;
    left:-140px;
    opacity:0.6;
}

body::after{
    width:480px;
    height:480px;
    background:#a00000;
    bottom:-140px;
    right:-140px;
    opacity:0.4;
}

/* ================= HEADER ================= */
.header{
    position:relative;
    z-index:1;
    padding:18px;
    text-align:center;
    font-size:22px;
    font-weight:700;
    letter-spacing:1px;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border-bottom:1px solid rgba(255,255,255,0.1);
}

/* ================= LAYOUT ================= */
.container{
    position:relative;
    z-index:1;
    display:grid;
    grid-template-columns:1.4fr 1fr;
    gap:15px;
    padding:15px;
}

/* ================= PANELS ================= */
.panel{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:18px;
    padding:15px;
    box-shadow:0 0 25px rgba(121,0,0,0.25);
}

/* ================= HEADINGS ================= */
h3{
    border-left:4px solid #790000;
    padding-left:10px;
    margin-bottom:12px;
    color:#fff;
}

/* ================= CLIENT CARD ================= */
.client-card{
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:14px;
    padding:12px;
    margin-bottom:12px;
    transition:0.3s;
}

.client-card:hover{
    transform:translateY(-3px);
    box-shadow:0 0 20px rgba(121,0,0,0.3);
}

.client-card b{
    color:#fff;
}

.client-card small{
    color:#ccc;
}

/* ================= BADGES ================= */
.badge{
    padding:4px 10px;
    font-size:11px;
    border-radius:20px;
    background:#790000;
    color:#fff;
    margin-right:5px;
}

/* ================= BUTTONS ================= */
.btn{
    display:inline-block;
    padding:8px 12px;
    border-radius:10px;
    font-size:12px;
    text-decoration:none;
    color:white;
    margin-right:5px;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
}

.blue{
    background:linear-gradient(135deg,#790000,#a00000);
}

.green{
    background:linear-gradient(135deg,#a00000,#ff1a1a);
}

.orange{
    background:linear-gradient(135deg,#ff1a1a,#800000);
}

/* ================= STATS ================= */
.stats-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.stat-card{
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:14px;
    padding:14px;
    text-align:center;
    transition:0.3s;
}

.stat-card:hover{
    transform:scale(1.03);
}

.stat-number{
    font-size:22px;
    font-weight:700;
    color:#ff4d4d;
}

.stat-label{
    font-size:12px;
    color:#ccc;
}

/* ================= CHAT ================= */
.chat-box{
    margin-top:10px;
    display:flex;
    flex-direction:column;
    gap:10px;
    max-height:450px;
    overflow:auto;
    padding-right:5px;
}

.msg-bubble{
    display:flex;
    gap:10px;
    padding:10px;
    border-radius:14px;
    background:rgba(255,255,255,0.06);
    border-left:4px solid #790000;
}

.avatar{
    width:34px;
    height:34px;
    border-radius:50%;
    background:linear-gradient(135deg,#790000,#ff1a1a);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:700;
}

.msg-content{
    flex:1;
}

.msg-name{
    font-weight:600;
    font-size:13px;
    color:#ff4d4d;
}

.msg-text{
    font-size:13px;
    color:#ddd;
    margin-top:2px;
}

.msg-time{
    font-size:10px;
    color:#999;
    margin-top:3px;
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
    z-index:10;
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
<a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Logout</a>
<div class="header">🏋️ Coach Dashboard Ultra Pro</div>

<div class="container">

<!-- LEFT -->
<div class="panel">

<h3>👥 Clients</h3>

<?php while($r = $registrations->fetch_assoc()){ ?>

<div class="client-card">

    <b><?= htmlspecialchars($r['fullname']) ?></b><br>
    <small><?= htmlspecialchars($r['email']) ?></small><br><br>

    <span class="badge"><?= $r['level'] ?></span>
    <span class="badge"><?= $r['program_name'] ?></span>

    <br><br>

    <a class="btn blue" href="view_progress.php?reg_id=<?= $r['reg_id'] ?>">📊 Progress</a>
    <a class="btn green" href="chat.php?reg_id=<?= $r['reg_id'] ?>">💬 Chat</a>
    <a class="btn orange" href="coach_manage_workout.php?reg_id=<?= $r['reg_id'] ?>&program_id=<?= $r['program_id'] ?>">🏋️ Manage</a>

</div>

<?php } ?>

</div>

<!-- RIGHT -->
<div class="panel">

<h3>📊 Live Analytics</h3>

<div class="stats-grid">

<div class="stat-card">
<div class="stat-number"><?= $userCount ?></div>
<div class="stat-label">Users</div>
</div>

<div class="stat-card">
<div class="stat-number"><?= $coachCount ?></div>
<div class="stat-label">Coaches</div>
</div>

<div class="stat-card">
<div class="stat-number"><?= $programCount ?></div>
<div class="stat-label">Programs</div>
</div>

<div class="stat-card">
<div class="stat-number"><?= $progressCount ?></div>
<div class="stat-label">Progress</div>
</div>

</div>

<h3>💬 Latest Messages</h3>

<div class="chat-box">

<?php while($m = $messages->fetch_assoc()){ 

    $name = $m['fullname'] ?? 'User';
?>

<div class="msg-bubble">

<div class="avatar">
<?= strtoupper(substr($name,0,1)) ?>
</div>

<div class="msg-content">

<div class="msg-name">
<?= htmlspecialchars($name) ?>
</div>

<div class="msg-text">
<?= htmlspecialchars($m['message']) ?>
</div>

<div class="msg-time">
<?= $m['created_at'] ?>
</div>

</div>

</div>

<?php } ?>

</div>

</div>

</div>
<script>
function confirmLogout(){
    return confirm("Are you sure you want to logout?");
}
</script>
<script>
setInterval(() => {
    fetch("keep_alive.php");
}, 30000); // every 30 seconds
</script>
</body>
</html>