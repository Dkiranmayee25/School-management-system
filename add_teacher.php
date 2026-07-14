<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "school");
    
    $name = $_POST['teacher_name'];
    $subject = $_POST['subject'];
    $classes = $_POST['classes'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];

    $stmt = $conn->prepare("INSERT INTO teachers (name, subject, classes, phone, salary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $name, $subject, $classes, $phone, $salary);
    
    if ($stmt->execute()) {
        header("Location: teacher.php"); // Redirect back to list
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Teacher</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; padding-top: 50px; }
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        h3 { margin-top: 0; color: #2c3e50; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-row { display: flex; gap: 10px; margin-top: 10px; }
        button { flex: 2; background: #1abc9c; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .cancel { flex: 1; background: #e74c3c; text-align: center; color: white; text-decoration: none; padding: 12px; border-radius: 5px; font-size: 14px; }
    </style>
</head>
<body>

<div class="form-card">
    <h3>Add New Teacher</h3>
    <form method="POST">
        <input type="text" name="teacher_name" placeholder="Teacher Full Name" required>
        <input type="text" name="subject" placeholder="Subject (e.g. Maths)" required>
        <input type="text" name="classes" placeholder="Classes (e.g. 10th, 11th, 12th)" required>
        <input type="tel" name="phone" placeholder="Phone Number" required>
        <input type="number" step="0.01" name="salary" placeholder="Monthly Salary" required>
        
        <div class="btn-row">
            <button type="submit">Save Teacher</button>
            <a href="teacher_management.php" class="cancel">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
