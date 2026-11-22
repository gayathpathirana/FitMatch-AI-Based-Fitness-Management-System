<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $short_text = $_POST['short_text'];
    $image_name = "";
    
    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_dir = "../images/";
        
        // Ensure directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    $sql = "INSERT INTO programs (name, category, description, short_text, image)
            VALUES ('$name', '$category', '$description', '$short_text', '$image_name')";

    if (mysqli_query($conn, $sql)) {
        echo "<p style='text-align:center;color:#22c55e;font-weight:bold'>Program Added Successfully!</p>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Program</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

/* ================= DARK RED FITMATCH THEME ================= */
body{
    background: linear-gradient(135deg, #0b0b0b, #1a0000);
    color:#fff;
}

/* TITLE */
h2{
    text-align:center;
    padding:18px;
    margin-bottom:25px;
    border-radius:14px;
    background: linear-gradient(135deg, #790000, #ff1a1a);
    color:white;
    font-size:24px;
    font-weight:bold;
    box-shadow:0 4px 20px rgba(255,0,0,0.3);
}

/* FORM */
form{
    width:60%;
    margin:0 auto;
    background: rgba(20,20,20,0.95);
    padding:25px;
    border-radius:14px;
    box-shadow:0 0 25px rgba(255,0,0,0.2);
    border-left:5px solid #ff1a1a;
}

/* INPUTS */
input, textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:10px;
    border:1px solid #333;
    font-size:14px;
    outline:none;
    background:#111;
    color:#fff;
}

/* FOCUS */
input:focus, textarea:focus{
    border-color:#ff1a1a;
    box-shadow:0 0 8px rgba(255,26,26,0.4);
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    margin-top:10px;
    font-weight:bold;
    font-size:14px;
    cursor:pointer;
    color:white;
    background: linear-gradient(135deg, #790000, #ff1a1a);
    transition:0.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(255,0,0,0.4);
}

/* SUCCESS TEXT */
p{
    text-align:center;
    font-weight:bold;
}

/* FILE BUTTON */
.file-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 8px 0 20px 0;
    padding: 10px;
    background: #111;
    border: 1px solid #333;
    border-radius: 10px;
}

.select-file-btn{
    padding: 10px 18px;
    background: #333;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    width: auto;
    margin: 0;
}

.select-file-btn:hover{
    background: #555;
    transform: none;
    box-shadow: none;
}

.file-name {
    font-size: 14px;
    color: #aaa;
}

/* RESPONSIVE */
@media(max-width:768px){
    form{ width:95%; }
    h2{ font-size:20px; }
    .file-wrapper { flex-direction: column; align-items: flex-start; }
}
</style>

</head>

<body>

<h2>Add Fitness Program</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Program Name" required><br><br>

    <input type="text" name="category" placeholder="Category" required><br><br>

    <textarea name="description" placeholder="Description" required></textarea><br><br>

    <input type="text" name="short_text" placeholder="Short Text" required><br><br>

    <div class="file-wrapper">
        <input type="file" name="image" id="fileInput" accept="image/*" style="display:none;" required>
        <button type="button" class="select-file-btn" onclick="document.getElementById('fileInput').click()">📁 Select File</button>
        <span class="file-name" id="fileName">No file selected</span>
    </div>

    <script>
        document.getElementById('fileInput').addEventListener('change', function() {
            var name = this.value.split('\\').pop();
            document.getElementById('fileName').textContent = name ? name : 'No file selected';
        });
    </script>

    <button type="submit">Add Program</button>

</form>

</body>
</html>