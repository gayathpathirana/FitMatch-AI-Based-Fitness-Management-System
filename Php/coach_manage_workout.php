<?php
include "db.php";

$reg_id = intval($_GET['reg_id'] ?? 0);
$program_id = intval($_GET['program_id'] ?? 0);

$user = $conn->query("
    SELECT * FROM registrations WHERE reg_id=$reg_id
")->fetch_assoc();

/* ================= ADD ITEM ================= */
if(isset($_POST['add'])){

    $day = trim($_POST['day'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $sets = trim($_POST['sets'] ?? '');
    $reps = trim($_POST['reps'] ?? '');
    $type = strtolower(trim($_POST['type'] ?? 'workout'));
    $workout_name = trim($_POST['workout_name'] ?? '');

    if(empty($type)){
        $type = 'workout';
    }

    $allowed = ['workout', 'nutrition', 'recovery'];

    if(!in_array($type, $allowed)){
        $type = 'workout';
    }

    if($type == 'nutrition'){

        $fields = [
            'Calories' => [trim($_POST['calories'] ?? ''),    'kcal'],
            'Protein'  => [trim($_POST['protein'] ?? ''),     'g'],
            'Carbs'    => [trim($_POST['carbs'] ?? ''),       'g'],
            'Fats'     => [trim($_POST['fats'] ?? ''),        'g'],
            'Sleep'    => [trim($_POST['sleep_hours'] ?? ''), 'hours'],
        ];

        $lines = [];

        foreach($fields as $label => $f){
            if($f[0] !== '' && is_numeric($f[0])){
                $lines[] = $label . ': ' . $f[0] . ' ' . $f[1];
            }
        }

        $notes = mb_substr(trim($_POST['notes'] ?? ''), 0, 100);

        if($notes !== ''){
            $lines[] = 'Notes: ' . $notes;
        }

        if(count($lines) > 0){

            $nutTitle = 'Daily Nutrition';
            $nutText  = implode("\n", $lines);
            $nutType  = 'nutrition';

            $stmt = $conn->prepare("
                INSERT INTO workout_plan
                (reg_id, program_id, day, title, workout_name, sets, reps, type)
                VALUES (?, ?, ?, ?, ?, '', '', ?)
            ");

            $stmt->bind_param("iissss", $reg_id, $program_id, $day, $nutTitle, $nutText, $nutType);
            $stmt->execute();
            $stmt->close();
        }

    } else {

        $stmt = $conn->prepare("
            INSERT INTO workout_plan
            (
                reg_id,
                program_id,
                day,
                title,
                workout_name,
                sets,
                reps,
                type
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iissssss",
            $reg_id,
            $program_id,
            $day,
            $title,
            $workout_name,
            $sets,
            $reps,
            $type
        );

        $stmt->execute();
        $stmt->close();
    }

    header("Location: coach_manage_workout.php?reg_id=$reg_id&program_id=$program_id");
    exit;
}

/* ================= DELETE WORKOUT / RECOVERY ITEM ================= */
if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    $conn->query("
        DELETE FROM workout_plan
        WHERE id=$id
    ");

    header("Location: coach_manage_workout.php?reg_id=$reg_id&program_id=$program_id");
    exit;
}

/* ================= LOAD WEEKLY PLAN ================= */
$plans = $conn->query("
    SELECT *
    FROM workout_plan
    WHERE reg_id=$reg_id
    ORDER BY
        FIELD(
            day,
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ),
        type
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Coach Manage Workout</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#000;
    color:#fff;
    min-height:100vh;
    padding:20px;
    position:relative;
    overflow-x:hidden;
}

body::before{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    background:#790000;
    filter:blur(140px);
    top:-150px;
    left:-150px;
    opacity:0.6;
    z-index:-1;
}

body::after{
    content:"";
    position:fixed;
    width:500px;
    height:500px;
    background:#a00000;
    filter:blur(150px);
    bottom:-150px;
    right:-150px;
    opacity:0.4;
    z-index:-1;
}

.header{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.1);
    color:white;
    padding:18px;
    text-align:center;
    border-radius:18px;
    margin-bottom:20px;
    box-shadow:0 0 25px rgba(121,0,0,0.3);
    font-size:22px;
    font-weight:bold;
}

.container{
    max-width:1000px;
    margin:auto;
}

h2{
    margin-top:25px;
    margin-bottom:10px;
    color:#ff4d4d !important;
    border-left:4px solid #790000;
    padding-left:10px;
}

h3{
    margin-top:20px;
    margin-bottom:15px;
    color:#fff;
    border-left:4px solid #790000;
    padding-left:10px;
}

.card{
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.1);
    padding:15px;
    margin:12px 0;
    border-radius:15px;
    box-shadow:0 0 20px rgba(121,0,0,0.2);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 0 30px rgba(121,0,0,0.35);
}

.delete{
    float:right;
    text-decoration:none;
    background:#790000;
    color:white;
    width:28px;
    height:28px;
    text-align:center;
    line-height:28px;
    border-radius:50%;
    font-weight:bold;
    transition:0.3s;
}

.delete:hover{
    background:#ff1a1a;
    transform:scale(1.1);
}

input,
select{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:none;
    outline:none;
    border-radius:10px;
    background:rgba(255,255,255,0.08);
    color:white;
    border:1px solid rgba(255,255,255,0.08);
}

input:focus,
select:focus{
    border-color:#790000;
    box-shadow:0 0 10px rgba(121,0,0,0.6);
}

input::placeholder{
    color:#ccc;
}

select{
    cursor:pointer;
}

select option{
    background:#111;
    color:white;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#790000,#a00000);
    color:white;
    font-size:15px;
    font-weight:bold;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

button:hover{
    transform:scale(1.02);
    background:linear-gradient(135deg,#a00000,#ff1a1a);
    box-shadow:0 0 15px rgba(255,0,0,0.4);
}

#nutritionFields{
    display:none;
}

@media(max-width:768px){

    body{
        padding:15px;
    }

    .header{
        font-size:18px;
    }

    .card{
        padding:12px;
    }

}

</style>

</head>

<body>

<div class="header">
🏋️ Managing Plan for <?= htmlspecialchars($user['fullname'] ?? 'User') ?>
</div>

<div class="container">

<h3>Weekly Plan</h3>

<?php

$currentDay = "";

while($row = $plans->fetch_assoc()){

    if($currentDay != $row['day']){

        $currentDay = $row['day'];

        echo "<h2 style='margin-top:20px;color:#2c3e50;'>📅 " . htmlspecialchars($currentDay) . "</h2>";
    }

?>

<div class="card">

    <b><?= ucfirst(htmlspecialchars($row['type'])) ?></b>

    <br>

    <?= htmlspecialchars($row['title'] ?? '') ?>

    -

    <?= nl2br(htmlspecialchars($row['workout_name'] ?? '')) ?>

    <?php if($row['type'] == 'workout'){ ?>
        (<?= htmlspecialchars($row['sets'] ?? '') ?> × <?= htmlspecialchars($row['reps'] ?? '') ?>)
    <?php } ?>

    <a
        class="delete"
        href="?reg_id=<?= $reg_id ?>&program_id=<?= $program_id ?>&delete=<?= $row['id'] ?>"
    >
        X
    </a>

</div>

<?php

}

?>

<h3>Add New Item</h3>

<form method="POST" class="card">

    <select name="day" required>
        <option value="">Select Day</option>
        <option>Monday</option>
        <option>Tuesday</option>
        <option>Wednesday</option>
        <option>Thursday</option>
        <option>Friday</option>
        <option>Saturday</option>
        <option>Sunday</option>
    </select>

    <select
        name="type"
        id="type"
        required
        onchange="toggleFields()"
    >
        <option value="workout">Workout</option>
        <option value="nutrition">Nutrition</option>
        <option value="recovery">Recovery</option>
    </select>

    <div id="workoutFields">

        <input
            name="title"
            id="title"
            placeholder="Title (e.g Chest + Triceps)"
            required
        >

        <input
            name="workout_name"
            id="workout_name"
            placeholder="Workout (e.g Push Ups 12×4)"
        >

        <input
            name="sets"
            id="sets"
            placeholder="Sets"
        >

        <input
            name="reps"
            id="reps"
            placeholder="Reps"
        >

    </div>

    <div id="nutritionFields">

        <input
            type="number"
            name="calories"
            id="calories"
            placeholder="Calories"
            step="any"
        >

        <input
            type="number"
            name="protein"
            id="protein"
            placeholder="Protein (g)"
            step="any"
        >

        <input
            type="number"
            name="carbs"
            id="carbs"
            placeholder="Carbs (g)"
            step="any"
        >

        <input
            type="number"
            name="fats"
            id="fats"
            placeholder="Fats (g)"
            step="any"
        >

        <input
            type="number"
            name="sleep_hours"
            id="sleep_hours"
            placeholder="Sleep Hours"
            step="any"
        >

        <input
            type="text"
            name="notes"
            id="notes"
            placeholder="Notes"
        >

    </div>

    <button
        type="submit"
        name="add"
    >
        Add
    </button>

</form>

</div>

<script>

function toggleFields(){

    const type = document.getElementById("type").value;

    const workoutFields = document.getElementById("workoutFields");
    const nutritionFields = document.getElementById("nutritionFields");
    const title = document.getElementById("title");

    if(type === "nutrition"){

        workoutFields.style.display = "none";
        nutritionFields.style.display = "block";
        title.required = false;

    }else{

        workoutFields.style.display = "block";
        nutritionFields.style.display = "none";
        title.required = true;
    }
}

toggleFields();

</script>

</body>
</html>