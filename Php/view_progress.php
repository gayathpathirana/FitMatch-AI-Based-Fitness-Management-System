<?php
include "db.php";

/* GET USER ID */
$reg_id = isset($_GET['reg_id']) ? intval($_GET['reg_id']) : 0;

if ($reg_id == 0) {
    die("Invalid user");
}

/* GET USER INFO */
$user = $conn->query("
    SELECT * 
    FROM registrations 
    WHERE reg_id=$reg_id
")->fetch_assoc();

/* GET PROGRESS DATA */
$progress = $conn->query("
    SELECT *
    FROM progress
    WHERE reg_id=$reg_id
    ORDER BY progress_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>User Progress</title>

<style>


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#000;
    color:#fff;
    min-height:100vh;
    padding:20px;
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
    opacity:0.5;
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
    border:1px solid rgba(255,255,255,0.08);
    color:#fff;
    padding:20px;
    text-align:center;
    font-size:24px;
    font-weight:bold;
    border-radius:20px;
    margin-bottom:20px;
    box-shadow:0 0 30px rgba(121,0,0,0.3);
}

/* Main Container */
.container{
    width:95%;
    max-width:1200px;
    margin:auto;
}

/* Cards */
.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    padding:18px;
    margin-bottom:15px;
    border-radius:18px;
    box-shadow:0 0 20px rgba(121,0,0,0.15);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 0 30px rgba(121,0,0,0.35);
}

/* User Info */
.title{
    color:#ff4d4d;
    font-size:20px;
    font-weight:bold;
    margin-bottom:12px;
}

/* Week Header */
.week-header{
    background:rgba(121,0,0,0.25);
    border-left:5px solid #ff4d4d;
    padding:15px;
    border-radius:12px;
    font-size:22px;
    font-weight:bold;
    color:#fff;
    margin:15px 0;
}

/* Day Header */
.day-header{
    background:rgba(121,0,0,0.15);
    border-left:4px solid #ff4d4d;
    padding:12px;
    border-radius:10px;
    font-size:18px;
    font-weight:bold;
    color:#ffcccc;
    margin:10px 0;
}

/* Progress Data */
.card b{
    color:#ff6666;
}

/* Badge */
.badge{
    display:inline-block;
    padding:6px 12px;
    background:#790000;
    color:#fff;
    border-radius:20px;
    font-size:12px;
    margin-top:8px;
}

/* No Data */
p{
    color:#ccc;
    text-align:center;
    margin-top:20px;
    font-size:16px;
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

</style>


</head>

<body>

<div class="header">
📊 Progress Analysis - <?= $user['fullname'] ?>
</div>

<div class="container">

<!-- USER INFO -->
<div class="card">
<div class="title">User Info</div>
Name: <?= $user['fullname'] ?><br>
Level: <?= $user['level'] ?><br>
Email: <?= $user['email'] ?>
</div>
<!-- PROGRESS LIST -->

<div class="card">
<div class="title">Saved Progress</div>

<?php
if($progress->num_rows > 0){

    $currentWeek = 0;
    $currentDay = "";

    while($p = $progress->fetch_assoc()){

        // NEW WEEK HEADER
        if($currentWeek != $p['week_number']){
            $currentWeek = $p['week_number'];
            $currentDay = ""; // reset day when week changes

            echo "
            <div class='week-header'>
                📊 Week {$currentWeek}
            </div>
            ";
        }

        // NEW DAY HEADER
        if($currentDay != $p['day']){
            $currentDay = $p['day'];

            echo "
            <div class='day-header'>
                📅 {$currentDay}
            </div>
            ";
        }
?>

<div class="card">

    <b>Type:</b> <?= $p['metric_type'] ?><br><br>
    <b>Before:</b> <?= $p['before_value'] ?><br><br>
    <b>After:</b> <?= $p['after_value'] ?><br><br>
    <b>Note:</b> <?= $p['note'] ?? '-' ?>

</div>

<?php } ?>

<?php } else { ?>
    <p>No progress data found.</p>
<?php } ?>

</div>


</body>
</html>