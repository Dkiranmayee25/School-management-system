<?php
include("../config/connect1.php");

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $class = $_POST['class'];

    mysqli_query($conn, "UPDATE students SET name='$name', class='$class' WHERE id='$id'");
    header("Location: view_students.php");
}
?>

<form method="POST">
<input type="text" name="name" value="<?php echo $row['name']; ?>" required>
<input type="text" name="class" value="<?php echo $row['class']; ?>" required>
<button name="update">Update Student</button>
</form>