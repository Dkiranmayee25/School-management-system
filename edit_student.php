<?php
// Silent connection - ensure connect1.php has no HTML or whitespace
ob_start();
include("connect1.php");
ob_end_clean();

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $class = $_POST['class'];
    $rollno = $_POST['rollno'];

    mysqli_query($conn, "UPDATE students SET name='$name', class='$class', rollno ='$rollno' WHERE id='$id'");
    header("Location: view_students.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student - PPS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .form-container h2 {
            margin-bottom: 25px;
            color: #333;
            font-family: sans-serif;
        }

        /* Styled inputs like the "pill" buttons */
        .form-container input {
            width: 100%;
            padding: 12px 20px;
            margin: 10px 0;
            display: inline-block;
            border: 2px solid #ddd;
            border-radius: 25px; /* Rounded pill look */
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
            font-size: 14px;
        }

        .form-container input:focus {
            border-color: #007bff;
        }

        /* Match the blue button style */
        .btn-update {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        .btn-update:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Student</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
        <input type="text" name="class" placeholder="Class" value="<?php echo htmlspecialchars($row['class']); ?>" required>
        <input type="text" name="rollno" placeholder="Roll Number" value="<?php echo htmlspecialchars($row['rollno']); ?>" required>
        
        <button type="submit" name="update" class="btn-update">Update Student</button>
    </form>
    <a href="view_students.php" class="back-link">← Back to List</a>
</div>

</body>
</html>
