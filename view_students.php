<?php
// 1. Silent connection - Ensure connect1.php ONLY has the $conn setup
include("connect1.php");

// 2. Sorting Logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';
$nextOrder = ($order == 'ASC') ? 'desc' : 'asc';

// 3. Fetch Data (This creates the $result variable to fix your "fetch_assoc" error)
$query = "SELECT * FROM students ORDER BY $sort $order";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Directory</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        
        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            padding: 30px;
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h2 { color: #1a1c20; margin: 0; font-size: 24px; }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 16px; text-align: left; font-size: 13px; text-transform: uppercase; color: #6c757d; }
        th a { text-decoration: none; color: inherit; display: flex; align-items: center; gap: 5px; }
        
        td { padding: 18px 16px; border-bottom: 1px solid #f1f1f1; color: #444; vertical-align: middle; }
        tr:hover { background-color: #fafbfc; }

        /* Progress Report Pills & Bars */
        .grade-pill { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .grade-a { background: #d1fae5; color: #065f46; } /* Excellent */
        .grade-b { background: #fff7ed; color: #9a3412; } /* Average */
        .grade-c { background: #fee2e2; color: #991b1b; } /* Below Avg */

        .progress-container { width: 130px; background: #eee; border-radius: 10px; height: 8px; margin-top: 6px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 10px; transition: width 0.4s ease; }

        /* Manage Button */
        .manage-btn { 
            background: #4361ee; color: white; padding: 8px 20px; 
            border-radius: 25px; text-decoration: none; font-size: 13px; 
            font-weight: 600; transition: 0.3s; display: inline-block;
        }
        .manage-btn:hover { background: #3046bd; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2); }

        /* Empty State */
        .no-data { text-align: center; padding: 40px; color: #888; }
    </style>
</head>
<body>
<a href="dashboard.php?page=overview" style="display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 600;">
    <i class="fas fa-chevron-left"></i> Back to Dashboard
</a>

<div class="container">
    <div class="header">
        <h2>Student Performance Directory</h2>
        <div style="font-size: 14px; color: #888;">Total Records: <?php echo ($result) ? $result->num_rows : 0; ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th><a href="?sort=name&order=<?php echo $nextOrder; ?>">Student ⇅</a></th>
                <th><a href="?sort=rollno&order=<?php echo $nextOrder; ?>">ID ⇅</a></th>
                <th><a href="?sort=marks&order=<?php echo $nextOrder; ?>">Report ⇅</a></th>
                <th><a href="?sort=attendance&order=<?php echo $nextOrder; ?>">Attendance ⇅</a></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): 
                    // PREVENT WARNINGS: Use ?? 0 to handle missing database columns
                    $marks = $row['marks'] ?? 0;
                    $att = $row['attendance'] ?? 0;

                    // Logic for Grade Badge
                    if ($marks >= 80) { $gradeClass = 'grade-a'; $gradeText = 'Excellent'; }
                    elseif ($marks >= 50) { $gradeClass = 'grade-b'; $gradeText = 'Average'; }
                    else { $gradeClass = 'grade-c'; $gradeText = 'Below Avg'; }
                    
                    // Logic for Attendance Color
                    $attColor = ($att >= 75) ? '#2ec4b6' : (($att >= 50) ? '#ff9f1c' : '#e71d36');
                ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #2d3436;"><?php echo $row['name']; ?></div>
                        <div style="font-size: 12px; color: #a0a0a0;">Class: <?php echo $row['class']; ?></div>
                    </td>
                    <td>#<?php echo $row['rollno']; ?></td>
                    <td>
                        <span class="grade-pill <?php echo $gradeClass; ?>"><?php echo $gradeText; ?> (<?php echo $marks; ?>)</span>
                    </td>
                    <td>
                        <div style="font-size: 13px; font-weight: 700; color: <?php echo $attColor; ?>;"><?php echo $att; ?>%</div>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?php echo $att; ?>%; background: <?php echo $attColor; ?>;"></div>
                        </div>
                    </td>
                    <td>
                        <a href="students.php?id=<?php echo $row['id']; ?>" class="manage-btn">Manage</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="no-data">No students found in the database.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
