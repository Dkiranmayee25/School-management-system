<?php
session_start();
$conn = new mysqli("localhost", "root", "", "school");

// --- DATA FETCHING ---
$count_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'] ?? 0;
$count_teachers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='teacher'")->fetch_assoc()['total'] ?? 0;
$total_fees = $conn->query("SELECT SUM(amount) AS total FROM fees WHERE status = 'Paid'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Launchpad</title>
    <link rel="stylesheet" href="https://cloudflare.com">
<style>
    :root {
        --bg-midnight: #0f172a;   /* Deep Oxford Blue */
        --accent-cyan: #00f2ff;    /* Neon Aurora */
        --accent-glow: rgba(0, 242, 255, 0.25);
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --text-bright: #ffffff;
        --text-dim: #94a3b8;
    }

    /* --- Base & Responsive Grid --- */
    body { 
        font-family: 'Inter', system-ui, -apple-system, sans-serif; 
        margin: 0; 
        min-height: 100vh;
        padding: 40px 5%; /* Responsive padding */
        color: var(--text-bright);
        background-color: var(--bg-midnight);
        
        /* Animated Grid Background */
        background-image: 
            linear-gradient(rgba(0, 242, 255, 0.05) 1.5px, transparent 1.5px),
            linear-gradient(90deg, rgba(0, 242, 255, 0.05) 1.5px, transparent 1.5px);
        background-size: 50px 50px;
        animation: gridMove 30s linear infinite;
        position: relative;
        overflow-x: hidden;
    }

    @keyframes gridMove {
        from { background-position: 0 0; }
        to { background-position: 50px 50px; }
    }

    .container { 
        max-width: 1200px; 
        margin: 0 auto; 
        animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); 
    }
    /* --- Digital Clock Styling --- */
.digital-clock {
    margin-top: 10px;
    font-family: 'JetBrains Mono', monospace; /* Monospaced looks more "digital" */
}
#time {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--accent-cyan);
    text-shadow: 0 0 10px var(--accent-glow);
}
#date {
    font-size: 0.85rem;
    color: var(--text-dim);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* --- Search Bar Styling --- */
.header-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 15px;
}

.search-container {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    width: 300px;
    transition: 0.3s;
}

.search-container:focus-within {
    border-color: var(--accent-cyan);
    box-shadow: 0 0 15px var(--accent-glow);
    background: rgba(255,255,255,0.05);
}

.search-container i { color: var(--accent-cyan); font-size: 14px; }

.search-container input {
    background: transparent;
    border: none;
    color: white;
    outline: none;
    width: 100%;
    font-size: 0.9rem;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .header-right, .header-left { align-items: center; text-align: center; width: 100%; }
    .search-container { width: 100%; max-width: 400px; }
}


    /* --- Header & Stats Section --- */
    .header { 
        display: flex; 
        flex-wrap: wrap; /* Allows wrapping on mobile */
        justify-content: space-between; 
        align-items: center; 
        gap: 20px;
        margin-bottom: 50px; 
    }

    .header h1 { 
        font-size: clamp(24px, 5vw, 36px); /* Responsive font size */
        font-weight: 800; 
        letter-spacing: -1.5px;
        background: linear-gradient(135deg, #fff 40%, var(--accent-cyan));
        -webkit-background-clip: text; 
        -webkit-text-fill-color: transparent;
        margin: 0;
    }

    .header-stats { 
        display: flex; 
        gap: 15px; 
        overflow-x: auto; /* Horizontal scroll on very small screens */
        padding-bottom: 5px;
    }

    .mini-box {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        padding: 12px 20px;
        border-radius: 16px;
        border: 1px solid var(--glass-border);
        min-width: 100px;
        transition: 0.3s ease;
    }

    .mini-box span { font-size: 10px; font-weight: 800; color: var(--accent-cyan); text-transform: uppercase; }
    .mini-box strong { font-size: 20px; color: #fff; display: block; }

    /* --- Responsive Command Grid --- */
    .command-grid { 
        display: grid; 
        /* Intelligent grid: 1 col on mobile, 2 on tablet, 3+ on desktop */
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 25px; 
    }

    .box-card { 
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        padding: 40px 30px; 
        border-radius: 28px; 
        text-decoration: none;
        border: 1px solid var(--glass-border);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        text-align: center;
        animation: cardPop 0.6s backwards;
    }

    /* Staggered Loading Animations */
    .box-card:nth-child(1) { animation-delay: 0.1s; }
    .box-card:nth-child(2) { animation-delay: 0.2s; }
    .box-card:nth-child(3) { animation-delay: 0.3s; }
    .box-card:nth-child(4) { animation-delay: 0.4s; }

    .box-card:hover { 
        transform: translateY(-10px) scale(1.02); 
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--accent-cyan);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px var(--accent-glow);
    }

    /* Icon Squircle */
    .icon-sq {
        width: 80px; height: 80px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 22px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 20px;
        transform: rotate(-6deg);
        transition: 0.5s ease;
        border: 1px solid var(--glass-border);
    }

    .box-card:hover .icon-sq { 
        background: var(--accent-cyan);
        transform: rotate(0deg) scale(1.1);
        box-shadow: 0 0 20px var(--accent-cyan);
    }

    .box-card i { font-size: 30px; color: var(--accent-cyan); transition: 0.4s; }
    .box-card:hover i { color: #000; }

    .box-card h3 { color: #fff; margin: 10px 0; font-size: 22px; font-weight: 700; }
    .box-card p { color: var(--text-dim); font-size: 14px; line-height: 1.6; }

    /* --- Logout Button --- */
    .logout-btn { 
        padding: 12px 24px; 
        background: transparent;
        color: #ff4d4d; 
        text-decoration: none; 
        border-radius: 12px; 
        font-weight: 700; 
        border: 1px solid #ff4d4d;
        transition: 0.3s;
        display: flex; align-items: center; gap: 8px;
    }

    .logout-btn:hover { background: #ff4d4d; color: #fff; box-shadow: 0 0 15px rgba(255, 77, 77, 0.4); }

    /* --- Animations --- */
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes cardPop {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    /* --- Tablet/Mobile Specific Adjustments --- */
    @media (max-width: 768px) {
        body { padding: 30px 15px; }
        .header { flex-direction: column; text-align: center; }
        .header-stats { width: 100%; justify-content: center; }
        .box-card { padding: 30px 20px; }
    }
</style>

</head>
<body>
<div class="header">
    <div class="header-left">
        <!-- Real-time Digital Clock -->
        <div class="digital-clock">
            <span id="time">02:38:18</span>
            <span id="date">Thursday, 30 April 2026</span>
        </div>
    </div>

    <div class="header-right">
        <!-- Search Bar -->
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search records, students..." id="dashboardSearch">
        </div>
        
        <div class="header-stats">
            <div class="mini-box">
                <span>Total Students</span>
                <strong>1,240</strong>
            </div>
            <div class="mini-box">
                <span>Active Notices</span>
                <strong>12</strong>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Header with mini stats -->
    <div class="header">
    <div>
        <h1>Admin Launchpad</h1>
        <p style="color:var(--text-light); margin:5px 0 0 0;">Green Valley School | Global Controller</p>
    </div>
    <div class="header-stats">
        <!-- Revenue Mini Box (Green) -->
        <div class="mini-box m-green">
            <span>Revenue</span>
            <strong>$<?= number_format($total_fees) ?></strong>
        </div>

        <!-- Staff Mini Box (Purple) -->
        <div class="mini-box m-purple">
            <span>Staff</span>
            <strong><?= $count_teachers ?></strong>
        </div>

        <!-- Student Mini Box (Blue) -->
        <div class="mini-box m-blue">
            <span>Users</span>
            <strong><?= $count_students ?></strong>
        </div>

        <a href="logout.php" class="logout-btn" style="margin-left: 10px;"><i class="fas fa-power-off"></i>Logout</a>
    </div>
</div>


    <!-- The 6-Box Grid -->
    <div class="command-grid">
        <!-- Student Box -->
        <a href="students.php" class="box-card">
            <div class="icon-sq"><i class="fas fa-user-graduate"></i></div>
            <h3>Students</h3>
            <p>Manage the student database, enrollments, and profiles.</p>
        </a>

        <!-- Teacher Box -->
        <a href="teacher.php" class="box-card">
            <div class="icon-sq"><i class="fas fa-chalkboard-teacher"></i></div>
            <h3>Teachers</h3>
            <p>Direct staff management, assignments, and faculty data.</p>
        </a>

        <!-- Fees Box -->
        <a href="fees.php" class="box-card">
            <div class="icon-sq"><i class="fas fa-file-invoice-dollar"></i></div>
            <h3>Finance</h3>
            <p>Monitor fee collections, invoices, and financial reports.</p>
        </a>

        <!-- Notice Box -->
        <a href="notices.php" class="box-card">
            <div class="icon-sq"><i class="fas fa-bullhorn"></i></div>
            <h3>Notices</h3>
            <p>Broadcast announcements and manage school-wide alerts.</p>
        </a>

        <!-- Attendance Box -->
        <a href="attendance.php" class="box-card">
            <div class="icon-sq"><i class="fas fa-calendar-check"></i></div>
            <h3>Attendance</h3>
            <p>System-wide attendance logs and school frequency trends.</p>
        </a>

        <!-- Settings/Config Box -->
        <a href="settings.php" class="box-card">
            <div class="icon-sq"><i class="fas fa-gears"></i></div>
            <h3>Settings</h3>
            <p>System configuration, database backups, and portal security.</p>
        </a>
    </div>
</div>
<script>function updateClock() {
    const now = new Date();
    const timeOptions = { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
    const dateOptions = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
    
    document.getElementById('time').textContent = now.toLocaleTimeString('en-IN', timeOptions);
    document.getElementById('date').textContent = now.toLocaleDateString('en-IN', dateOptions);
}

// Update every second
setInterval(updateClock, 1000);
updateClock(); // Initial call
</script>
</body>
</html>
