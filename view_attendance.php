<?php
$conn = new mysqli("localhost", "root", "", "school");

// In a real app, you'd get this from the session: $student_id = $_SESSION['student_id'];
$student_id = 1; 

// Fetch individual records
$records = $conn->query("SELECT date, status FROM attendance WHERE student_id = $student_id ORDER BY date DESC");

// Calculate Stats
$total_days = $records->num_rows;
$present_days = $conn->query("SELECT id FROM attendance WHERE student_id = $student_id AND status = 'Present'")->num_rows;
$percentage = ($total_days > 0) ? round(($present_days / $total_days) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Attendance | Student Portal</title>
    <link href="https://cloudflare.com" rel="stylesheet">
    <style>
        :root { --primary: #1e6091; --secondary: #184e77; --bg: #f0f2f5; --success: #2d6a4f; --danger: #c1121f; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); padding: 40px 20px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #f0f2f5; padding-bottom: 20px; }
        .btn-back { text-decoration: none; color: var(--primary); font-weight: 600; font-size: 14px; }

        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 30px; }
        .stat-card { background: #f8f9fa; padding: 20px; border-radius: 12px; text-align: center; border: 1px solid #eee; }
        .stat-card h4 { margin: 0; color: #666; font-size: 12px; text-transform: uppercase; }
        .stat-card p { margin: 10px 0 0 0; font-size: 24px; font-weight: bold; color: var(--secondary); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #888; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #f0f2f5; }
        td { padding: 15px; border-bottom: 1px solid #f0f2f5; }
        
        .status-pill { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .Present { background: #d8f3dc; color: var(--success); }
        .Absent { background: #ffdada; color: var(--danger); }
    </style>
</head>
<body>
            <a href="sdashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
<div class="container">
    <div class="header">
        <div>

            <h2 style="margin-top:10px; color: var(--secondary);">My Attendance History</h2>
        </div>
        <i class="fas fa-calendar-alt fa-3x" style="color: #e9ecef;"></i>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Days</h4>
            <p><?php echo $total_days; ?></p>
        </div>
        <div class="stat-card">
            <h4>Days Present</h4>
            <p><?php echo $present_days; ?></p>
        </div>
        <div class="stat-card">
            <h4>Attendance %</h4>
            <p style="color: <?php echo ($percentage < 75) ? 'var(--danger)' : 'var(--success)'; ?>">
                <?php echo $percentage; ?>%
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if($records->num_rows > 0): ?>
                <?php while($row = $records->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo date('d M, Y', strtotime($row['date'])); ?></strong></td>
                    <td style="color: #666;"><?php echo date('l', strtotime($row['date'])); ?></td>
                    <td>
                        <span class="status-pill <?php echo $row['status']; ?>">
                            <i class="fas <?php echo ($row['status'] == 'Present') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                            <?php echo strtoupper($row['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" style="text-align:center; padding: 40px; color: #999;">No attendance records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
