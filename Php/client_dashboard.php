<?php
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];

/*  GET LATEST REGISTRATION..  */
$stmt = $conn->prepare("
    SELECT reg_id, program_id, approval_status
    FROM registrations
    WHERE user_id = ?
    ORDER BY reg_id DESC
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No registration found");
}

$row = $result->fetch_assoc();

$reg_id = $row['reg_id'];
$program_id = $row['program_id'];
$approval_status = $row['approval_status'];

if ($approval_status != 'approved') {
    header("Location: waiting_payment_approval.php");
    exit();
}


/* ================= GET REGISTRATION DETAILS ================= */
$regData = $conn->prepare("
    SELECT *
    FROM registrations
    WHERE reg_id = ?
");

$regData->bind_param("i", $reg_id);
$regData->execute();
$resultReg = $regData->get_result();

if($resultReg->num_rows == 0){
    die("Registration not found");
}

$registration = $resultReg->fetch_assoc();

/* ================= GET PROGRAM DETAILS ================= */
$programData = $conn->prepare("
    SELECT *
    FROM programs
    WHERE program_id = ?
");

$programData->bind_param("i", $program_id);
$programData->execute();
$resultProg = $programData->get_result();

if($resultProg->num_rows == 0){
    die("Program not found");
}

$program = $resultProg->fetch_assoc();

/* SAVE PROGRESS */
$msg = "";

if(isset($_POST['save_progress'])){

    /* GET CURRENT WEEK */
    $q = $conn->query("
        SELECT MAX(week_number) AS last_week 
        FROM progress 
        WHERE reg_id = $reg_id
    ");
    $r = $q->fetch_assoc();

    $lastWeek = $r['last_week'] ?? 0;

    /* CHECK IF WEEK IS COMPLETE (Sunday exists) */
    $check = $conn->query("
        SELECT COUNT(*) AS cnt 
        FROM progress 
        WHERE reg_id = $reg_id
        AND week_number = $lastWeek 
        AND day = 'Sunday'
    ");

    $c = $check->fetch_assoc()['cnt'] ?? 0;

    /* DECIDE WEEK */
    if($lastWeek == 0){
        $week = 1;
    }
    else if($c > 0){
        $week = $lastWeek + 1;
    }
    else{
        $week = $lastWeek;
    }

    /* SAVE DATA */
    foreach($_POST['metric_type'] as $day => $type){

        $before = $_POST['before'][$day] ?? '';
        $after  = $_POST['after'][$day] ?? '';
        $note   = $_POST['note'][$day] ?? '';

        if($before != '' || $after != '' || $note != ''){

            $conn->query("
                INSERT INTO progress (
                    reg_id,
                    program_id,
                    day,
                    metric_type,
                    before_value,
                    after_value,
                    note,
                    week_number
                )
                VALUES (
                    $reg_id,
                    $program_id,
                    '$day',
                    '$type',
                    '$before',
                    '$after',
                    '$note',
                    $week
                )
            ");
        }
    }

    echo "<script>alert('Weekly Progress Saved');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Client Dashboard</title>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(15px);
    padding:14px;
    text-align:center;
    font-size:18px;
    font-weight:bold;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

/* sub header */
.subheader{
    position:relative;
    z-index:1;
    background:rgba(255,255,255,0.05);
    padding:10px;
    text-align:center;
    font-size:13px;
    color:#ccc;
}

/* ================= LAYOUT ================= */
.container{
    position:relative;
    z-index:1;
    display:flex;
    gap:12px;
    padding:12px;
    min-height:85vh;
}

/* panels */
.panel{
    flex:1;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:16px;
    padding:15px;
    overflow-y:auto;
}

/* middle highlight */
.middle{
    background:rgba(121,0,0,0.08);
}

/* ================= HEADINGS ================= */
h3{
    border-left:4px solid #790000;
    padding-left:10px;
    margin-bottom:10px;
}

h4{
    color:#ff4d4d;
}

/* ================= CARDS ================= */
.card{
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:14px;
    padding:14px 16px;
    margin:10px 0;
    transition: all 0.35s ease;
    position: relative;
    overflow: hidden;
}

/* ---- DONE state ---- */
.card.is-done{
    background: rgba(0, 180, 90, 0.10);
    border-color: rgba(0, 220, 100, 0.35);
    box-shadow: 0 0 18px rgba(0, 220, 100, 0.15);
}

.card.is-done .workout-title,
.card.is-done .workout-meta{
    text-decoration: line-through;
    opacity: 0.55;
}

/* ---- Completed badge ---- */
.done-badge{
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: linear-gradient(135deg, #00b45a, #00e676);
    color: #000;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.8px;
    padding: 3px 10px;
    border-radius: 20px;
    margin-bottom: 6px;
    text-transform: uppercase;
    animation: popIn 0.3s ease;
}

@keyframes popIn{
    from{ transform:scale(0.5); opacity:0; }
    to  { transform:scale(1);   opacity:1; }
}

/* ---- Pass button ---- */
.pass-form{ margin:0; }

.pass-btn{
    width: auto;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 22px;
    margin-top: 12px;
    border: none;
    border-radius: 30px;
    background: linear-gradient(135deg, #790000, #c0392b);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(121,0,0,0.4);
    text-transform: uppercase;
}

.pass-btn:hover{
    background: linear-gradient(135deg, #a00000, #e74c3c);
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 6px 20px rgba(200,0,0,0.5);
}

.pass-btn.undo{
    background: linear-gradient(135deg, #1a6e3c, #27ae60);
    box-shadow: 0 4px 14px rgba(0,180,90,0.35);
}

.pass-btn.undo:hover{
    background: linear-gradient(135deg, #239a52, #2ecc71);
    box-shadow: 0 6px 20px rgba(0,200,100,0.45);
}

.workout-title{ font-weight:700; font-size:15px; margin-bottom:4px; }
.workout-meta { font-size:13px; color:#ccc; margin-bottom:2px; }

/* ================= INPUTS ================= */
input, textarea, select{
    width:100%;
    padding:10px;
    margin-top:6px;
    border:none;
    outline:none;
    border-radius:10px;
    background:rgba(255,255,255,0.08);
    color:#fff;
}

select option{
    color:#000;
}

input:focus, textarea:focus, select:focus{
    box-shadow:0 0 12px #790000;
    background:rgba(121,0,0,0.25);
}

/* ================= BUTTON ================= */
button{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#790000,#a00000);
    color:#fff;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.03);
}

/* ================= CHAT ================= */
.chat-box{
    flex:1;
    min-height:260px;
    overflow-y:auto;
    border:1px solid rgba(255,255,255,0.1);
    padding:10px;
    border-radius:10px;
    background:rgba(0,0,0,0.3);
}

.msg{
    padding:8px;
    margin:6px;
    border-radius:10px;
    max-width:75%;
    word-wrap:break-word;
}

.left{
    float:left;
    background:#333;
}

.right{
    float:right;
    background:#790000;
    color:white;
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
    border-radius:25px;
    font-weight:bold;
    transition:0.3s;
    z-index:10;
}

.logout-btn:hover{
    transform:scale(1.05);
}

/* ================= RESPONSIVE ================= */
@media(max-width:1000px){
    .container{
        flex-direction:column;
    }
}

</style>
</head>

<body>

<div class="header">
🏋️ FitMatch User Dashboard - <?= $program['name'] ?>
</div>

<div class="subheader">
👤 Name: <?= htmlspecialchars($registration['fullname'] ?? '') ?> |
📧 Email: <?= htmlspecialchars($registration['email'] ?? '') ?> |
📊 Level: <?= htmlspecialchars($registration['level'] ?? '') ?>
</div>
<div class="container">

<!-- LEFT -->

<div class="panel">

<h3>Workout Plan</h3>

<?php
$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

foreach($days as $day){

    // WORKOUT
    $workouts = $conn->query("
        SELECT * FROM workout_plan
        WHERE reg_id=$reg_id AND type='workout' AND day='$day'
        ORDER BY id ASC
    ");

    // NUTRITION
    $nutrition = $conn->query("
        SELECT * FROM workout_plan
        WHERE reg_id=$reg_id AND type='nutrition' AND day='$day'
        ORDER BY id ASC
    ");

    // RECOVERY
    $recovery = $conn->query("
        SELECT * FROM workout_plan
        WHERE reg_id=$reg_id AND type='recovery' AND day='$day'
        ORDER BY id ASC
    ");

    // skip empty days
    if($workouts->num_rows == 0 && $nutrition->num_rows == 0 && $recovery->num_rows == 0){
        continue;
    }

   echo "<h3 style='margin-top:15px;color:#ff4d4d;border-left:4px solid #790000;padding-left:10px;'>📅 $day</h3>";

    // WORKOUT
    while($w = $workouts->fetch_assoc()){
        $isDone = !empty($w['is_done']);
?>
        <div class="card <?= $isDone ? 'is-done' : '' ?>">
            <?php if($isDone): ?>
                <div class="done-badge">✓ Completed</div>
            <?php endif; ?>
            <div class="workout-title"><?= htmlspecialchars($w['title']) ?></div>
            <div class="workout-meta"><?= htmlspecialchars($w['workout_name'] ?? '') ?></div>
            <div class="workout-meta"><?= $w['sets'] ?> sets &times; <?= $w['reps'] ?> reps</div>

            <form method="POST" action="tick_workout.php" class="pass-form">
                <input type="hidden" name="id" value="<?= $w['id'] ?>">
                <input type="hidden" name="reg_id" value="<?= $reg_id ?>">
                <button type="submit" class="pass-btn <?= $isDone ? 'undo' : '' ?>">
                    <?= $isDone ? '↩ Undo' : '✔ Done' ?>
                </button>
            </form>
        </div>
<?php } ?>

<?php while($n = $nutrition->fetch_assoc()){
    $isDone = ($n['is_done'] == 1);
?>
        <div class="card <?= $isDone ? 'is-done' : '' ?>">
            <?php if($isDone): ?>
                <div class="done-badge">✓ Completed</div>
            <?php endif; ?>
            <div class="workout-title"><?= htmlspecialchars($n['title']) ?></div>
            <div class="workout-meta"><?= nl2br(htmlspecialchars($n['workout_name'] ?? '')) ?></div>

            <form method="POST" action="tick_workout.php" class="pass-form">
                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                <input type="hidden" name="reg_id" value="<?= $reg_id ?>">
                <button type="submit" class="pass-btn <?= $isDone ? 'undo' : '' ?>">
                    <?= $isDone ? '↩ Undo' : '✔ Done' ?>
                </button>
            </form>
        </div>
<?php } ?>

<?php while($r = $recovery->fetch_assoc()){
    $isDone = ($r['is_done'] == 1);
?>
        <div class="card <?= $isDone ? 'is-done' : '' ?>">
            <?php if($isDone): ?>
                <div class="done-badge">✓ Completed</div>
            <?php endif; ?>
            <div class="workout-title"><?= htmlspecialchars($r['title']) ?></div>
            <div class="workout-meta"><?= htmlspecialchars($r['workout_name'] ?? '') ?></div>

            <form method="POST" action="tick_workout.php" class="pass-form">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <input type="hidden" name="reg_id" value="<?= $reg_id ?>">
                <button type="submit" class="pass-btn <?= $isDone ? 'undo' : '' ?>">
                    <?= $isDone ? '↩ Undo' : '✔ Done' ?>
                </button>
            </form>
        </div>
<?php } ?>

<?php } ?>

</div>


<!-- MIDDLE -->
<div class="panel middle">

<h3>📊 Weekly Progress Tracker</h3>

<form method="POST">

<?php
$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

foreach($days as $day){
?>

<div class="card">
    <b>📅 <?= $day ?></b><br><br>

    <select name="metric_type[<?= $day ?>]">
        <option value="weight">Weight</option>
        <option value="speed">Speed</option>
        <option value="pr">PR</option>
        <option value="strength">Strength</option>
    </select>

    <br><br>

    Before Workout:
    <input type="text" name="before[<?= $day ?>]" placeholder="Before value">

    After Workout:
    <input type="text" name="after[<?= $day ?>]" placeholder="After value">

    Note:
<input type="text" name="note[<?= $day ?>]" placeholder="Small note (optional)">
</div>

<?php } ?>

<button type="submit" name="save_progress">💾 Save Weekly Progress</button>

</form>
<h3>📈 Weekly History</h3>

<?php
$result = $conn->query("
    SELECT * FROM progress
    WHERE reg_id=$reg_id
    ORDER BY week_number ASC,
    FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
");

$currentWeek = 0;
$currentDay = "";

while($p = $result->fetch_assoc()){

    /* NEW WEEK HEADER */
    if($currentWeek != $p['week_number']){
        $currentWeek = $p['week_number'];
        $currentDay = "";

       echo "<h3 style='color:#fff;margin-top:15px;border-left:4px solid #790000;padding-left:10px;'>📊 Week $currentWeek</h3>";
    }

    /* NEW DAY HEADER */
    if($currentDay != $p['day']){
        $currentDay = $p['day'];

        echo "<h4 style='color:#4facfe;margin-left:10px;'>📅 {$p['day']}</h4>";
    }
?>

<div class="card">
    <b><?= $p['metric_type'] ?></b><br>
    Before: <?= $p['before_value'] ?><br>
    After: <?= $p['after_value'] ?><br>
    Note: <?= $p['note'] ?>
</div>

<?php } ?>

</div> 

<div class="panel">

    <h3>💬 Chat</h3>

    <div class="chat-box" id="chatBox"></div>

    <textarea id="msg" placeholder="Type message..."></textarea>

    <button type="button" onclick="sendMsg()">Send</button>

</div> <!-- END RIGHT PANEL -->

</div> <!-- END CONTAINER -->
<a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Logout</a>
<script>

function loadChat(){
    $("#chatBox").load("load_chat.php?reg_id=<?= $reg_id ?>");
}

setInterval(loadChat, 1500);
loadChat();

function sendMsg(){
    let msg = $("#msg").val();
    if(msg.trim() == "") return;

    $.post("send_message.php",{
        reg_id: <?= $reg_id ?>,
        message: msg,
        role: "user"
    },function(){
        $("#msg").val("");
        loadChat();
    });
}

</script>
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