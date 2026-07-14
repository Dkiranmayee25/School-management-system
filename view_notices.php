<?php
// 1. Database Connection
$conn = new mysqli("localhost", "root", "", "school");

// 2. The Logic: Date formatting function
function time_ago_logic($timestamp) {
    if(!$timestamp) return "Date unknown";
    $time_ago = strtotime($timestamp);
    $diff = time() - $time_ago;
    if ($diff <= 60) return "Just Now";
    $min = round($diff / 60);
    $hrs = round($diff / 3600);
    if ($min <= 60) return ($min == 1) ? "1 min ago" : "$min mins ago";
    if ($hrs <= 24) return ($hrs == 1) ? "1 hour ago" : "$hrs hours ago";
    return date('d M, Y', $time_ago);
}

// 3. THE AJAX SWITCH: If 'refresh' is in the URL, only output the notices
if (isset($_GET['refresh'])) {
    // Check if created_at exists, otherwise use date
    $notices = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
    
    if ($notices && $notices->num_rows > 0) {
        while ($row = $notices->fetch_assoc()) {
            $time = time_ago_logic($row['created_at']);
            $priorityClass = (isset($row['priority']) && $row['priority'] == 'High') ? 'priority-urgent' : '';
            
            echo "
            <div class='notice-card $priorityClass'>
                <div class='notice-meta'>
                    <span class='notice-tag'>" . htmlspecialchars($row['category'] ?? 'Announcement') . "</span>
                    <span class='notice-date'><i class='far fa-clock'></i> $time</span>
                </div>
                <h3 class='notice-title'>" . htmlspecialchars($row['title']) . "</h3>
                <div class='notice-content'>" . nl2br(htmlspecialchars($row['content'])) . "</div>
            </div>";
        }
    } else {
        echo "<div class='empty-state'><p>No new announcements at the moment.</p></div>";
    }
    exit; // Stop here for AJAX requests
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notice Board | Student Portal</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <style>
        :root { --primary: #1e6091; --secondary: #184e77; --bg: #f0f2f5; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: var(--bg); padding: 40px 20px; color: #333; }
        .container { max-width: 850px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .notice-card { background: var(--white); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid var(--primary); }
        .priority-urgent { border-left-color: #c1121f; }
        .notice-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .notice-tag { background: #e9ecef; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; color: #666; }
        .notice-title { margin: 0 0 10px 0; color: var(--secondary); font-size: 19px; }
        .notice-content { color: #555; line-height: 1.6; }
        .empty-state { text-align: center; padding: 60px; color: #999; }
    </style>
</head>
<body>
<a href="sdashboard.php?page=overview" style="display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 600;">
    <i class="fas fa-chevron-left"></i> Back to Dashboard
</a>

<div class="container">
    <div class="header">
        <div>
            <h2 style="margin:0; color: var(--secondary);">School Notice Board</h2>
            <small style="color: #27ae60;"><i class="fas fa-sync fa-spin"></i> Live Updates Enabled</small>
        </div>
        <i class="fas fa-bullhorn fa-3x" style="color: #dee2e6;"></i>
    </div>

    <div id="content-area">
        <p style="text-align:center; color: #999;">Connecting...</p>
    </div>
</div>

<script>
    function refreshNotices() {
        // 'window.location.pathname' tells JavaScript exactly where THIS file is
        // even if the folder name has spaces like "school web"
        const currentFile = window.location.pathname;
        
        fetch(currentFile + '?refresh=1&t=' + Date.now())
            .then(response => {
                if (!response.ok) throw new Error("File not found");
                return response.text();
            })
            .then(data => {
                document.getElementById("content-area").innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    refreshNotices();
    setInterval(refreshNotices, 3000);
</script>

</body>
</html>
