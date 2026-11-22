//<?php
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// SAFE PROGRAM ID HANDLING
if(isset($_POST['program_id'])){
    $_SESSION['program_id'] = $_POST['program_id'];
}

$program_id = $_SESSION['program_id'];
$user_id = $_SESSION['user_id'];
?>

<h2>Client Registration Form</h2>

<form method="POST">

<input type="text" name="full_name" placeholder="Full Name" required><br><br>
<input type="text" name="phone" placeholder="Phone" required><br><br>

<button type="submit" name="save">Confirm Registration</button>

</form>

<?php
if(isset($_POST['save'])){

    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    if (empty($full_name) || empty($phone)) {
        echo "Error: Full name and phone are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO registrations (user_id, program_id, full_name, phone)
                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $user_id, $program_id, $full_name, $phone);

        if($stmt->execute()){

            // mark first login done
            $update_stmt = $conn->prepare("UPDATE users SET first_login=0 WHERE user_id=?");
            $update_stmt->bind_param("s", $user_id);
            $update_stmt->execute();

            unset($_SESSION['program_id']);

            echo "Registration successful!";
            echo "<br><a href='client_dashboard.php'>Go Dashboard</a>";

        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>