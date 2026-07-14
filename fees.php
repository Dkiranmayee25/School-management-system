<?php
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "school");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Handle Student Fee Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_fee'])) {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $student_id, $amount, $date, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: fees.php?tab=fees");
    exit();
}

// 3. Handle Teacher Salary Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_salary'])) {
    $teacher_id = $_POST['teacher_id'];
    $amount = $_POST['amount'];
    $month = $_POST['month'] . "-01"; // Format for DATE column
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO salaries (teacher_id, amount, month, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $teacher_id, $amount, $month, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: fees.php?tab=salaries");
    exit();
}

// 4. Data Fetching
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'fees';

$students = $conn->query("SELECT id, name FROM students ORDER BY name ASC");
$teachers = $conn->query("SELECT id, name FROM teachers ORDER BY name ASC");

$fee_records = $conn->query("SELECT f.*, s.name FROM fees f JOIN students s ON f.student_id = s.id ORDER BY f.date DESC");
$salary_records = $conn->query("SELECT sal.*, t.name FROM salaries sal JOIN teachers t ON sal.teacher_id = t.id ORDER BY sal.month DESC");

// Financial Summary
$total_fees = $conn->query("SELECT SUM(amount) as total FROM fees WHERE status='Paid'")->fetch_assoc()['total'] ?? 0;
$total_salaries = $conn->query("SELECT SUM(amount) as total FROM salaries WHERE status='Paid'")->fetch_assoc()['total'] ?? 0;
$net_liquidity = $total_fees - $total_salaries;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Finance Hub | ABC School</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root {
            --bg: #f8fafc;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --accent: #3b82f6;
            --white: #ffffff;
        }

        body { 
            font-family: 'Inter', -apple-system, sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            margin: 0; 
            padding: 40px 5%; 
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* --- Header & Summary --- */
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .top-nav h1 { font-size: 28px; font-weight: 800; margin: 0; }
        
        .summary-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .summary-card { 
            background: var(--white); padding: 25px; border-radius: 12px; 
            border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
        }
        .summary-card span { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
        .summary-card strong { display: block; font-size: 24px; color: var(--accent); margin-top: 5px; }

        /* --- Tabs --- */
        .tab-btn { 
            padding: 12px 25px; background: #e2e8f0; border: none; border-radius: 8px; 
            cursor: pointer; font-weight: 700; color: var(--muted); margin-right: 10px;
        }
        .tab-btn.active { background: var(--text); color: var(--white); }

        /* --- Form --- */
        .form-card { background: var(--white); padding: 30px; border-radius: 12px; border: 1px solid var(--border); margin: 30px 0; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end; }
        select, input { 
            width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; box-sizing: border-box;
        }
        .btn-post { background: var(--text); color: var(--white); padding: 13px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }

        /* --- Search & Table --- */
        .search-container {
            display: flex; align-items: center; background: #fff; border: 1px solid var(--border);
            padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; max-width: 400px;
        }
        .search-container input { border: none; outline: none; margin-left: 10px; width: 100%; }

        .table-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fcfcfc; text-align: left; font-size: 11px; color: var(--muted); padding: 15px 20px; border-bottom: 1px solid var(--border); text-transform: uppercase; }
        td { padding: 15px 20px; border-bottom: 1px solid #f9f9f9; font-size: 14px; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .paid { background: #ecfdf5; color: #059669; }
        .unpaid, .pending { background: #fef2f2; color: #dc2626; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="top-nav">
        <h1>Finance Hub</h1>
        <a href="dashboard.php" style="text-decoration:none; color:inherit; font-weight:bold;"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <div class="summary-bar">
        <div class="summary-card"><span>Total Fees</span><strong>$<?= number_format($total_fees, 2) ?></strong></div>
        <div class="summary-card"><span>Total Salaries</span><strong>$<?= number_format($total_salaries, 2) ?></strong></div>
        <div class="summary-card"><span>Net Balance</span><strong>$<?= number_format($net_liquidity, 2) ?></strong></div>
    </div>

    <div class="tab-wrapper">
        <button class="tab-btn <?= $active_tab == 'fees' ? 'active' : '' ?>" onclick="switchTab('fees', this)">Student Fees</button>
        <button class="tab-btn <?= $active_tab == 'salaries' ? 'active' : '' ?>" onclick="switchTab('salaries', this)">Teacher Salaries</button>
    </div>

    <!-- Student Fees Section -->
    <div id="fees-section" class="<?= $active_tab == 'fees' ? '' : 'hidden' ?>">
        <div class="form-card">
            <form method="POST" class="form-row">
                <select name="student_id" required>
                    <option value="">Select Student</option>
                    <?php while($s = $students->fetch_assoc()): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                <input type="date" name="date" required>
                <select name="status"><option>Paid</option><option>Unpaid</option></select>
                <button type="submit" name="add_fee" class="btn-post">Record Fee</button>
            </form>
        </div>

        <div class="search-container"><i class="fas fa-search"></i><input type="text" id="feeSearch" onkeyup="filterTable('feeSearch', 'feeTable')" placeholder="Search fee records..."></div>
        <div class="table-card">
            <table id="feeTable">
                <thead><tr><th>Name</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = $fee_records->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td>$<?= number_format($row['amount'], 2) ?></td>
                        <td><?= date("d M, Y", strtotime($row['date'])) ?></td>
                        <td><span class="badge <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Salaries Section -->
    <div id="salaries-section" class="<?= $active_tab == 'salaries' ? '' : 'hidden' ?>">
        <div class="form-card">
            <form method="POST" class="form-row">
                <select name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php while($t = $teachers->fetch_assoc()): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" step="0.01" name="amount" placeholder="Salary" required>
                <input type="month" name="month" required>
                <select name="status"><option>Paid</option><option>Pending</option></select>
                <button type="submit" name="add_salary" class="btn-post">Post Salary</button>
            </form>
        </div>

        <div class="search-container"><i class="fas fa-search"></i><input type="text" id="salSearch" onkeyup="filterTable('salSearch', 'salTable')" placeholder="Search salary records..."></div>
        <div class="table-card">
            <table id="salTable">
                <thead><tr><th>Teacher</th><th>Amount</th><th>Month</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = $salary_records->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td>$<?= number_format($row['amount'], 2) ?></td>
                        <td><?= date("F Y", strtotime($row['month'])) ?></td>
                        <td><span class="badge <?= $row['status'] == 'Paid' ? 'paid' : 'pending' ?>"><?= $row['status'] ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function switchTab(tab, btn) {
        document.getElementById('fees-section').classList.add('hidden');
        document.getElementById('salaries-section').classList.add('hidden');
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(tab + '-section').classList.remove('hidden');
        btn.classList.add('active');
    }

    function filterTable(inputId, tableId) {
        let filter = document.getElementById(inputId).value.toLowerCase();
        let tr = document.getElementById(tableId).getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = tr[i].textContent.toLowerCase().includes(filter) ? "" : "none";
        }
    }
</script>

</body>
</html>
