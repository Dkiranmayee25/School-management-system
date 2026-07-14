<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | Dashboard</title>
    <link href="https://cloudflare.com" rel="stylesheet">
  <style>
    :root {
        --bg-midnight: #0f172a;   /* Oxford Blue */
        --accent-cyan: #00f2ff;    /* Neon Aurora */
        --accent-glow: rgba(0, 242, 255, 0.3);
        --glass-card: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --text-bright: #ffffff;
        --text-dim: #94a3b8;
    }

    /* --- Animated Background with Moving Grid --- */
    body { 
        font-family: 'Inter', 'Segoe UI', sans-serif; 
        background-color: var(--bg-midnight); 
        margin: 0; 
        padding: 60px 20px;
        color: var(--text-bright);
        overflow-x: hidden;
        position: relative;
        /* Moving Grid Pattern */
        background-image: 
            linear-gradient(rgba(0, 242, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 242, 255, 0.05) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: gridMove 20s linear infinite;
    }

    @keyframes gridMove {
        from { background-position: 0 0; }
        to { background-position: 50px 50px; }
    }

    /* Glowing Spotlight */
    body::before {
        content: ""; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 600px; height: 600px; background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
        z-index: -1; pointer-events: none;
    }

    .container { max-width: 1200px; margin: 0 auto; animation: slideUp 0.8s ease; }

    /* --- Header Styling --- */
    .header { 
        margin-bottom: 50px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 20px;
    }

    .header h1 { 
        background: linear-gradient(135deg, #fff 40%, var(--accent-cyan));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 32px; font-weight: 800; letter-spacing: -1.5px;
    }
    .welcome-text { color: var(--text-dim); margin-top: 5px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }

    /* --- Navigation Grid --- */
    .dashboard-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
        gap: 30px; 
    }

    .card { 
        background: var(--glass-card); 
        backdrop-filter: blur(15px);
        padding: 40px; 
        border-radius: 28px; 
        text-decoration: none;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        border: 1px solid var(--glass-border);
        animation: cardPop 0.5s backwards;
    }

    /* Staggered Entrance */
    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }

    .card:hover { 
        transform: translateY(-15px) scale(1.02); 
        border-color: var(--accent-cyan);
        background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 20px var(--accent-glow);
    }

    /* --- Modern Squircle Icons --- */
    .icon-circle {
        width: 85px;
        height: 85px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 24px; /* Squircle shape */
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        transform: rotate(-10deg);
        transition: 0.5s ease;
        border: 1px solid var(--glass-border);
    }

    .card:hover .icon-circle { 
        background: var(--accent-cyan); 
        transform: rotate(0deg) scale(1.1);
        box-shadow: 0 0 20px var(--accent-cyan);
    }

    .card i { font-size: 32px; color: var(--accent-cyan); transition: 0.4s; }
    .card:hover i { color: #000; transform: scale(1.1); }

    .card h3 { color: #fff; margin: 15px 0 10px 0; font-size: 22px; font-weight: 700; }
    .card p { color: var(--text-dim); font-size: 14.5px; line-height: 1.6; margin: 0; }

    /* --- Enhanced Logout --- */
    .logout-btn { 
        padding: 12px 25px; 
        background: transparent; 
        color: #ff4d4d; 
        text-decoration: none; 
        border-radius: 12px; 
        font-weight: 700; 
        font-size: 14px;
        border: 1px solid #ff4d4d;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .logout-btn:hover { 
        background: #ff4d4d; 
        color: #fff; 
        box-shadow: 0 0 15px rgba(255, 77, 77, 0.4);
    }

    /* --- Animations --- */
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes cardPop { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

    /* Responsive adjustment */
    @media (max-width: 600px) {
        .header { flex-direction: column; text-align: center; gap: 20px; }
        .card { padding: 30px; }
    }
</style>

</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>Student Dashboard</h1>
            <p class="welcome-text">Welcome back, Student! Here is your school overview.</p>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="dashboard-grid">
        <!-- Attendance Card -->
        <a href="view_attendance.php" class="card">
            <div class="icon-circle"><i class="fas fa-calendar-check"></i></div>
            <h3>View Attendance</h3>
            <p>Check your daily presence and overall percentage for the current term.</p>
        </a>

        <!-- Results Card -->
        <a href="view_results.php" class="card">
            <div class="icon-circle"><i class="fas fa-poll"></i></div>
            <h3>View Results</h3>
            <p>Access your exam scores, grade cards, and overall academic performance.</p>
        </a>

        <!-- Notices Card -->
        <a href="view_notices.php" class="card">
            <div class="icon-circle"><i class="fas fa-bullhorn"></i></div>
            <h3>View Notices</h3>
            <p>Stay updated with the latest school announcements and event news.</p>
        </a>
    </div>
</div>

</body>
</html>
