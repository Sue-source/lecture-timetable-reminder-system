<?php
// delete_reminder.php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete reminder from the database
    $sql = "DELETE FROM reminders WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Reminder deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
