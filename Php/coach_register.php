<?php 
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* LOGIN CHECK */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coach'){
    header("Location: login.php");
    exit();
}

$coach_id = $_SESSION['user_id'];

$program_id = intval($_GET['program_id'] ?? 0);

if($program_id == 0){
    die("Invalid Program");
}

/* GET PROGRAM */
$result = $conn->query("SELECT * FROM programs WHERE program_id=$program_id");

if(!$result || $result->num_rows == 0){
    die("Program not found");
}

$program = $result->fetch_assoc();

/* CHECK IF ALREADY REGISTERED */
$check = $conn->query("
    SELECT id 
    FROM coach_programs 
    WHERE coach_id=$coach_id 
    AND program_id=$program_id 
    LIMIT 1
");

/* ================= SAVE ================= */
if(isset($_POST['save'])){

    if($check && $check->num_rows > 0){
        echo "<script>alert('Already submitted');</script>";
    } else {

        $experience = htmlspecialchars(trim($_POST['experience']));
        $speciality = htmlspecialchars(trim($_POST['speciality']));
        $notes = htmlspecialchars(trim($_POST['notes']));
        $status = 'pending';

        if (empty($experience) || empty($speciality)) {
            echo "<script>alert('Experience and Speciality are required');</script>";
        } else {

            $stmt = $conn->prepare("
                INSERT INTO coach_programs
                (coach_id, program_id, experience, speciality, notes, status)
                VALUES
                (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iissss", $coach_id, $program_id, $experience, $speciality, $notes, $status);

            if($stmt->execute()){

                /* OPTIONAL: mark coach as pending in users table */
                $update_stmt = $conn->prepare("
                    UPDATE users 
                    SET coach_status = 'pending'
                    WHERE user_id = ?
                ");
                $update_stmt->bind_param("i", $coach_id);
                $update_stmt->execute();

                /* 🚀 DIRECT DASHBOARD */
                header("Location: coach_dashbord.php");
                exit();

            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Coach Register</title>

<style>
body{
    margin:0;
    padding:40px;
    font-family:Poppins, Arial, sans-serif;
    background:#000;
    color:#fff;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow-x:hidden;
    position:relative;
}

/* Background Glow */
body::before,
body::after{
    content:"";
    position:fixed;
    border-radius:50%;
    filter:blur(140px);
    z-index:-1;
}

body::before{
    width:520px;
    height:520px;
    background:#790000;
    top:-150px;
    left:-150px;
    opacity:.55;
}

body::after{
    width:480px;
    height:480px;
    background:#c40000;
    bottom:-140px;
    right:-140px;
    opacity:.35;
}

/* Main Card */

.container{
    width:100%;
    max-width:650px;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.08);
    border-radius:20px;
    padding:35px;
    box-shadow:
        0 0 30px rgba(150,0,0,.35),
        0 10px 35px rgba(0,0,0,.5);
}

/* Heading */

h2{
    margin-top:0;
    margin-bottom:30px;
    text-align:center;
    color:#ff4d4d;
    font-size:28px;
    font-weight:700;
    letter-spacing:.5px;
}

/* Inputs */

input,
textarea{
    width:100%;
    padding:14px 16px;
    margin:12px 0;
    box-sizing:border-box;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(255,255,255,.06);
    color:#fff;
    font-size:15px;
    outline:none;
    transition:.3s;
}

input::placeholder,
textarea::placeholder{
    color:#bdbdbd;
}

textarea{
    min-height:120px;
    resize:vertical;
}

/* Focus */

input:focus,
textarea:focus{
    border-color:#ff3333;
    box-shadow:
        0 0 12px rgba(255,0,0,.45);
    background:rgba(255,255,255,.08);
}

/* Button */

button{
    width:100%;
    padding:15px;
    margin-top:15px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-size:16px;
    font-weight:700;
    color:#fff;
    transition:.3s;
}

/* Submit */

.submit{
    background:linear-gradient(135deg,#790000,#ff1a1a);
    box-shadow:0 6px 18px rgba(255,0,0,.30);
}

.submit:hover{
    transform:translateY(-2px);
    box-shadow:
        0 10px 25px rgba(255,0,0,.50);
}

.submit:active{
    transform:scale(.98);
}

/* Scrollbar */

::-webkit-scrollbar{
    width:8px;
}

::-webkit-scrollbar-thumb{
    background:#8b0000;
    border-radius:20px;
}

::-webkit-scrollbar-track{
    background:#111;
}

/* Responsive */

@media(max-width:768px){

    body{
        padding:20px;
    }

    .container{
        width:100%;
        padding:25px;
    }

    h2{
        font-size:22px;
    }

}
</style>

</head>

<body>

<div class="container">

<h2>🏋️ Coach Register for <?= htmlspecialchars($program['name']) ?></h2>

<form method="POST">

    <input type="text" name="experience" placeholder="Experience" required>

    <input type="text" name="speciality" placeholder="Speciality" required>

    <textarea name="notes" placeholder="Notes"></textarea>

    <button type="submit" name="save" class="submit">
        📩 Submit
    </button>

</form>

</div>

</body>
</html>