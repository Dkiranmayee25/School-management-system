<?php
$conn = new mysqli("localhost", "root", "", "school");

// Check connection
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$edit_mode = false;
$edit_id = 0;
$title = "";
$content = "";

// 1. HANDLE DELETE
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: notices.php");
    exit();
}

// 2. HANDLE EDIT (Fetching existing data into form)
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit_id'];
    $res = $conn->query("SELECT * FROM notices WHERE id = $edit_id");
    if ($row = $res->fetch_assoc()) {
        $title = $row['title'];
        $content = $row['content'];
    }
}

// 3. HANDLE SUBMISSION (Add or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_notice'])) {
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];

    if (isset($_POST['id']) && $_POST['id'] > 0) {
        // UPDATE existing
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE notices SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_title, $new_content, $id);
    } else {
        // INSERT new
        $stmt = $conn->prepare("INSERT INTO notices (title, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $new_title, $new_content);
    }
    
    $stmt->execute();
    header("Location: notices.php");
    exit();
}

// 4. FETCH ALL NOTICES
$notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | Notice Board</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #1abc9c; --dark: #2c3e50; --danger: #e74c3c; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg); padding: 30px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .form-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 40px; border-top: 5px solid var(--primary); }
        h2 { color: var(--dark); margin-bottom: 20px; }
        
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; font-size: 15px; }
        .btn { padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-save { background: var(--primary); color: white; }
        .btn-cancel { background: #95a5a6; color: white; margin-left: 10px; }
        
        .notice-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .notice-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); position: relative; }
        .notice-actions { display: flex; gap: 15px; margin-top: 15px; border-top: 1px solid #eee; pt: 10px; padding-top: 10px; }
        .action-link { font-size: 13px; text-decoration: none; font-weight: bold; }
        .edit { color: var(--primary); }
        .delete { color: var(--danger); }
    </style>
</head>
<body>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
<div class="container">
    <h2><?= $edit_mode ? "✏️ Edit Notice" : "📢 Post New Notice" ?></h2>
    
    <div class="form-card">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_id ?>">
            <input type="text" name="title" placeholder="Title" value="<?= htmlspecialchars($title) ?>" required>
            <textarea name="content" rows="4" placeholder="Announcement Details..." required><?= htmlspecialchars($content) ?></textarea>
            
            <button type="submit" name="submit_notice" class="btn btn-save">
                <?= $edit_mode ? "Update Notice" : "Post Notice" ?>
            </button>
            
            <?php if($edit_mode): ?>
                <a href="notices.php" class="btn btn-cancel">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>Existing Notices</h2>
    <div class="notice-grid">
        <?php while($row = $notices->fetch_assoc()): ?>
        <div class="notice-card">
            <small style="color: #888;">📅 <?= date('M d, g:i A', strtotime($row['created_at'])) ?></small>
            <h3 style="margin: 10px 0;"><?= htmlspecialchars($row['title']) ?></h3>
            <p style="color: #555; font-size: 14px;"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            
            <div class="notice-actions">
                <a href="notices.php?edit_id=<?= $row['id'] ?>" class="action-link edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="notices.php?delete_id=<?= $row['id'] ?>" class="action-link delete" 
                   onclick="return confirm('Delete this notice permanently?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
