<?php
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM programs");
?>

<h2>Select a Program (Client)</h2>

<?php
while($row = $result->fetch_assoc()){
?>
    <div style="border:1px solid #000; padding:10px; margin:10px;">
        <h3><?php echo $row['program_name']; ?></h3>

        <form method="POST" action="client_register.php">
            <input type="hidden" name="program_id" value="<?php echo $row['program_id']; ?>">
            <button type="submit">Select</button>
        </form>
    </div>
<?php } ?>