<?php
include "db.php";

$reg_id = intval($_GET['reg_id']);

$messages = $conn->query("
    SELECT * FROM chat_messages
    WHERE reg_id=$reg_id
    ORDER BY message_id ASC
");

while($m = $messages->fetch_assoc()){

    $isCoach = ($m['sender'] == 'admin'); // coach = admin
?>
    
    <div class="msg <?= $isCoach ? 'right' : 'left' ?>">
        <?= htmlspecialchars($m['message']) ?>
        <br>
        <small><?= $m['created_at'] ?></small>
    </div>

<?php } ?>