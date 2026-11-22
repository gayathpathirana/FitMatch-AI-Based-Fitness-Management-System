<?php
include 'db.php';

$user_id = $_SESSION['user_id'];
$program_id = $_POST['program_id'];

$fullname = htmlspecialchars(trim($_POST['fullname']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$level = htmlspecialchars(trim($_POST['level']));
$phone = htmlspecialchars(trim($_POST['phone']));
$age = htmlspecialchars(trim($_POST['age']));

$weight = htmlspecialchars(trim($_POST['weight']));
$height = htmlspecialchars(trim($_POST['height']));
$body_fat = htmlspecialchars(trim($_POST['body_fat']));
$waist = htmlspecialchars(trim($_POST['waist']));
$chest = htmlspecialchars(trim($_POST['chest']));
$bmi = htmlspecialchars(trim($_POST['bmi']));

$workout_time = htmlspecialchars(trim($_POST['workout_time']));
$budget = htmlspecialchars(trim($_POST['budget']));
$body_goal = htmlspecialchars(trim($_POST['body_goal']));
$dream_physique = htmlspecialchars(trim($_POST['dream_physique']));
$fitness_goal = htmlspecialchars(trim($_POST['fitness_goal']));
$notes = htmlspecialchars(trim($_POST['notes']));

$gender = htmlspecialchars(trim($_POST['gender']));
$address = htmlspecialchars(trim($_POST['address']));
$experience = htmlspecialchars(trim($_POST['experience']));
$status = 'new';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

$stmt = $conn->prepare("INSERT INTO registrations (
    user_id, program_id, fullname, email, level, phone, age,
    weight, height, body_fat, waist, chest, bmi,
    workout_time, budget, body_goal, dream_physique,
    fitness_goal, notes, gender, address, experience, status
) VALUES (
    ?, ?, ?, ?, ?, ?, ?,
    ?, ?, ?, ?, ?, ?,
    ?, ?, ?, ?,
    ?, ?, ?, ?, ?, ?
)");

$stmt->bind_param("sssssssssssssssssssssss", 
    $user_id, $program_id, $fullname, $email, $level, $phone, $age,
    $weight, $height, $body_fat, $waist, $chest, $bmi,
    $workout_time, $budget, $body_goal, $dream_physique,
    $fitness_goal, $notes, $gender, $address, $experience, $status
);

if($stmt->execute()){

    $last_id = $conn->insert_id;

    header("Location: waiting_payment_approval.php?reg_id=$last_id");
    exit();

} else {
    echo "Error: " . $conn->error;
}
?>