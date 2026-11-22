<?php
include "db.php";

$program_id = $_GET['program_id'];

/* ADD */
if(isset($_POST['add'])){
    $conn->query("
        INSERT INTO workouts(program_id,week_number,workout_name,sets,reps,muscle_group)
        VALUES (
        $program_id,
        '$_POST[week]',
        '$_POST[name]',
        '$_POST[sets]',
        '$_POST[reps]',
        '$_POST[muscle]'
        )
    ");
}

/* DELETE */
if(isset($_GET['del'])){
    $conn->query("DELETE FROM workouts WHERE workout_id=".$_GET['del']);
}

$data = $conn->query("SELECT * FROM workouts WHERE program_id=$program_id");
?>

<h2>Workout Manager</h2>

<form method="POST">
<input name="week" placeholder="Week">
<input name="name" placeholder="Workout">
<input name="sets" placeholder="Sets">
<input name="reps" placeholder="Reps">
<input name="muscle" placeholder="Muscle">
<button name="add">Add</button>
</form>

<hr>

<?php while($w=$data->fetch_assoc()){ ?>

<div style="border:1px solid #ccc;padding:10px;margin:5px;">
Week <?= $w['week_number'] ?> - <?= $w['workout_name'] ?>

<a href="?program_id=<?= $program_id ?>&del=<?= $w['workout_id'] ?>">Delete</a>
</div>

<?php } ?>