<?php
// 1. Database Connection
include("connect1.php");

$msg = "";
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'directory';
$edit_id = isset($_GET['edit']) ? mysqli_real_escape_string($conn, $_GET['edit']) : null;

// --- HANDLE BULK UPDATE ---
if (isset($_POST['save_bulk'])) {
    foreach ($_POST['student_data'] as $id => $data) {
        $marks = mysqli_real_escape_string($conn, $data['marks']);
        $fees = mysqli_real_escape_string($conn, $data['fees']);
        mysqli_query($conn, "UPDATE students SET marks='$marks', fees_paid='$fees' WHERE id='$id'");
    }
    $msg = "<div class='toast success'>✅ Bulk update completed successfully!</div>";
}

// --- HANDLE INDIVIDUAL UPDATE ---
if (isset($_POST['update_single'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $marks = mysqli_real_escape_string($conn, $_POST['marks']);
    $fees = mysqli_real_escape_string($conn, $_POST['fees']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $id = mysqli_real_escape_string($conn, $_POST['student_id']);

    if (mysqli_query($conn, "UPDATE students SET name='$name', marks='$marks', fees_paid='$fees', class='$class' WHERE id='$id'")) {
        $msg = "<div class='toast success'>✅ Student #$id updated successfully!</div>";
    }
}

// --- HANDLE DELETION ---
if (isset($_POST['delete_student'])) {
    $id = mysqli_real_escape_string($conn, $_POST['student_id']);
    mysqli_query($conn, "DELETE FROM students WHERE id='$id'");
    header("Location: students.php?tab=directory&msg=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management Hub | Admin</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --bg: #f8fafc; --primary: #4f46e5; --text: #1e293b; --muted: #64748b; --border: #e2e8f0; --white: #ffffff; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 40px; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        /* --- Navigation Tabs --- */
        .tab-nav { display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid var(--border); padding-bottom: 10px; }
        .tab-link { text-decoration: none; padding: 10px 20px; border-radius: 8px; color: var(--muted); font-weight: 600; transition: 0.3s; }
        .tab-link.active { background: var(--primary); color: white; }
        .tab-link:hover:not(.active) { background: #e2e8f0; }

        /* --- Components --- */
        .card { background: var(--white); padding: 30px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .toast { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .success { background: #dcfce7; color: #15803d; }

        /* --- Tables --- */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; }
        tr:hover { background: #fbfbfb; }

        /* --- Buttons --- */
        .btn { padding: 11px 20px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; text-decoration: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-link-add { background: #10b981; color: white; margin-bottom: 20px; }
        .btn-link-add:hover { background: #059669; transform: translateY(-2px); }
        
        .search-box { padding: 12px; border: 1px solid var(--border); border-radius: 8px; outline: none; width: 300px; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="container">
    <!-- Tab Navigation -->
    <div class="tab-nav">
        <a href="students.php?tab=directory" class="tab-link <?php echo $active_tab == 'directory' ? 'active' : ''; ?>">Student Directory</a>
        <a href="students.php?tab=bulk" class="tab-link <?php echo $active_tab == 'bulk' ? 'active' : ''; ?>">Bulk Results Update</a>
        <a href="dashboard.php" class="tab-link" style="margin-left: auto;">Exit to Dashboard <i class="fa-solid fa-arrow-right-from-bracket"></i></a>
    </div>

    <?php echo $msg; ?>

    <!-- TAB 1: DIRECTORY -->
    <?php if($active_tab == 'directory' && !$edit_id): ?>
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin:0;">Student Directory</h2>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" id="dirSearch" onkeyup="searchTable()" class="search-box" placeholder="Search by name or roll...">
                <!-- LINKED BUTTON TO YOUR ADD PAGE -->
                <a href="add_Student.php" class="btn btn-link-add">
                    <i class="fa-solid fa-user-plus"></i> New Student Entry
                </a>
            </div>
        </div>

        <table id="dirTable">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Result Status</th>
                    <th>Management</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM students ORDER BY class, rollno");
                while($row = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?php echo $row['rollno']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo $row['marks']; ?>%</td>
                    <td>
                        <a href="students.php?edit=<?php echo $row['id']; ?>" style="color:var(--primary); font-weight:700; text-decoration:none;">Manage Records</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- TAB 2: BULK UPDATE -->
    <?php if($active_tab == 'bulk'): ?>
    <div class="card">
        <h2 style="margin-top:0;">Class-wise Bulk Update</h2>
        <form method="POST">
            <table>
                <thead>
                    <tr><th>Roll No</th><th>Student Name</th><th>Marks (%)</th><th>Fees Paid (INR)</th></tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM students ORDER BY class, rollno");
                    while($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $row['rollno']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?> (<?php echo $row['class']; ?>)</td>
                        <td><input type="number" name="student_data[<?php echo $row['id']; ?>][marks]" value="<?php echo $row['marks']; ?>" style="width:70px; padding:8px; border:1px solid var(--border); border-radius:6px;"></td>
                        <td><input type="number" step="0.01" name="student_data[<?php echo $row['id']; ?>][fees]" value="<?php echo $row['fees_paid']; ?>" style="width:120px; padding:8px; border:1px solid var(--border); border-radius:6px;"></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="text-align: right; margin-top: 20px;">
                <button name="save_bulk" class="btn btn-primary">Update All Selected Records</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- TAB 3: INDIVIDUAL EDIT (POPS UP WHEN YOU CLICK MANAGE) -->
    <?php if($edit_id): 
        $res = mysqli_query($conn, "SELECT * FROM students WHERE id='$edit_id'");
        $row = mysqli_fetch_assoc($res);
    ?>
    <div class="card">
        <h2>Editing Profile: <?php echo htmlspecialchars($row['name']); ?></h2>
        <form method="POST">
            <input type="hidden" name="student_id" value="<?php echo $row['id']; ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div><label style="font-size:12px; font-weight:700; color:var(--muted);">Legal Name</label><br><input type="text" name="name" value="<?php echo $row['name']; ?>" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; margin-top:5px;"></div>
                <div><label style="font-size:12px; font-weight:700; color:var(--muted);">Grade/Class</label><br><input type="text" name="class" value="<?php echo $row['class']; ?>" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; margin-top:5px;"></div>
                <div><label style="font-size:12px; font-weight:700; color:var(--muted);">Current Marks</label><br><input type="number" name="marks" value="<?php echo $row['marks']; ?>" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; margin-top:5px;"></div>
                <div><label style="font-size:12px; font-weight:700; color:var(--muted);">Fees Balance Paid</label><br><input type="number" name="fees" value="<?php echo $row['fees_paid']; ?>" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; margin-top:5px;"></div>
            </div>
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button name="update_single" class="btn btn-primary">Save Changes</button>
                <button name="delete_student" class="btn" style="background:#fee2e2; color:#ef4444;" onclick="return confirm('Confirm permanent deletion?')">Delete Entry</button>
                <a href="students.php" class="btn" style="background:#eee;">Cancel</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
function searchTable() {
    const filter = document.getElementById("dirSearch").value.toLowerCase();
    const rows = document.getElementById("dirTable").getElementsByTagName("tr");
    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = rows[i].innerText.toLowerCase().includes(filter) ? "" : "none";
    }
}
</script>

</body>
</html>
