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

// 3. HANDLE FILTERS & SEARCH
$class_filter = isset($_GET['class_filter']) ? $_GET['class_filter'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// 4. BUILD DYNAMIC QUERY
$sql = "SELECT * FROM students WHERE 1=1";

if ($class_filter != '') {
    $sql .= " AND class = '" . $conn->real_escape_string($class_filter) . "'";
}
if ($search_query != '') {
    $sql .= " AND (name LIKE '%" . $conn->real_escape_string($search_query) . "%' OR id LIKE '%" . $conn->real_escape_string($search_query) . "%')";
}
$sql .= " ORDER BY class ASC, name ASC";

$students = $conn->query($sql);

// Fetch unique classes for the filter dropdown
$classes = $conn->query("SELECT DISTINCT class FROM students ORDER BY class ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory | Academic Terminal</title>
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
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-midnight); 
            margin: 0; padding: 40px 20px;
            color: var(--text-bright);
            background-image: 
                linear-gradient(rgba(0, 242, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 242, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .container { 
            max-width: 1200px; margin: 0 auto; 
            background: var(--glass-card); backdrop-filter: blur(20px);
            padding: 40px; border-radius: 32px; border: 1px solid var(--glass-border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            animation: slideUp 0.8s ease;
        }

        .header { text-align: left; margin-bottom: 30px; border-left: 4px solid var(--accent-cyan); padding-left: 20px; }
        .header h1 { background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; font-size: 32px; font-weight: 800; }

        /* Search & Filter Bar */
        .controls-row { 
            display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; 
            background: rgba(255,255,255,0.02); padding: 25px; border-radius: 20px; border: 1px solid var(--glass-border);
        }
        .search-box { flex: 2; position: relative; }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--accent-cyan); }
        .search-box input { width: 80%; padding: 14px 14px 14px 45px; background: #161b22; border: 1px solid var(--glass-border); border-radius: 10px; color: white; outline: none; }
        
        .filter-box { flex: 1; min-width: 200px; }
        select { width: 100%; padding: 14px; background: #161b22; border: 1px solid var(--glass-border); border-radius: 12px; color: white; outline: none; cursor: pointer; }
        
        .btn-search { padding: 14px 30px; background: var(--primary-gradient); border: none; border-radius: 12px; color: #000; font-weight: 800; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { transform: translateY(-2px); box-shadow: 0 0 20px var(--accent-glow); }

        /* Table */
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: left; padding: 15px; color: var(--text-dim); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 18px 15px; background: rgba(255,255,255,0.02); border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); }
        td:first-child { border-left: 1px solid var(--glass-border); border-radius: 15px 0 0 15px; }
        td:last-child { border-right: 1px solid var(--glass-border); border-radius: 0 15px 15px 0; }

        tr:hover td { background: rgba(255,255,255,0.05); border-color: var(--accent-cyan); }

        .btn-action { color: var(--accent-cyan); text-decoration: none; font-weight: 700; font-size: 13px; margin-right: 15px; transition: 0.3s; }
        .btn-action:hover { text-shadow: 0 0 10px var(--accent-cyan); }

        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--accent-cyan); text-decoration: none; font-weight: 700; margin-bottom: 25px; transition: 0.3s; }
        .btn-back:hover { transform: translateX(-5px); }

        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <a href="tdashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Return to Command Center</a>

    <div class="header">
        <p><i class="fas fa-id-card"></i> Student Registry Protocol</p>
        <h1>Student Directory</h1>
    </div>

    <!-- Filters & Search -->
    <form method="GET" class="controls-row">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Enter student name or ID to scan..." value="<?= htmlspecialchars($search_query) ?>">
        </div>
        
        <div class="filter-box">
            <select name="class_filter" onchange="this.form.submit()">
                <option value="">All Class Designations</option>
                <?php while($c = $classes->fetch_assoc()): ?>
                    <option value="<?= $c['class'] ?>" <?= $class_filter == $c['class'] ? 'selected' : '' ?>>
                        Grade <?= $c['class'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <button type="submit" class="btn-search">Scanner Search</button>
    </form>

    <!-- Student Table -->
    <table>
        <thead>
            <tr>
                <th>Identity Scan</th>
                <th>Class level</th>
                <th>Database ID</th>
                <th>Registry Status</th>
                <th>Action Protocol</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($students->num_rows > 0): ?>
                <?php while($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><strong style="color: var(--accent-cyan);"><?= htmlspecialchars($row['name']) ?></strong></td>
                    <td><span style="color: var(--text-dim);">Grade <?= htmlspecialchars($row['class']) ?></span></td>
                    <td style="font-family: 'Courier New', monospace; font-size: 13px;">STU-<?= $row['id'] ?></td>
                    <td><span style="color: #2ecc71; font-size: 11px; font-weight: 800;"><i class="fas fa-check-circle"></i> ACTIVE</span></td>
                    <td>
                        <a href="view_marks.php?student_id=<?= $row['id'] ?>" class="btn-action">MARK SCORES</a>

                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 50px; color: var(--text-dim);">
                        <i class="fas fa-user-slash fa-2x"></i><br><br>No student records found matching the current scan parameters.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
