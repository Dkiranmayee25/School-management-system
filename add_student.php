<?php 
ob_start();
include("connect1.php"); 
ob_end_clean();

$message = "";

if(isset($_POST['add']) || isset($_POST['add_another'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);

    // Duplicate check
    $check = mysqli_query($conn, "SELECT id FROM students WHERE class='$class' AND rollno='$rollno'");
    
    if(mysqli_num_rows($check) > 0) {
        $message = "<div class='error'>❌ Roll No $rollno already exists in $class!</div>";
    } else {
        $query = "INSERT INTO students (name, class, rollno) VALUES ('$name', '$class', '$rollno')";
        if(mysqli_query($conn, $query)) {
            
            // If "Save & Add Another" was clicked
            if(isset($_POST['add_another'])) {
                $message = "<div class='success'>✅ $name added! Enter next student.</div>";
            } 
            // If "Confirm & Save" was clicked
            else {
                header("Location: students.php?tab=directory&status=added");
                exit();
            }
        } else {
            $message = "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Student | Admin</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root {
            --bg: #f8fafc;
            --primary: #4f46e5;
            --text: #1e293b;
            --border: #e2e8f0;
            --white: #ffffff;
        }

        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background-color: var(--bg); 
            margin: 0; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .add-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 450px;
        }

        h2 { margin: 0 0 8px 0; font-size: 22px; font-weight: 800; text-align: center; }
        p.subtitle { text-align: center; color: #64748b; font-size: 14px; margin-bottom: 30px; }

        .form-input { 
            width: 100%; 
            padding: 14px; 
            margin-bottom: 15px; 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            outline: none; 
            box-sizing: border-box;
            background: #fcfcfc;
            transition: 0.2s;
        }
        .form-input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        
        .error, .success { 
            padding: 12px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            font-size: 13px; 
            font-weight: 600;
            text-align: center; 
        }
        .error { background: #fef2f2; color: #b91c1c; }
        .success { background: #dcfce7; color: #15803d; }

        .btn-stack { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .btn-row { display: flex; gap: 10px; }

        .btn {
            padding: 14px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary { background: var(--primary); color: white; flex: 1; }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }

        .btn-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); flex: 1; }
        .btn-outline:hover { background: rgba(79, 70, 229, 0.05); }

        .btn-back { background: #f1f5f9; color: #64748b; width: 100%; }
        .btn-back:hover { background: #e2e8f0; color: var(--text); }

        label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-bottom: 6px; display: block; }
    </style>
</head>
<body>

<div class="add-card">
    <h2>Enroll Student</h2>
    <p class="subtitle">Enter credentials to register</p>

    <?php echo $message; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" class="form-input" placeholder="Student's name" required autofocus>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Class</label>
                <input type="text" name="class" class="form-input" placeholder="e.g. 10B" required>
            </div>
            <div>
                <label>Roll No</label>
                <input type="text" name="rollno" class="form-input" placeholder="e.g. 24" required>
            </div>
        </div>
        
        <div class="btn-stack">
            <div class="btn-row">
                <!-- Save & Continue -->
                <button type="submit" name="add_another" class="btn btn-outline">
                    <i class="fa-solid fa-rotate-right"></i> Add Another
                </button>
                <!-- Save & Exit -->
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Save & Finish
                </button>
            </div>
            <a href="students.php" class="btn btn-back">Return to Directory</a>
        </div>
    </form>
</div>

</body>
</html>
