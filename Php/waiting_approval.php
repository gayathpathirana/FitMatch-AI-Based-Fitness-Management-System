<?php
?>
<!DOCTYPE html>
<html>
<head>
<title>Pending Approval</title>

<style>
body{
    margin:0;
    font-family:Poppins, Arial;
    background:#000;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    overflow:hidden;
}

/* glowing background */
body::before,
body::after{
    content:"";
    position:fixed;
    border-radius:50%;
    filter:blur(140px);
    z-index:0;
}

body::before{
    width:500px;
    height:500px;
    background:#790000;
    top:-120px;
    left:-120px;
    opacity:0.6;
}

body::after{
    width:450px;
    height:450px;
    background:#a00000;
    bottom:-120px;
    right:-120px;
    opacity:0.4;
}

/* card */
.card{
    position:relative;
    z-index:1;
    width:420px;
    background:rgba(255,255,255,0.06);
    backdrop-filter: blur(18px);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:18px;
    padding:30px;
    text-align:center;
    box-shadow:0 0 30px rgba(121,0,0,0.4);
}

/* icon */
.icon{
    font-size:50px;
    margin-bottom:10px;
    color:#ff4d4d;
}

/* heading */
h1{
    font-size:22px;
    margin:10px 0;
    color:#ff4d4d;
}

/* text */
p{
    font-size:14px;
    opacity:0.85;
    line-height:1.5;
    color:#ddd;
}

/* status badge */
.status{
    margin-top:15px;
    padding:8px 12px;
    display:inline-block;
    background:rgba(121,0,0,0.3);
    border:1px solid rgba(255,0,0,0.3);
    border-radius:20px;
    font-size:12px;
    color:#fff;
}

/* loader */
.loader{
    margin:20px auto;
    border:4px solid rgba(255,255,255,0.1);
    border-top:4px solid #ff1a1a;
    border-radius:50%;
    width:40px;
    height:40px;
    animation:spin 1s linear infinite;
}

@keyframes spin{
    0%{transform:rotate(0deg);}
    100%{transform:rotate(360deg);}
}

/* button */
.btn{
    margin-top:20px;
    display:inline-block;
    padding:10px 18px;
    background:linear-gradient(135deg,#790000,#ff1a1a);
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-size:13px;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
}
</style>

</head>

<body>

<div class="card">

    <div class="icon">⏳</div>

    <h1>Account Under Review</h1>

    <p>
        Your coach qualification is currently being verified by our admin team.<br>
        Once approved, you will get full access to your dashboard.
    </p>

    <div class="loader"></div>

    <div class="status">Status: Pending Approval</div>

    <br>

    <a class="btn" href="login.php">🔙 Back to Login</a>

</div>

</body>
</html>