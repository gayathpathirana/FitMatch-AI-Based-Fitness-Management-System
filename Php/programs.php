<?php
include "db.php";

/* SAFE SESSION FIX (IMPORTANT) */
if(!isset($_SESSION['role'])){
    $_SESSION['role'] = 'user';
}

/* GET PROGRAMS */
$sql = "SELECT program_id, name, short_text, description, image FROM programs";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>FitMatch Programs</title>

<style>
/* ================= GLOBAL ================= */
body{
    font-family:Arial;
    background:#000;
    margin:0;
    padding:20px;
    color:#fff;
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

/* ================= TITLE ================= */
h1{
    text-align:center;
    margin-bottom:30px;
    color:#fff;
    position:relative;
    z-index:1;
    font-size:26px;
    letter-spacing:1px;
}

/* ================= GRID ================= */
.grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:20px;
    position:relative;
    z-index:1;
}

/* ================= CARD ================= */
.card{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:16px;
    padding:15px;
    box-shadow:0 0 25px rgba(121,0,0,0.2);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 0 35px rgba(121,0,0,0.4);
}

/* ================= IMAGE ================= */
.image-box{
    width:100%;
    height:170px;
    border-radius:12px;
    overflow:hidden;
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
}

.image-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.no-image{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100%;
    color:#aaa;
    font-size:14px;
}

/* ================= TEXT ================= */
.title{
    font-size:17px;
    font-weight:bold;
    margin-top:10px;
    color:#fff;
}

.short{
    font-size:13px;
    color:#ccc;
    margin-top:6px;
}

/* ================= BUTTONS ================= */
.btn{
    display:block;
    width:100%;
    padding:10px;
    margin-top:10px;
    border-radius:10px;
    text-align:center;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
    border:none;
    cursor:pointer;
    box-sizing:border-box;
}

/* view button */
.view{
    background:linear-gradient(135deg,#790000,#a00000);
    color:white;
}

.view:hover{
    transform:scale(1.03);
}

/* register button */
.register{
    background:linear-gradient(135deg,#ff1a1a,#790000);
    color:white;
    display:block;
    width:100%;
    box-sizing:border-box;
}

.register:hover{
    transform:scale(1.03);
}

/* ================= MODAL ================= */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.8);
    justify-content:center;
    align-items:center;
    z-index:999;
}

.modal-content{
    background:rgba(20,20,20,0.95);
    padding:20px;
    width:60%;
    border-radius:16px;
    border:1px solid rgba(255,255,255,0.1);
    color:#fff;
}

.close{
    float:right;
    cursor:pointer;
    font-size:18px;
    color:#ff4d4d;
}

/* ================= RESPONSIVE ================= */
@media(max-width:1000px){
    .grid{
        grid-template-columns:1fr;
    }

    .modal-content{
        width:90%;
    }
}
</style>

<script>
function openModal(id){
    document.getElementById("modal"+id).style.display="flex";
}

function closeModal(id){
    document.getElementById("modal"+id).style.display="none";
}
</script>

</head>

<body>

<h1>Available Fitness Programs</h1>

<div class="grid">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

    <div class="card">

   <!-- IMAGE -->
<div class="image-box">

<?php 
$img = $row['image'];

if (empty($img)) {
    $img = "bulk.png";
}
?>

<img src="../images/<?php echo $img; ?>" alt="Program Image">

</div>
        <!-- TITLE -->
        <div class="title">
            <?php echo $row['name']; ?>
        </div>

        <!-- SHORT -->
        <div class="short">
            <?php echo $row['short_text']; ?>
        </div>

        <!-- VIEW -->
        <button class="btn view"
            onclick="openModal(<?php echo $row['program_id']; ?>)">
            View More
        </button>

        <!-- REGISTER (FIXED ROLE CHECK ONLY) -->
       <?php if($_SESSION['role'] == 'coach'){ ?>

    <a class="btn register"
       href="coach_register.php?program_id=<?php echo $row['program_id']; ?>">
        Coach Register
    </a>

<?php } else { ?>

    <a class="btn register"
       href="register_form.php?program_id=<?php echo $row['program_id']; ?>">
        Register
    </a>

<?php } ?>

    </div>

    <div class="modal" id="modal<?php echo $row['program_id']; ?>">
        <div class="modal-content">
            <span class="close" onclick="closeModal(<?php echo $row['program_id']; ?>)">X</span>
            <h2><?php echo $row['name']; ?></h2>
            <p><?php echo nl2br($row['description']); ?></p>
        </div>
    </div>

<?php } ?>

</div>

</body>
</html>