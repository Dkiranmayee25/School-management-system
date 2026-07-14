/* --- CYBER-NEON SYSTEM VARIABLES --- */
:root {
    --bg-dark: #0a0b10;
    --card-bg: #161b22;
    --neon-blue: #00d2ff;
    --neon-pink: #ff007f;
    --neon-green: #39ff14;
    --neon-purple: #bc13fe;
    --text-main: #ffffff;
    --text-dim: #8b949e;
    --border: #30363d;
}

/* --- BASE STYLES --- */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body { 
    font-family: 'Segoe UI', Roboto, sans-serif; 
    background-color: var(--bg-dark); 
    color: var(--text-main);
    line-height: 1.6;
    /* Subtle tech-grid background */
    background-image: 
        linear-gradient(rgba(0, 210, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 210, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* --- HEADINGS --- */
h1, h2, h3 {
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 20px;
}

.glitch-text {
    text-shadow: 0 0 10px var(--neon-blue);
}

/* --- REUSABLE PANELS --- */
.glass-panel {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

/* --- FORMS & INPUTS --- */
input, select, textarea {
    width: 100%;
    padding: 12px;
    background: #0d1117;
    border: 1px solid var(--border);
    border-radius: 6px;
    color: white;
    margin-bottom: 15px;
    outline: none;
    transition: 0.3s;
}

input:focus {
    border-color: var(--neon-blue);
    box-shadow: 0 0 8px rgba(0, 210, 255, 0.3);
}

/* --- BUTTONS --- */
.btn {
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    transition: 0.3s;
    border: none;
    display: inline-block;
    text-decoration: none;
    text-align: center;
}

.btn-blue { background: var(--neon-blue); color: #000; }
.btn-blue:hover { box-shadow: 0 0 20px var(--neon-blue); }

.btn-pink { background: var(--neon-pink); color: #fff; }
.btn-pink:hover { box-shadow: 0 0 20px var(--neon-pink); }

.btn-outline {
    background: transparent;
    border: 1px solid var(--neon-blue);
    color: var(--neon-blue);
}
.btn-outline:hover { background: var(--neon-blue); color: #000; }

/* --- TABLES (For View Students) --- */
.cyber-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.cyber-table th {
    text-align: left;
    padding: 15px;
    background: #1c2128;
    color: var(--neon-blue);
    border-bottom: 2px solid var(--border);
}

.cyber-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border);
    color: var(--text-dim);
}

.cyber-table tr:hover {
    background: rgba(0, 210, 255, 0.05);
}

/* --- UTILITY CLASSES --- */
.text-center { text-align: center; }
.mt-20 { margin-top: 20px; }
.neon-border-blue { border-color: var(--neon-blue) !important; }
.neon-border-pink { border-color: var(--neon-pink) !important; }






















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal | Cyber Edition</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root {
            --bg-dark: #0a0b10;
            --card-bg: #161b22;
            --neon-blue: #00d2ff;
            --neon-pink: #ff007f;
            --neon-green: #39ff14;
            --text-gray: #8b949e;
        }

        body { 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background-color: var(--bg-dark); 
            color: #ffffff;
            margin: 0; 
            padding: 60px 20px;
            /* Subtle grid background for a tech feel */
            background-image: 
                linear-gradient(rgba(0, 210, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 210, 255, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .container { max-width: 1100px; margin: 0 auto; }
        
        .header { margin-bottom: 50px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { 
            font-size: 36px; 
            letter-spacing: -1px;
            text-shadow: 0 0 10px var(--neon-blue);
        }

        .dashboard-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 30px; 
        }

        .card { 
            background: var(--card-bg); 
            padding: 40px; 
            border-radius: 15px; 
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #30363d;
            position: relative;
        }

        /* Neon Glowing Borders on Hover */
        .card:hover { 
            transform: translateY(-10px); 
            background: #1c2128;
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.2);
        }

        /* Unique neon colors for each card type */
        .card.attendance:hover { border-color: var(--neon-green); box-shadow: 0 0 20px rgba(57, 255, 20, 0.3); }
        .card.marks:hover { border-color: var(--neon-pink); box-shadow: 0 0 20px rgba(255, 0, 127, 0.3); }
        .card.students:hover { border-color: var(--neon-blue); box-shadow: 0 0 20px rgba(0, 210, 255, 0.3); }

        .icon-box {
            font-size: 40px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .attendance .icon-box { color: var(--neon-green); }
        .marks .icon-box { color: var(--neon-pink); }
        .students .icon-box { color: var(--neon-blue); }

        .card h3 { font-size: 24px; margin: 10px 0; font-weight: 600; }
        .card p { color: var(--text-gray); font-size: 15px; line-height: 1.5; text-align: center; }

        .logout-btn { 
            padding: 10px 20px; 
            border: 1px solid #ff4d4d;
            color: #ff4d4d; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            transition: 0.3s;
        }

        .logout-btn:hover { background: #ff4d4d; color: white; box-shadow: 0 0 15px #ff4d4d; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>TEACHER_SYSTEM v2.0</h1>
        <a href="logout.php" class="logout-btn">DISCONNECT</a>
    </div>

    <div class="dashboard-grid">
        <a href="attendance.php" class="card attendance">
            <div class="icon-box"><i class="fas fa-fingerprint"></i></div>
            <h3>Scan Attendance</h3>
            <p>Initialize student presence protocols for the current cycle.</p>
        </a>

        <a href="upload_marks.php" class="card marks">
            <div class="icon-box"><i class="fas fa-database"></i></div>
            <h3>Upload Data</h3>
            <p>Sync academic performance metrics to the central core.</p>
        </a>

        <a href="view_students.php" class="card students">
            <div class="icon-box"><i class="fas fa-user-shield"></i></div>
            <h3>Roster Access</h3>
            <p>Decrypt and view encrypted student profiles and status.</p>
        </a>
    </div>
</div>

</body>
</html>

