<?php
$conn = new mysqli("localhost", "root", "", "school");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SAVE ATTENDANCE - Enhanced with Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['attendance'])) {
    $date = $_POST['date'];
    // Optimization: Prepare the statement once, execute many times
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE status = VALUES(status)");
    
    foreach ($_POST['attendance'] as $student_id => $status) {
        $stmt->bind_param("iss", $student_id, $date, $status);
        $stmt->execute();
    }
    $stmt->close();
    // Refresh to show new records
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

$students = $conn->query("SELECT * FROM students");
$records = $conn->query("SELECT s.name, s.class, a.date, a.status FROM attendance a JOIN students s ON a.student_id = s.id ORDER BY a.date DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Pro</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        h2, h3 { color: #1e6091; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f8f9fa; text-align: left; padding: 12px; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        input[type="date"], select { padding: 8px; border-radius: 5px; border: 1px solid #ddd; width: 100%; box-sizing: border-box; }
        button { background: #1e6091; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; transition: 0.3s; }
        button:hover { background: #184e77; }
        .status-present { color: #2d6a4f; font-weight: bold; }
    </style>
</head>
<body>
<a href="dashboard.php?page=overview" style="display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 600;">
    <i class="fas fa-chevron-left"></i> Back to Dashboard
</a>

<div class="container">
    <h2>Attendance Management</h2>
    <form method="POST">
        <label><strong>Select Date:</strong></label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>

        <table>
            <thead>
                <tr><th>Name</th><th>Class</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $students->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['class']; ?></td>
                    <td>
                        <select name="attendance[<?php echo $row['id']; ?>]">
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit">Save Today's Attendance</button>
    </form>

    <h3>Recent Records</h3>
    <table>
        <thead>
            <tr><th>Name</th><th>Class</th><th>Date</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php while ($row = $records->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['class']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td class="status-present"><?php echo $row['status']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
