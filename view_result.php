<?php
$conn = new mysqli("localhost", "root", "", "school");

// Simulation: $student_id = $_SESSION['student_id'];
$student_id = 1; 

// Fetch All Results
$results = $conn->query("
    SELECT subject_name, exam_type, marks_obtained, total_marks 
    FROM marks 
    WHERE student_id = $student_id 
    ORDER BY id DESC
");

// Calculate Totals for the Top Header
$summary = $conn->query("SELECT SUM(marks_obtained) as obtained, SUM(total_marks) as total FROM marks WHERE student_id = $student_id")->fetch_assoc();
$overall_percent = ($summary['total'] > 0) ? round(($summary['obtained'] / $summary['total']) * 100, 1) : 0;

// Function to determine Grade
function getGrade($marks) {
    if ($marks >= 90) return ['grade' => 'A+', 'color' => '#2d6a4f'];
    if ($marks >= 80) return ['grade' => 'A', 'color' => '#40916c'];
    if ($marks >= 70) return ['grade' => 'B', 'color' => '#1e6091'];
    if ($marks >= 50) return ['grade' => 'C', 'color' => '#aacc00'];
    if ($marks >= 35) return ['grade' => 'D', 'color' => '#fb8500'];
    return ['grade' => 'F', 'color' => '#c1121f'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Results | Student Portal</title>
    <link href="https://cloudflare.com" rel="stylesheet">
    <style>
        :root { --primary: #1e6091; --secondary: #184e77; --bg: #f0f2f5; --white: #ffffff; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: var(--white); padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        
        /* Summary Header */
        .results-header { 
            background: linear-gradient(135deg, var(--primary), var(--secondary)); 
            color: white; padding: 30px; border-radius: 15px; margin-bottom: 40px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .summary-box h2 { margin: 0; font-size: 14px; text-transform: uppercase; opacity: 0.8; letter-spacing: 1px; }
        .summary-box p { margin: 5px 0 0 0; font-size: 32px; font-weight: 800; }

        /* Results Table */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 18px; color: #888; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #f0f2f5; }
        td { padding: 20px 18px; border-bottom: 1px solid #f8f9fa; vertical-align: middle; }
        tr:hover { background-color: #fafbfc; }

        /* Progress Bar */
        .progress-container { width: 100%; background: #eee; border-radius: 10px; height: 8px; margin-top: 8px; }
        .progress-bar { height: 100%; border-radius: 10px; background: var(--primary); transition: 0.5s; }

        /* Grade Pill */
        .grade-badge { 
            padding: 6px 15px; border-radius: 8px; color: white; font-weight: bold; font-size: 14px;
            display: inline-block; min-width: 35px; text-align: center;
        }

        .btn-back { text-decoration: none; color: white; border: 1px solid rgba(255,255,255,0.3); padding: 8px 15px; border-radius: 8px; font-size: 13px; transition: 0.3s; }
        .btn-back:hover { background: rgba(255,255,255,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="results-header">
        <div class="summary-box">
            <h2>Overall Percentage</h2>
            <p><?php echo $overall_percent; ?>%</p>
        </div>
        <div class="summary-box" style="text-align: right;">
            <a href="dashboard.php" class="btn-back"><i class="fas fa-chevron-left"></i> Dashboard</a>
            <p style="font-size: 16px; margin-top: 15px; opacity: 0.9;">Total Score: <?php echo $summary['obtained']; ?> / <?php echo $summary['total']; ?></p>
        </div>
    </div>

    <h3>Detailed Scorecard</h3>
    <table>
        <thead>
            <tr>
                <th>Subject & Exam</th>
                <th width="30%">Score Analysis</th>
                <th>Marks</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $results->fetch_assoc()): 
                $percent = ($row['marks_obtained'] / $row['total_marks']) * 100;
                $gradeData = getGrade($percent);
            ?>
            <tr>
                <td>
                    <strong style="color: var(--secondary); font-size: 16px;"><?php echo $row['subject_name']; ?></strong><br>
                    <span style="font-size: 12px; color: #999;"><?php echo $row['exam_type']; ?></span>
                </td>
                <td>
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">Accuracy: <?php echo round($percent); ?>%</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $percent; ?>%; background: <?php echo $gradeData['color']; ?>;"></div>
                    </div>
                </td>
                <td>
                    <span style="font-weight: bold; font-size: 16px;"><?php echo $row['marks_obtained']; ?></span>
                    <span style="color: #ccc;">/ <?php echo $row['total_marks']; ?></span>
                </td>
                <td>
                    <span class="grade-badge" style="background: <?php echo $gradeData['color']; ?>;">
                        <?php echo $gradeData['grade']; ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
