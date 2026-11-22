<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['program_id'])) {
    $_SESSION['program_id'] = intval($_GET['program_id']);
}

if (!isset($_SESSION['program_id'])) {
    header("Location: programs.php");
    exit();
}

$program_id = $_SESSION['program_id'];

$result = $conn->query("SELECT * FROM programs WHERE program_id=$program_id");
if (!$result || $result->num_rows == 0) {
    die("Invalid program");
}
$program = $result->fetch_assoc();

if (isset($_POST['save'])) {

    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $age = $_POST['age'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $level = $_POST['level'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $experience = $_POST['experience'] ?? '';

    $weight = $_POST['weight'] ?? '';
    $height = $_POST['height'] ?? '';
    $body_fat = $_POST['body_fat'] ?? '';
    $waist = $_POST['waist'] ?? '';
    $chest = $_POST['chest'] ?? '';
    $bmi = $_POST['bmi'] ?? '';

    $workout_time = $_POST['workout_time'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $body_goal = $_POST['body_goal'] ?? '';
    $dream_physique = $_POST['dream_physique'] ?? '';
    $fitness_goal = $_POST['fitness_goal'] ?? '';
    $notes = $_POST['notes'] ?? '';

   $sql = "INSERT INTO registrations (
    user_id, program_id, fullname, email, age, phone, level, gender, address, experience,
    weight, height, body_fat, waist, chest, bmi,
    workout_time, budget, body_goal, dream_physique, fitness_goal, notes, approval_status
) VALUES (
    '$user_id', '$program_id', '$fullname', '$email', '$age', '$phone', '$level', '$gender', '$address', '$experience',
    '$weight', '$height', '$body_fat', '$waist', '$chest', '$bmi',
    '$workout_time', '$budget', '$body_goal', '$dream_physique', '$fitness_goal', '$notes', 'pending'
)";

   if ($conn->query($sql)) {

    // 🔥 GET LAST INSERT ID
    $reg_id = $conn->insert_id;

    // (OPTIONAL) update not needed because already inserted as pending

    unset($_SESSION['program_id']);

    // 🔥 FIX: PASS REG_ID TO NEXT PAGE
    header("Location: waiting_payment_approval.php?reg_id=$reg_id");
    exit();
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Fitness Registration</title>

<style>

body{
    margin:0;
    font-family:Poppins, Arial, sans-serif;
    background:#000;
    color:#fff;
}

/* glowing background */
body::before,
body::after{
    content:"";
    position:fixed;
    border-radius:50%;
    filter:blur(150px);
    z-index:-1;
}

body::before{
    width:600px;
    height:600px;
    background:#790000;
    top:-180px;
    left:-180px;
    opacity:.55;
}

body::after{
    width:520px;
    height:520px;
    background:#ff1a1a;
    bottom:-180px;
    right:-180px;
    opacity:.30;
}

/* HEADER */
.header{
    text-align:center;
    padding:40px 20px;
    background:rgba(255,255,255,0.04);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,0.08);
    box-shadow:0 0 25px rgba(255,0,0,0.2);
}

.header h1{
    margin:0;
    font-size:30px;
    color:#ff4d4d;
    letter-spacing:1px;
}

.header p{
    margin-top:8px;
    color:#ddd;
    font-size:14px;
}

/* MAIN CONTAINER */
.container{
    width:85%;
    max-width:1100px;
    margin:40px auto;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:20px;
    padding:30px;
    box-shadow:
        0 0 35px rgba(150,0,0,0.35),
        0 10px 40px rgba(0,0,0,0.6);
}

/* SECTIONS */
.section{
    margin-top:25px;
    padding:20px;
    border-radius:14px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.06);
}

.section h3{
    margin-bottom:15px;
    color:#ff4d4d;
    font-size:18px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}

/* FIELD */
.field{
    display:flex;
    flex-direction:column;
}

.field label{
    font-size:12px;
    margin-bottom:6px;
    color:#ccc;
    letter-spacing:.5px;
}

/* INPUT */
input{
    padding:12px 14px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,0.12);
    background:rgba(0,0,0,0.4);
    color:#fff;
    outline:none;
    transition:.3s;
}

input::placeholder{
    color:#888;
}

/* FOCUS */
input:focus{
    border-color:#ff1a1a;
    box-shadow:0 0 12px rgba(255,0,0,0.5);
    background:rgba(255,255,255,0.06);
}

/* BUTTON */
button{
    width:100%;
    padding:16px;
    margin-top:25px;
    border:none;
    border-radius:12px;
    font-size:16px;
    font-weight:bold;
    color:#fff;
    cursor:pointer;
    background:linear-gradient(135deg,#790000,#ff1a1a);
    box-shadow:0 8px 25px rgba(255,0,0,0.35);
    transition:.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 30px rgba(255,0,0,0.5);
}

button:active{
    transform:scale(0.98);
}

/* RESPONSIVE */
@media(max-width:768px){
    .container{
        width:95%;
        padding:20px;
    }

    .grid{
        grid-template-columns:1fr;
    }

    .header h1{
        font-size:24px;
    }
}

/* scrollbar */
::-webkit-scrollbar{
    width:8px;
}

::-webkit-scrollbar-thumb{
    background:#8b0000;
    border-radius:10px;
}

::-webkit-scrollbar-track{
    background:#111;
}

</style>
</head>

<body>

<div class="header">
    <h1>Join Fitness Program</h1>
    <p><?php echo $program['name']; ?></p>
</div>

<div class="container">

<form method="POST">

<div class="section">
<h3>Personal Details</h3>
<div class="grid">

<div class="field"><label>Full Name</label><input type="text" name="fullname" required></div>
<div class="field"><label>Email</label><input type="email" name="email" required></div>
<div class="field"><label>Age</label><input type="number" name="age"></div>
<div class="field"><label>Phone</label><input type="text" name="phone"></div>
<div class="field"><label>Level</label><input type="text" name="level"></div>
<div class="field"><label>Gender</label><input type="text" name="gender"></div>
<div class="field"><label>Address</label><input type="text" name="address"></div>
<div class="field"><label>Experience</label><input type="text" name="experience"></div>

</div>
</div>

<div class="section">
<h3>Body Stats</h3>
<div class="grid">

<div class="field"><label>Weight</label><input type="text" name="weight"></div>
<div class="field"><label>Height</label><input type="text" name="height"></div>
<div class="field"><label>Body Fat</label><input type="text" name="body_fat"></div>
<div class="field"><label>Waist</label><input type="text" name="waist"></div>
<div class="field"><label>Chest</label><input type="text" name="chest"></div>
<div class="field"><label>BMI</label><input type="text" name="bmi"></div>

</div>
</div>

<div class="section">
<h3>Fitness Goals</h3>
<div class="grid">

<div class="field"><label>Workout Time</label><input type="text" name="workout_time"></div>
<div class="field"><label>Budget</label><input type="text" name="budget"></div>
<div class="field"><label>Body Goal</label><input type="text" name="body_goal"></div>
<div class="field"><label>Dream Physique</label><input type="text" name="dream_physique"></div>
<div class="field"><label>Fitness Goal</label><input type="text" name="fitness_goal"></div>
<div class="field"><label>Notes</label><input type="text" name="notes"></div>

</div>
</div>

<button type="submit" name="save">Start My Transformation</button>

</form>

</div>

</body>
</html>