<?php 
ob_start();
include("connect1.php"); 
ob_end_clean();

$message = "";
$showForm = false; // Flag to keep form open on error

if(isset($_POST['add'])){
    $showForm = true; 
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $rollno = mysqli_real_escape_string($conn, $_POST['rollno']);

    $check = mysqli_query($conn, "SELECT id FROM students WHERE class='$class' AND rollno='$rollno'");
    
    if(mysqli_num_rows($check) > 0) {
        $message = "<div class='error'>❌ Roll No $rollno already exists in $class!</div>";
    } else {
        $query = "INSERT INTO students (name, class, rollno) VALUES ('$name', '$class', '$rollno')";
        if(mysqli_query($conn, $query)) {
            $message = "<div class='success'>✅ Student Added Successfully!</div>";
            $showForm = false; // Hide form on success
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-actions { text-align: center; margin: 20px 0; }
        
        /* The Add Button */
        .btn-toggle {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-toggle:hover { background: #0056b3; transform: scale(1.05); }

        /* The Hidden Form Container */
        #addFormWrapper {
            display: <?php echo $showForm ? 'block' : 'none'; ?>;
            max-width: 400px;
            margin: 20px auto;
            padding: 25px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .form-input { 
            width: 100%; 
            padding: 12px; 
            margin: 8px 0; 
            border: 2px solid #eee; 
            border-radius: 25px; 
            outline: none; 
            box-sizing: border-box;
        }
        .form-input:focus { border-color: #007bff; }
        
        .success, .error { padding: 10px; border-radius: 25px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<!-- Keep your existing PHP logic at the top -->

<div id="addFormWrapper">
    <h3 style="text-align:center; margin-top:0;">Enter Student Details</h3>
    <form method="POST" id="studentForm">
        <input type="text" name="name" class="form-input" placeholder="Student Name" required>
        <input type="text" name="class" class="form-input" placeholder="Class (e.g. 10A)" required>
        <input type="text" name="rollno" class="form-input" placeholder="Roll Number" required>
        
        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <!-- Save Button -->
            <button name="add" class="btn-save" style="flex: 2; background:#28a745; color:white; padding:12px; border-radius:25px; border:none; cursor:pointer; font-weight:bold;">
                Confirm Add
            </button>
            
            <!-- Cancel Button -->
            <button type="button" onclick="cancelAdd()" class="btn-cancel" style="flex: 1; background:#6c757d; color:white; padding:12px; border-radius:25px; border:none; cursor:pointer; font-weight:bold;">
                Cancel
            </button>
        </div>
    </form>
</div>

<script>
// Main toggle function
function toggleAddForm() {
    var x = document.getElementById("addFormWrapper");
    x.style.display = (x.style.display === "none" || x.style.display === "") ? "block" : "none";
}

// Specific cancel function
function cancelAdd() {
    document.getElementById("studentForm").reset(); // Clears all inputs
    document.getElementById("addFormWrapper").style.display = "none"; // Hides the form
}
</script>

<style>
    /* Hover effects for new buttons */
    .btn-save:hover { background: #218838 !important; }
    .btn-cancel:hover { background: #5a6268 !important; }
</style>

<div class="content">
    <div class="admin-actions">
        <?php echo $message; ?>
        <!-- Toggle Button -->
        <button class="btn-toggle" onclick="toggleAddForm()">+ Add New Student</button>
    </div>

    <!-- Collapsible Form -->
    <div id="addFormWrapper">
        <h3 style="text-align:center; margin-top:0;">Enter Student Details</h3>
        <form method="POST">
            <input type="text" name="name" class="form-input" placeholder="Student Name" required>
            <input type="text" name="class" class="form-input" placeholder="Class (e.g. 10A)" required>
            <input type="text" name="rollno" class="form-input" placeholder="Roll Number" required>
            <button name="add" class="btn-add" style="width:100%; background:#28a745; color:white; padding:12px; border-radius:25px; border:none; cursor:pointer; font-weight:bold;">Confirm Add</button>
        </form>
    </div>

    <!-- Your View Table would go here -->
</div>

<script>
function toggleAddForm() {
    var x = document.getElementById("addFormWrapper");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}
</script>

</body>
</html>
