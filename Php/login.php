<?php
/* ================= SESSION FIX (ONLY ADDITION - DO NOT CHANGE LOGIC) ================= */
if (session_status() === PHP_SESSION_NONE) {
    /* make session stable */
    ini_set('session.gc_maxlifetime', 86400);
    ini_set('session.cookie_lifetime', 86400);

    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

if (isset($_POST['login'])) {

    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email and password are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            $isValid = false;

            if (password_get_info($user['password'])['algo']) {
                $isValid = password_verify($password, $user['password']);
            } else {
                $isValid = ($password === $user['password']);
            }

            if ($isValid) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                }

                if ($user['role'] === 'coach') {

                    if ($user['is_verified'] == 0) {
                        header("Location: waiting_approval.php");
                        exit();
                    }
                    $coach_id = intval($user['user_id']);

                    $check = $conn->query("
                    SELECT id 
                    FROM coach_programs 
                    WHERE coach_id = $coach_id 
                    LIMIT 1
                ");

                    if ($check && $check->num_rows > 0) {
                        header("Location: coach_dashbord.php");
                    } else {
                        header("Location: programs.php");
                    }

                    exit();
                }

                if ($user['role'] === 'user') {

                    $user_id = intval($user['user_id']);

                    $check = $conn->prepare("
                    SELECT reg_id, approval_status
                    FROM registrations
                    WHERE user_id = ?
                    ORDER BY reg_id DESC
                    LIMIT 1
                ");

                    $check->bind_param("i", $user_id);
                    $check->execute();
                    $result2 = $check->get_result();

                    if ($result2->num_rows > 0) {

                        $reg = $result2->fetch_assoc();
                        $reg_id = $reg['reg_id'];

                        if ($reg['approval_status'] === 'approved') {
                            header("Location: client_dashboard.php");
                        } else {
                            header("Location: waiting_approval.php?reg_id=$reg_id");
                        }

                        exit();

                    } else {
                        header("Location: programs.php");
                        exit();
                    }
                }

            } else {
                $error = "Wrong password";
            }

        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>FitMatch Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #000;
            overflow: hidden;
        }

        /* glowing background */
        body::before,
        body::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
        }

        body::before {
            width: 600px;
            height: 600px;
            background: #790000;
            top: -100px;
            left: -150px;
            opacity: 0.6;
        }

        body::after {
            width: 500px;
            height: 500px;
            background: #790000;
            bottom: -120px;
            right: -120px;
            opacity: 0.4;
        }

        /* login box */
        form {
            position: relative;
            width: 350px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(121, 0, 0, 0.4);
            text-align: center;
            z-index: 1;
        }

        /* title */
        form h2 {
            color: #fff;
            margin-bottom: 25px;
        }

        /* inputs */
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            outline: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        /* focus */
        input:focus {
            background: rgba(121, 0, 0, 0.2);
            box-shadow: 0 0 10px #790000;
        }

        /* button */
        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 10px;
            background: #790000;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #a00000;
            transform: scale(1.03);
        }

        /* error */
        p {
            margin-top: 15px;
            color: #ff4d4d;
        }
    </style>

</head>

<body>

    <form method="POST">
        <h2>FITMATCH LOGIN</h2>

        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">LOGIN</button>

        <?php if (isset($error))
            echo "<p>$error</p>"; ?>
    </form>

</body>

</html>