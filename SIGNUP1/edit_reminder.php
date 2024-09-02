<?php
// edit_reminder.php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch reminder details
    $sql = "SELECT * FROM reminders WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lecture = $row['lecture'];
        $reminder_date = $row['reminder_date'];
        $reminder_time = $row['reminder_time'];
    } else {
        echo "Reminder not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $lecture = $_POST['lecture'];
    $reminder_date = $_POST['reminder_date'];
    $reminder_time = $_POST['reminder_time'];

    // Update reminder in the database
    $sql = "UPDATE reminders SET lecture = '$lecture', reminder_date = '$reminder_date', reminder_time = '$reminder_time' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Reminder updated successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Reminder</title>
</head>
<body>
    <h1>Edit Reminder</h1>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="text" name="lecture" value="<?php echo $lecture; ?>" placeholder="Lecture Name" required>
        <input type="date" name="reminder_date" value="<?php echo $reminder_date; ?>" required>
        <input type="time" name="reminder_time" value="<?php echo $reminder_time; ?>" required>
        <button type="submit">Update Reminder</button>
    </form>
</body>
</html>
