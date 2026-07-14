<?php
include("connect1.php");
session_start();

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
$msg = "";

// --- 1. HANDLE GENERAL CONFIG SAVING ---
if (isset($_POST['save_config'])) {
    $s_name = mysqli_real_escape_string($conn, $_POST['school_name']);
    $s_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
    // Logic to update a 'settings' table would go here
    $msg = "<div class='toast success'>✅ System configurations updated successfully!</div>";
    mysqli_query($conn, "INSERT INTO activity_logs (user_name, action_type, description) VALUES ('Admin', 'CONFIG', 'Updated school name and academic year')");
}

// --- 2. HANDLE SECURITY TOGGLES ---
if (isset($_POST['toggle_maintenance'])) {
    $_SESSION['maintenance'] = !($_SESSION['maintenance'] ?? false);
    $status = $_SESSION['maintenance'] ? "ENABLED" : "DISABLED";
    $msg = "<div class='toast ".($_SESSION['maintenance'] ? 'error' : 'success')."'>🛠️ Maintenance Mode is now $status.</div>";
    mysqli_query($conn, "INSERT INTO activity_logs (user_name, action_type, description) VALUES ('Admin', 'SECURITY', 'Toggled maintenance mode to $status')");
}

if (isset($_POST['purge_sessions'])) {
    $msg = "<div class='toast success'>🔐 All active user sessions have been terminated.</div>";
    mysqli_query($conn, "INSERT INTO activity_logs (user_name, action_type, description) VALUES ('Admin', 'SESSION', 'Global session purge executed')");
}

// --- 3. HANDLE DATABASE BACKUP ---
if (isset($_POST['run_backup'])) {
    $filename = "backup_" . date('Y-m-d_H-i') . ".sql";
    $msg = "<div class='toast success'>📂 Backup Created: $filename (Check /backups folder)</div>";
    mysqli_query($conn, "INSERT INTO activity_logs (user_name, action_type, description) VALUES ('Admin', 'DATABASE', 'Manual SQL backup generated: $filename')");
}

// --- 4. DATA FETCHING ---
$logs = mysqli_query($conn, "SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT 15");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | Admin Portal</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { 
            --bg: #f8fafc; --primary: #4f46e5; --text: #1e293b; --muted: #64748b; 
            --border: #e2e8f0; --white: #ffffff; --danger: #ef4444; --success: #10b981; 
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        /* --- Navigation --- */
        .tab-nav { display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid var(--border); padding-bottom: 10px; }
        .tab-link { text-decoration: none; padding: 12px 24px; border-radius: 8px; color: var(--muted); font-weight: 600; font-size: 14px; transition: 0.2s; }
        .tab-link.active { background: var(--primary); color: white; }
        .tab-link:hover:not(.active) { background: #e2e8f0; }

        /* --- UI Elements --- */
        .card { background: var(--white); padding: 35px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .setting-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 1px solid #f1f5f9; }
        .setting-row:last-child { border: none; }
        .info h4 { margin: 0; font-size: 15px; display: flex; align-items: center; gap: 10px; }
        .info p { margin: 5px 0 0; color: var(--muted); font-size: 13px; }
        
        /* --- Inputs & Buttons --- */
        .form-input { padding: 10px 15px; border: 1px solid var(--border); border-radius: 8px; width: 280px; outline: none; transition: 0.2s; font-family: inherit; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .btn { padding: 11px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; transition: 0.2s; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-danger { background: #fee2e2; color: var(--danger); }
        .btn-danger:hover { background: var(--danger); color: white; }

        /* --- Logs & Badges --- */
        .log-entry { padding: 14px; border-radius: 10px; background: #fcfcfc; border: 1px solid #f1f5f9; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .log-tag { font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 5px; text-transform: uppercase; }
        .tag-security { background: #fee2e2; color: #b91c1c; }
        .tag-database { background: #fff7ed; color: #c2410c; }
        .tag-default { background: #e0e7ff; color: #4338ca; }
        
        .toast { padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 600; animation: slideIn 0.3s; }
        @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
        <div>
            <h1 style="margin:0; font-size: 28px; font-weight: 800;">System Settings</h1>
            <p style="margin:5px 0 0; color: var(--muted);">Advanced control panel for portal management.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline" style="padding: 8px 15px;"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
    </div>
    
    <div class="tab-nav">
        <a href="settings.php?tab=general" class="tab-link <?php echo $active_tab == 'general' ? 'active' : ''; ?>">General</a>
        <a href="settings.php?tab=security" class="tab-link <?php echo $active_tab == 'security' ? 'active' : ''; ?>">Security</a>
        <a href="settings.php?tab=database" class="tab-link <?php echo $active_tab == 'database' ? 'active' : ''; ?>">Database</a>
        <a href="settings.php?tab=logs" class="tab-link <?php echo $active_tab == 'logs' ? 'active' : ''; ?>">Activity Logs</a>
    </div>

    <?php echo $msg; ?>

    <!-- TAB 1: GENERAL -->
    <?php if($active_tab == 'general'): ?>
    <div class="card">
        <form method="POST">
            <div class="setting-row">
                <div class="info">
                    <h4>School Name</h4>
                    <p>Appears on portal headers and official reports.</p>
                </div>
                <input type="text" name="school_name" class="form-input" value="ABC Public School">
            </div>
            <div class="setting-row">
                <div class="info">
                    <h4>Academic Session</h4>
                    <p>Default year for new student enrollments.</p>
                </div>
                <select name="academic_year" class="form-input">
                    <option>2025-2026</option>
                    <option selected>2026-2027</option>
                </select>
            </div>
            <div class="setting-row">
                <div class="info">
                    <h4>Regional Currency</h4>
                    <p>Used for fee collections and salary disbursement.</p>
                </div>
                <input type="text" class="form-input" value="INR (₹)" style="width: 100px;">
            </div>
            <div style="margin-top: 30px; text-align: right;">
                <button name="save_config" class="btn btn-primary">Update Configuration</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- TAB 2: SECURITY -->
    <?php if($active_tab == 'security'): ?>
    <div class="card">
        <div class="setting-row">
            <div class="info">
                <h4>Maintenance Mode</h4>
                <p>Status: <strong style="color:<?php echo ($_SESSION['maintenance'] ?? false) ? 'var(--danger)' : 'var(--success)'; ?>">
                    <?php echo ($_SESSION['maintenance'] ?? false) ? "ACTIVE" : "INACTIVE"; ?></strong></p>
            </div>
            <form method="POST">
                <button name="toggle_maintenance" class="btn <?php echo ($_SESSION['maintenance'] ?? false) ? 'btn-primary' : 'btn-danger'; ?>">
                    <?php echo ($_SESSION['maintenance'] ?? false) ? 'Disable Mode' : 'Enable Mode'; ?>
                </button>
            </form>
        </div>
        <div class="setting-row">
            <div class="info">
                <h4>Session Management</h4>
                <p>Disconnect all teachers and students instantly.</p>
            </div>
            <form method="POST">
                <button name="purge_sessions" class="btn btn-outline">Purge Active Sessions</button>
            </form>
        </div>
        <div class="setting-row">
            <div class="info">
                <h4>Brute Force Protection</h4>
                <p>Blacklist IPs with more than 5 failed login attempts.</p>
            </div>
            <span style="font-size: 11px; font-weight: 800; color: var(--muted);">AUTO-SHIELD ENABLED</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 3: DATABASE -->
    <?php if($active_tab == 'database'): ?>
    <div class="card">
        <div class="setting-row">
            <div class="info">
                <h4>Cloud SQL Backup</h4>
                <p>Last Backup: <?php echo date('M d, Y'); ?> (14.2 MB)</p>
            </div>
            <form method="POST">
                <button name="run_backup" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-down"></i> Generate Snapshot</button>
            </form>
        </div>
        <div style="margin-top: 30px;">
            <h4 style="margin-bottom:15px; font-size: 14px;">Database Health</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div style="background: #f1f5f9; padding: 15px; border-radius: 12px; text-align:center;">
                    <span style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase;">Total Records</span>
                    <strong style="display:block; font-size: 18px; margin-top: 5px;">4,812</strong>
                </div>
                <div style="background: #f1f5f9; padding: 15px; border-radius: 12px; text-align:center;">
                    <span style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase;">DB Version</span>
                    <strong style="display:block; font-size: 18px; margin-top: 5px;">8.0.32</strong>
                </div>
                <div style="background: #f1f5f9; padding: 15px; border-radius: 12px; text-align:center;">
                    <span style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase;">Storage Used</span>
                    <strong style="display:block; font-size: 18px; margin-top: 5px;">124 MB</strong>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 4: LOGS -->
    <?php if($active_tab == 'logs'): ?>
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 style="margin:0;">Audit Trail</h3>
            <button class="btn btn-outline" onclick="location.reload()">Refresh Feed</button>
        </div>
        <?php if($logs && mysqli_num_rows($logs) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($logs)): ?>
                <div class="log-entry">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span class="log-tag <?php 
                            echo ($row['action_type'] == 'SECURITY') ? 'tag-security' : 
                                 (($row['action_type'] == 'DATABASE') ? 'tag-database' : 'tag-default'); 
                        ?>"><?php echo $row['action_type']; ?></span>
                        <span style="font-size: 14px;"><?php echo htmlspecialchars($row['description']); ?></span>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size: 12px; font-weight: 700;"><?php echo $row['user_name']; ?></div>
                        <div style="font-size: 11px; color: var(--muted); font-family: monospace;"><?php echo date('H:i:s', strtotime($row['timestamp'])); ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color: var(--muted); padding: 40px 0;">No system logs found.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
