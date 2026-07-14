<?php
$conn = new mysqli("localhost", "root", "", "school");

// In a real app, use session: $student_id = $_SESSION['student_id'];
$student_id = 1; 

// Fetch Marks History
$marks_query = $conn->query("
    SELECT subject_name, exam_type, marks_obtained, total_marks 
    FROM marks 
    WHERE student_id = $student_id 
    ORDER BY id DESC
");

// Calculate Overall Performance for Top Banner
$stats = $conn->query("SELECT AVG(marks_obtained) as avg_score, MAX(marks_obtained) as top_score FROM marks WHERE student_id = $student_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Results | Student Portal</title>
    <link href="https://cloudflare.com" rel="stylesheet">
    <style>
        :root { --primary: #1e6091; --secondary: #184e77; --bg: #f0f2f5; --success: #2d6a4f; --danger: #c1121f; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); padding: 40px 20px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #f0f2f5; padding-bottom: 20px; }
        .btn-back { text-decoration: none; color: var(--primary); font-weight: 600; font-size: 14px; }

        /* Performance Overview */
        .performance-banner { 
            background: linear-gradient(135deg, var(--primary), var(--secondary)); 
            color: white; padding: 25px; border-radius: 12px; display: grid; 
            grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 35px;
        }
        .perf-item h4 { margin: 0; opacity: 0.8; font-size: 13px; text-transform: uppercase; }
        .perf-item p { margin: 5px 0 0 0; font-size: 28px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; color: #888; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #f0f2f5; }
        td { padding: 18px 15px; border-bottom: 1px solid #f0f2f5; }
        
        .marks-badge { 
            background: #f8f9fa; padding: 8px 15px; border-radius: 8px; 
            font-weight: bold; border: 1px solid #eee; display: inline-block;
        }

        .btn-download {
            background: var(--primary); color: white; border: none; padding: 8px 15px;
            border-radius: 6px; cursor: pointer; font-size: 13px; transition: 0.2s;
        }
        .btn-download:hover { background: var(--secondary); transform: scale(1.05); }

        /* Print Styles */
        @media print {
            body * { visibility: hidden; }
            #printable-card, #printable-card * { visibility: visible; }
            #printable-card { position: absolute; left: 0; top: 0; width: 100%; display: block !important; padding: 50px; border: 3px double #333; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <h2 style="margin-top:10px; color: var(--secondary);">Academic Results</h2>
        </div>
        <i class="fas fa-award fa-3x" style="color: #ffd700;"></i>
    </div>

    <!-- Overview Stats -->
    <div class="performance-banner">
        <div class="perf-item">
            <h4>Average Score</h4>
            <p><?php echo round($stats['avg_score'], 1); ?>%</p>
        </div>
        <div class="perf-item">
            <h4>Highest Score</h4>
            <p><?php echo $stats['top_score']; ?>/100</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Exam Type</th>
                <th>Obtained Marks</th>
                <th>Result</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($marks_query->num_rows > 0): ?>
                <?php while($row = $marks_query->fetch_assoc()): 
                    $isPass = $row['marks_obtained'] >= 35;
                ?>
                <tr>
                    <td><strong><?php echo $row['subject_name']; ?></strong></td>
                    <td style="color: #666;"><?php echo $row['exam_type']; ?></td>
                    <td><div class="marks-badge"><?php echo $row['marks_obtained']; ?> / <?php echo $row['total_marks']; ?></div></td>
                    <td>
                        <span style="color: <?php echo $isPass ? 'var(--success)' : 'var(--danger)'; ?>; font-weight: bold;">
                            <?php echo $isPass ? 'PASS' : 'FAIL'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-download" onclick="preparePrint('<?php echo $row['subject_name']; ?>', '<?php echo $row['exam_type']; ?>', '<?php echo $row['marks_obtained']; ?>')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding: 40px; color: #999;">No exam results published yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Hidden Print Template -->
<div id="printable-card" style="display:none; text-align: center; font-family: serif;">
    <h1>EXCELLENCE ACADEMY</h1>
    <h3>Individual Subject Performance Report</h3>
    <hr>
    <div style="text-align: left; margin: 40px 0; line-height: 2;">
        <p><strong>Subject:</strong> <span id="pr-sub"></span></p>
        <p><strong>Examination:</strong> <span id="pr-exam"></span></p>
        <p><strong>Marks Secured:</strong> <span id="pr-marks"></span> / 100</p>
        <p><strong>Result Status:</strong> <span id="pr-status"></span></p>
    </div>
    <div style="margin-top: 100px; display: flex; justify-content: space-between;">
        <p>____________________<br>Principal Signature</p>
        <p>____________________<br>Seal of Authority</p>
    </div>
</div>

<script>
function preparePrint(sub, exam, marks) {
    document.getElementById('pr-sub').innerText = sub;
    document.getElementById('pr-exam').innerText = exam;
    document.getElementById('pr-marks').innerText = marks;
    document.getElementById('pr-status').innerText = marks >= 35 ? 'QUALIFIED' : 'NOT QUALIFIED';
    window.print();
}
</script>

</body>
</html>
