<?php
include 'db.php';

$program_id = $_GET['program_id'] ?? '';
?>

<h2>Registration Form</h2>

<form method="POST" action="register_submit.php">

<input type="hidden" name="program_id" value="<?php echo $program_id; ?>">

Full Name: <input type="text" name="fullname" required><br><br>
Email: <input type="email" name="email" required><br><br>
Level: <input type="text" name="level"><br><br>
Phone: <input type="text" name="phone"><br><br>
Age: <input type="number" name="age"><br><br>

Weight: <input type="text" name="weight"><br><br>
Height: <input type="text" name="height"><br><br>
Body Fat: <input type="text" name="body_fat"><br><br>
Waist: <input type="text" name="waist"><br><br>
Chest: <input type="text" name="chest"><br><br>
BMI: <input type="text" name="bmi"><br><br>

Workout Time: <input type="text" name="workout_time"><br><br>
Budget: <input type="text" name="budget"><br><br>
Body Goal: <input type="text" name="body_goal"><br><br>
Dream Physique: <input type="text" name="dream_physique"><br><br>
Fitness Goal: <input type="text" name="fitness_goal"><br><br>
Notes: <textarea name="notes"></textarea><br><br>

Gender: <input type="text" name="gender"><br><br>
Address: <input type="text" name="address"><br><br>
Experience: <input type="text" name="experience"><br><br>

<button type="submit">Submit Registration</button>

</form>