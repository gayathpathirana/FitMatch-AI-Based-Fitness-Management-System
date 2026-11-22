<?php
include 'db.php';

$sql = "SELECT * FROM programs";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>FitMatch Programs</title>

    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }

        .view {
            background: #3498db;
        }

        .register {
            background: #27ae60;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .box {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>

    <script>
        function toggleView(id) {
            let box = document.getElementById("box_" + id);
            box.style.display = (box.style.display === "block") ? "none" : "block";
        }
    </script>

</head>

<body>

<h1>Available Fitness Programs</h1>

<div class="grid">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

    <div class="card">

        <?php if(!empty($row['image'])) { ?>
            <img src="../images/<?php echo $row['image']; ?>">
        <?php } else { ?>
            <img src="../images/default.jpg">
        <?php } ?>

        <h3><?php echo $row['name']; ?></h3>

        <p><b>Category:</b> <?php echo $row['category']; ?></p>

        <p><?php echo $row['short_text']; ?></p>

        <!-- VIEW BUTTON (NO NEW PAGE) -->
        <button class="btn view" onclick="toggleView(<?php echo $row['program_id']; ?>)">
            View Program
        </button>

        <div class="box" id="box_<?php echo $row['program_id']; ?>">
            <?php echo nl2br($row['description']); ?>
        </div>

        <!-- REGISTER BUTTON -->
        <form method="POST" action="register.php">
            <input type="hidden" name="program_id" value="<?php echo $row['program_id']; ?>">
            <button class="btn register" type="submit">
                Register
            </button>
        </form>

    </div>

<?php } ?>

</div>

</body>
</html>