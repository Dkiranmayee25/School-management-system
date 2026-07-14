<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}
$display_name = isset($_SESSION['username']) ? $_SESSION['username'] : "Instructor";
$initial = strtoupper(substr($display_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher OS | Command Center</title>
    <link href="https://googleapis.com" rel="stylesheet">
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
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-midnight); 
            margin: 0; 
            color: var(--text-bright);
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            /* Moving Grid Pattern */
            background-image: 
                linear-gradient(rgba(0, 242, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 242, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            from { background-position: 0 0; }
            to { background-position: 60px 60px; }
        }

        /* Glowing Spotlight */
        body::before {
            content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, var(--accent-glow) 0%, transparent 80%);
            z-index: -1; pointer-events: none;
        }

        .container { max-width: 1300px; margin: 0 auto; padding: 40px 20px; width: 100%; animation: slideUp 0.8s ease; perspective: 1000px; }

        /* --- Header --- */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }
        .profile { display: flex; align-items: center; gap: 20px; }
        
        .avatar-sq { 
            width: 65px; height: 65px; 
            background: var(--primary-gradient);
            border-radius: 22px; color: #000; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 28px; font-weight: 900;
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.4);
            transform: rotate(-3deg);
        }

        .header h1 { 
            margin: 0; font-size: 36px; font-weight: 800; letter-spacing: -1.5px;
            background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* --- Stats Bar --- */
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-item { 
            background: var(--glass-card); backdrop-filter: blur(15px); padding: 25px; 
            border-radius: 24px; display: flex; align-items: center; gap: 15px; 
            border: 1px solid var(--glass-border);
            transition: 0.3s;
        }
        .stat-item:hover { background: rgba(255,255,255,0.06); border-color: var(--accent-cyan); }
        .stat-item i { color: var(--accent-cyan); font-size: 22px; text-shadow: 0 0 10px var(--accent-cyan); }

        /* --- Layout --- */
        .main-layout { display: grid; grid-template-columns: 2.2fr 1fr; gap: 40px; }

        /* --- Action Grid & Cards --- */
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        
        .card { 
            background: var(--glass-card); backdrop-filter: blur(20px); padding: 45px 30px; 
            border-radius: 40px; text-decoration: none; color: inherit;
            border: 1px solid var(--glass-border); 
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            display: flex; flex-direction: column; align-items: center; text-align: center;
            transform-style: preserve-3d;
            position: relative; overflow: hidden;
        }

        /* Neon Border Trace Effect */
        .card::before {
            content: ""; position: absolute; inset: 0; border-radius: 40px;
            padding: 1.5px; background: linear-gradient(45deg, transparent, var(--accent-cyan), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude; opacity: 0; transition: 0.5s;
        }

        .card:hover { 
            transform: rotateX(8deg) rotateY(-8deg) translateY(-15px);
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 20px 40px rgba(0, 242, 255, 0.15);
        }
        .card:hover::before { opacity: 1; }

        .icon-box { 
            width: 85px; height: 85px; background: rgba(255,255,255,0.05); border-radius: 26px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 34px; color: var(--accent-cyan); margin-bottom: 25px; 
            transition: 0.6s; transform: translateZ(50px);
            box-shadow: inset 0 0 15px rgba(0, 242, 255, 0.2);
        }
        .card:hover .icon-box { background: var(--primary-gradient); color: #000; transform: translateZ(80px) rotateY(15deg); }

        /* --- Timetable --- */
        .timetable-pane { 
            background: var(--glass-card); backdrop-filter: blur(20px); padding: 35px; 
            border-radius: 40px; border: 1px solid var(--glass-border); 
        }
        .timeline { position: relative; padding-left: 20px; border-left: 2px dashed rgba(0, 242, 255, 0.2); }
        .entry { position: relative; margin-bottom: 35px; }
        .entry::after { 
            content: ''; position: absolute; left: -28px; top: 8px; width: 12px; height: 12px; 
            background: var(--bg-midnight); border-radius: 50%; border: 2px solid var(--text-dim); transition: 0.3s;
        }
        .entry.active::after { background: var(--accent-cyan); border-color: var(--accent-cyan); box-shadow: 0 0 15px var(--accent-cyan); }
        
        .entry-card { padding: 18px; background: rgba(255, 255, 255, 0.02); border-radius: 22px; border: 1px solid transparent; transition: 0.3s; }
        .entry.active .entry-card { background: rgba(0, 242, 255, 0.05); border-color: rgba(0, 242, 255, 0.3); }

        .logout-btn { 
            padding: 14px 30px; background: transparent; color: #ff4d4d; 
            text-decoration: none; border-radius: 20px; font-weight: 800;
            border: 1px solid rgba(255, 77, 77, 0.3); transition: 0.3s;
        }
        .logout-btn:hover { background: #ff4d4d; color: #fff; box-shadow: 0 0 20px rgba(255, 77, 77, 0.4); }

        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 1100px) { .main-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="profile">
            <div class="avatar-sq"><?php echo $initial; ?></div>
            <div>
                <span id="greeting" style="text-transform: uppercase; letter-spacing: 3px; font-size: 11px; color: var(--accent-cyan); font-weight: 800;">System Online</span>
                <h1>Hello, <?php echo htmlspecialchars($display_name); ?></h1>
            </div>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fas fa-power-off"></i> Sign Out</a>
    </div>

    <div class="stats-bar">
        <div class="stat-item"><i class="fas fa-users-viewfinder"></i> <div><strong style="font-size: 18px;">124</strong> <br><small style="color: var(--text-dim)">Total Students</small></div></div>
        <div class="stat-item"><i class="fas fa-microchip"></i> <div><strong style="font-size: 18px;">4</strong> <br><small style="color: var(--text-dim)">Classes Today</small></div></div>
        <div class="stat-item"><i class="fas fa-satellite-dish"></i> <div><strong style="font-size: 18px;">Live</strong> <br><small style="color: var(--text-dim)">Notice Status</small></div></div>
    </div>

    <div class="main-layout">
        <div class="action-grid">
            <a href="view_notice.php" class="card">
                <div class="icon-box"><i class="fas fa-terminal"></i></div>
                <h3 style="margin: 0; font-size: 22px;">Notice Board</h3>
                <p style="color: var(--text-dim); font-size: 14px; margin-top: 10px;">Deploy school-wide data transmissions.</p>
            </a>
            <a href="tstudents.php" class="card">
                <div class="icon-box"><i class="fas fa-user-shield"></i></div>
                <h3 style="margin: 0; font-size: 22px;">My Students</h3>
                <p style="color: var(--text-dim); font-size: 14px; margin-top: 10px;">Monitor student bios and protocols.</p>
            </a>
            <a href="upload_marks.php" class="card">
                <div class="icon-box"><i class="fas fa-chart-bar"></i></div>
                <h3 style="margin: 0; font-size: 22px;">Grades</h3>
                <p style="color: var(--text-dim); font-size: 14px; margin-top: 10px;">Analyze academic metrics & results.</p>
            </a>
            <a href="materials.php" class="card">
                <div class="icon-box"><i class="fas fa-folder-tree"></i></div>
                <h3 style="margin: 0; font-size: 22px;">Resources</h3>
                <p style="color: var(--text-dim); font-size: 14px; margin-top: 10px;">Manage learning module uploads.</p>
            </a>
        </div>

        <div class="timetable-pane">
            <h3 style="margin-top: 0; margin-bottom: 30px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px;">Daily Protocol</h3>
            <div class="timeline">
                <div class="entry active">
                    <div class="entry-card">
                        <div style="font-size: 11px; font-weight: 800; color: var(--accent-cyan);">09:00 - 10:30 AM</div>
                        <div style="font-weight: 800; margin: 5px 0; font-size: 16px;">Computer Science</div>
                        <div style="font-size: 12px; color: var(--text-dim);"><i class="fas fa-location-dot"></i> Lab 04 • Level 1</div>
                    </div>
                </div>
                <div class="entry">
                    <div class="entry-card">
                        <div style="font-size: 11px; font-weight: 800; color: var(--text-dim);">11:00 - 12:30 PM</div>
                        <div style="font-weight: 800; margin: 5px 0; font-size: 16px;">Cyber Security</div>
                        <div style="font-size: 12px; color: var(--text-dim);"><i class="fas fa-location-dot"></i> Room 202 • Level 2</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Time-based protocol greeting
    const hours = new Date().getHours();
    const greet = hours < 12 ? "Morning Shift" : hours < 18 ? "Afternoon Shift" : "Evening Shift";
    document.getElementById('greeting').innerText = greet + " | System Active";
</script>

</body>
</html>
