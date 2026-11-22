<?php
include "db.php";

$reg_id = isset($_GET['reg_id']) ? intval($_GET['reg_id']) : 0;
$program_id = isset($_GET['program_id']) ? intval($_GET['program_id']) : 0;

if($reg_id == 0 || $program_id == 0){
    die("Missing parameters");
}

/* ADD WORKOUT */
if(isset($_POST['add'])){
    $title = $_POST['title'];
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];

    $conn->query("
        INSERT INTO workout_plan (reg_id, program_id, title, sets, reps)
        VALUES ($reg_id, $program_id, '$title', '$sets', '$reps')
    ");
}

/* DELETE */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM workout_plan WHERE id=$id");
}

/* LIST */
$workouts = $conn->query("
    SELECT * FROM workout_plan 
    WHERE reg_id=$reg_id
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Coach Workout Manager</title>

<style>
body{
    font-family:Arial;
    background:#f4f6ff;
    margin:20px;
}

h2{
    text-align:center;
}

form{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:8px;
    margin:5px 0;
}

button{
    width:100%;
    padding:10px;
    background:#f39c12;
    border:none;
    color:white;
    border-radius:6px;
}

.card{
    background:white;
    padding:10px;
    margin:10px 0;
    border-radius:8px;
}
</style>

</head>

<body>

<h2>🏋️ Coach Workout Manager</h2>

<form method="POST">
    <input name="title" placeholder="Workout Name" required>
    <input name="sets" placeholder="Sets" required>
    <input name="reps" placeholder="Reps" required>
    <button name="add">Add Workout</button>
</form>

<hr>

<?php while($w = $workouts->fetch_assoc()){ ?>
    <div class="card">
        <b><?= $w['title'] ?></b> |
        <?= $w['sets'] ?> sets |
        <?= $w['reps'] ?> reps

        <br><br>

        <a href="?reg_id=<?= $reg_id ?>&program_id=<?= $program_id ?>&delete=<?= $w['id'] ?>">
            Delete
        </a>
    </div>
<?php } ?>

</body>
</html>