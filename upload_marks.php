<?php
session_start();

// 1. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "school");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// 2. SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

// 3. GET SELECTED CLASS (Default to none or first available)
$selected_class = isset($_GET['class_filter']) ? $_GET['class_filter'] : '';

// 4. SAVE MARKS LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marks'])) {
    $subject = $_POST['subject'];
    $exam = $_POST['exam'];
    
    $stmt = $conn->prepare("INSERT INTO marks (student_id, subject_name, exam_type, marks_obtained) 
                            VALUES (?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained)");
    
    foreach ($_POST['marks'] as $student_id => $score) {
        if($score !== "") { // Only save if a score was entered
            $stmt->bind_param("issi", $student_id, $subject, $exam, $score);
            $stmt->execute();
        }
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?class_filter=$selected_class&status=success");
    exit();
}

// 5. FETCH CLASSES FOR THE DROPDOWN
$class_list = $conn->query("SELECT DISTINCT class FROM students ORDER BY class ASC");

// 6. FETCH STUDENTS BASED ON FILTER
$students = null;
if ($selected_class != '') {
    $students = $conn->query("SELECT * FROM students WHERE class = '$selected_class' ORDER BY name ASC");
}

// 7. FETCH RECENT LOGS
$records = $conn->query("SELECT s.name, s.class, m.subject_name, m.exam_type, m.marks_obtained 
                         FROM marks m JOIN students s ON m.student_id = s.id 
                         ORDER BY m.id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Terminal | Grade Entry</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root {
            --bg-midnight: #0b0f1a;
            --accent-cyan: #00f2ff;
            --accent-glow: rgba(0, 242, 255, 0.15);
            --glass-card: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-bright: #ffffff;
            --text-dim: #94a3b8;
            --primary-gradient: linear-gradient(135deg, #00f2ff 0%, #0077ff 100%);
        }

        body { 
            font-family: 'Inter', sans-serif; background-color: var(--bg-midnight); margin: 0; padding: 40px 20px; color: var(--text-bright);
            background-image: linear-gradient(rgba(0, 242, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 242, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .container { max-width: 1100px; margin: 0 auto; background: var(--glass-card); backdrop-filter: blur(20px); padding: 40px; border-radius: 32px; border: 1px solid var(--glass-border); box-shadow: 0 20px 50px rgba(0,0,0,0.3); }

        .header { text-align: left; margin-bottom: 30px; border-left: 4px solid var(--accent-cyan); padding-left: 20px; }
        .header h1 { background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; font-size: 32px; font-weight: 800; }

        /* Filter Section */
        .filter-box { background: rgba(0, 242, 255, 0.05); padding: 20px; border-radius: 15px; border: 1px solid var(--accent-glow); margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
        
        /* Configuration Grid */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        
        label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--accent-cyan); font-size: 11px; text-transform: uppercase; }
        select, input.input-score { background: #161b22; color: white; width: 100%; padding: 12px; border: 1px solid var(--glass-border); border-radius: 10px; outline: none; }
        select:focus { border-color: var(--accent-cyan); box-shadow: 0 0 10px var(--accent-glow); }

        table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        td { padding: 15px; background: rgba(255,255,255,0.02); border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); }
        td:first-child { border-left: 1px solid var(--glass-border); border-radius: 12px 0 0 12px; }
        td:last-child { border-right: 1px solid var(--glass-border); border-radius: 0 12px 12px 0; }

        .btn-sync { width: 100%; padding: 18px; background: var(--primary-gradient); color: #000; border: none; border-radius: 15px; font-weight: 800; cursor: pointer; text-transform: uppercase; margin-top: 20px; }
        
        .empty-state { text-align: center; padding: 40px; color: var(--text-dim); border: 1px dashed var(--glass-border); border-radius: 20px; }
    </style>
</head>
<body>
<a href="tdashboard.php?page=overview" style="display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 600;">
    <i class="fas fa-chevron-left"></i> Back to Dashboard
</a>

<div class="container">
    <div class="header">
        <p><i class="fas fa-microchip"></i> Academic Protocol v2.5</p>
        <h1>Marks Entry Portal</h1>
    </div>

    <!-- STEP 1: CLASS SELECTION FILTER -->
    <form method="GET" action="" class="filter-box">
        <div style="flex: 1;">
            <label>Target Class Designation</label>
            <select name="class_filter" onchange="this.form.submit()">
                <option value="">-- Select Class to Load Students --</option>
                <?php while($c = $class_list->fetch_assoc()): ?>
                    <option value="<?= $c['class'] ?>" <?= $selected_class == $c['class'] ? 'selected' : '' ?>>
                        Grade <?= $c['class'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div style="padding-top: 18px; color: var(--text-dim); font-size: 12px;">
            <i class="fas fa-info-circle"></i> Selecting a class will auto-load the student roster.
        </div>
    </form>

    <?php if ($selected_class != ''): ?>
    <form method="POST">
        <!-- Hidden inputs to pass filters -->
        <input type="hidden" name="class_filter" value="<?= $selected_class ?>">

        <div class="form-grid">
            <div>
                <label>Subject Assignment</label>
                <select name="subject" required>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">Science</option>
                    <option value="English">English</option>
                    <option value="Computer Science">Computer Science</option>
                </select>
            </div>
            <div>
                <label>Examination Phase</label>
                <select name="exam" required>
                    <option value="Midterm">Midterm Examination</option>
                    <option value="Final">Final Examination</option>
                </select>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="text-align: left; padding-left: 15px;">Student Identity</th>
                    <th style="text-align: center;">Raw Score (100)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><strong style="color: var(--accent-cyan);"><?= $row['name'] ?></strong></td>
                    <td style="text-align: center;">
                        <input type="number" name="marks[<?= $row['id'] ?>]" class="input-score" placeholder="0-100" min="0" max="100">
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <button type="submit" class="btn-sync">
            <i class="fas fa-satellite-dish"></i> Synchronize Grade Data
        </button>
    </form>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-user-slash fa-3x" style="margin-bottom: 15px; opacity: 0.2;"></i>
            <p>Please select a **Class Designation** above to begin data entry.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
