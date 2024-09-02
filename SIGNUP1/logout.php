<?php
session_start(); // Start the session

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    require 'connect.php'; // Adjust the path to your database connection file

    // Ensure timezone is set correctly
    date_default_timezone_set('Europe/Istanbul');

    $user_id = $_SESSION['user_id'];
    $activity_time = date('Y-m-d H:i:s'); // Current timestamp

    // Record the logout activity
    $insertActivityStmt = $conn->prepare("
        INSERT INTO user_activity (user_id, activity_type, activity_time) 
        VALUES (?, 'logout', ?)
    ");
    $insertActivityStmt->bind_param('is', $user_id, $activity_time);

    if (!$insertActivityStmt->execute()) {
        // Handle any errors
        echo "Error logging out activity: " . $insertActivityStmt->error . "<br>";
    }

    // Close the statement
    $insertActivityStmt->close();

    // Update user status to 'offline'
    $updateStatusStmt = $conn->prepare("
        UPDATE users 
        SET status = 'offline' 
        WHERE id = ?
    ");
    $updateStatusStmt->bind_param('i', $user_id);

    if (!$updateStatusStmt->execute()) {
        // Handle any errors
        echo "Error updating user status: " . $updateStatusStmt->error . "<br>";
    }

    // Close the statement
    $updateStatusStmt->close();

    // Close the database connection
    $conn->close();

    // Destroy session
    session_destroy();

    // Redirect to login page
    header('Location: login_form.php');
    exit();
} else {
    // If no user is logged in, redirect to login page
    header('Location: login_form.php');
    exit();
}
?>
