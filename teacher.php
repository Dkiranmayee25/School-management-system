<?php
$conn = new mysqli("localhost", "root", "", "school");
$result = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Management</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* The "Option" to redirect */
        .add-btn { 
            background-color: #1abc9c; color: white; padding: 12px 24px; 
            text-decoration: none; border-radius: 5px; font-weight: 600; transition: 0.3s; 
        }
        .add-btn:hover { background-color: #16a085; }

        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        th { background-color: #34495e; color: white; text-align: left; padding: 15px; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
<a href="dashboard.php?page=overview" style="display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 600;">
    <i class="fas fa-chevron-left"></i> Back to Dashboard
</a>

    
<div class="container">
    <div class="header-flex">
        <h2>Teacher Management</h2>
        <!-- REDIRECT OPTION -->
        <a href="add_teacher.php" class="add-btn">+ Add New Teacher</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Subject</th>
                <th>Classes</th>
                <th>Phone</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['subject']) ?></td>
                <td><?= htmlspecialchars($row['classes']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td>$<?= number_format($row['salary'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
