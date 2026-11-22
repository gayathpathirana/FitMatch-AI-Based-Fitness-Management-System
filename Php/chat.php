<?php
include "db.php";

$reg_id = intval($_GET['reg_id'] ?? 0);

/* GET USER INFO */
$user = $conn->query("
    SELECT * FROM registrations 
    WHERE reg_id=$reg_id
")->fetch_assoc();

/* SEND MESSAGE (FIXED) */
if(isset($_POST['message'])){

    $msg = $conn->real_escape_string($_POST['message']);

    // coach = admin in DB
    $conn->query("
        INSERT INTO chat_messages (reg_id, message, sender, created_at)
        VALUES ($reg_id, '$msg', 'admin', NOW())
    ");

    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat System</title>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#000;
    min-height:100vh;
    color:white;
    overflow:hidden;
    position:relative;
}

body::before{
    content:"";
    position:fixed;
    width:700px;
    height:700px;
    background:#790000;
    filter:blur(180px);
    top:-250px;
    left:-250px;
    opacity:0.45;
    z-index:-2;
}

body::after{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    background:#790000;
    filter:blur(180px);
    bottom:-250px;
    right:-250px;
    opacity:0.35;
    z-index:-2;
}

.header{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,0.08);
    color:#fff;
    text-align:center;
    padding:20px;
    font-size:22px;
    font-weight:bold;
    box-shadow:0 0 25px rgba(121,0,0,0.3);
}

.chat-wrapper{
    width:70%;
    margin:25px auto;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:20px;
    padding:20px;
    box-shadow:0 0 30px rgba(121,0,0,0.25);
}

.chat-box{
    height:500px;
    overflow-y:auto;
    border-radius:15px;
    padding:15px;
    background:rgba(0,0,0,0.25);
    border:1px solid rgba(255,255,255,0.08);
}

.msg{
    padding:12px 15px;
    margin:10px 0;
    border-radius:15px;
    max-width:75%;
    clear:both;
    word-wrap:break-word;
    font-size:14px;
}

.left{
    float:left;
    background:rgba(255,255,255,0.08);
    color:white;
    border:1px solid rgba(255,255,255,0.08);
}

.right{
    float:right;
    background:#790000;
    color:white;
    box-shadow:0 0 15px rgba(121,0,0,0.4);
}

.input-box{
    display:flex;
    gap:10px;
    margin-top:15px;
}

input{
    flex:1;
    padding:14px;
    border:none;
    outline:none;
    border-radius:12px;
    background:rgba(255,255,255,0.08);
    color:white;
    font-size:14px;
}

input::placeholder{
    color:#bbb;
}

input:focus{
    box-shadow:0 0 10px #790000;
    background:rgba(121,0,0,0.15);
}

button{
    padding:14px 25px;
    border:none;
    border-radius:12px;
    background:#790000;
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#a00000;
    transform:translateY(-2px);
    box-shadow:0 0 15px rgba(121,0,0,0.6);
}

.chat-box::-webkit-scrollbar{
    width:8px;
}

.chat-box::-webkit-scrollbar-track{
    background:#111;
}

.chat-box::-webkit-scrollbar-thumb{
    background:#790000;
    border-radius:10px;
}

.chat-box::-webkit-scrollbar-thumb:hover{
    background:#a00000;
}

@media(max-width:900px){

    .chat-wrapper{
        width:95%;
    }

    .chat-box{
        height:450px;
    }

    .input-box{
        flex-direction:column;
    }

    button{
        width:100%;
    }
}

</style>
</head>

<body>

<div class="header">
💬 Chat with <?= htmlspecialchars($user['fullname']) ?>
</div>

<div class="chat-wrapper">

<div class="chat-box" id="chatBox"></div>

<div class="input-box">
    <input type="text" id="msg" placeholder="Type message...">
    <button onclick="sendMsg()">Send</button>
</div>

</div>

<script>

/* LOAD CHAT */
function loadChat(){
    $("#chatBox").load("load_chat.php?reg_id=<?= $reg_id ?>");
}

/* AUTO REFRESH */
setInterval(loadChat, 1500);
loadChat();

/* SEND MESSAGE */
function sendMsg(){

    let msg = $("#msg").val();

    if(msg.trim() === "") return;

    $.post("chat.php?reg_id=<?= $reg_id ?>", {
        message: msg
    }, function(){
        $("#msg").val("");
        loadChat();
    });
}

</script>

</body>
</html>