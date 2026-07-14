<?php
// ===============================
// DATABASE CONFIGURATION
// ===============================
$servername = "localhost";
$username   = "root";
$password   = "";          // XAMPP default is empty
$database   = "school";    // Your database name

// ===============================
// CREATE CONNECTION
// ===============================
$conn = new mysqli($servername, $username, $password, $database);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ===============================
// HANDLE FORM SUBMISSION
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data safely
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $phone   = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';

    // Prepare SQL statement (secure)
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("ssss", $name, $email, $phone, $message);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Message submitted successfully!');
                    window.location.href='contact.html';
                  </script>";
        } else {
            echo "Error inserting data: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();

?>