<?php
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST['register'])){

    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $error = null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long!";
    } elseif (!in_array($role, ['user', 'coach', 'admin'])) {
        $error = "Invalid role selected!";
    } else {
        // check if email exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if($result->num_rows > 0){
            $error = "Email already exists!";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users(username, email, password, role, first_login)
                VALUES (?, ?, ?, ?, 1)
            ");

            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

            if($stmt->execute()){
                header("Location: login.php");
                exit();
            } else {
                $error = "Error creating account";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>FitMatch Signup</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#000;
    overflow:hidden;
}

/* glow background */
body::before,
body::after{
    content:"";
    position:absolute;
    border-radius:50%;
    filter:blur(140px);
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

/* form box */
form{
    position:relative;
    width:380px;
    padding:45px;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(18px);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:18px;
    box-shadow: 0 0 35px rgba(121,0,0,0.4);
    text-align:center;
    z-index:1;
}

/* title */
form h2{
    color:#fff;
    font-size:22px;
    margin-bottom:25px;
    letter-spacing:2px;
}

/* inputs + select */
input, select{
    width:100%;
    padding:13px;
    margin:10px 0;
    border:none;
    outline:none;
    border-radius:10px;
    background: rgba(255,255,255,0.08);
    color:#fff;
    transition:0.3s;
}

select option{
    color:#000;
}

input:focus, select:focus{
    background: rgba(121,0,0,0.25);
    box-shadow: 0 0 12px #790000;
}

/* button */
button{
    width:100%;
    padding:13px;
    margin-top:18px;
    border:none;
    border-radius:10px;
    background: linear-gradient(135deg, #790000, #a00000);
    color:#fff;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.04);
    box-shadow:0 0 20px rgba(121,0,0,0.7);
}

/* error */
p{
    margin-top:15px;
    color:#ff4d4d;
    font-size:14px;
}

/* placeholder */
input::placeholder{
    color:#ccc;
}
</style>

</head>

<body>

<form method="POST">
    <h2>FITMATCH<br>SIGNUP</h2>

    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="role">
        <option value="user">Client</option>
        <option value="coach">Coach</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit" name="register">CREATE ACCOUNT</button>

    <?php if(isset($error)) echo "<p>$error</p>"; ?>
</form>

</body>
</html>