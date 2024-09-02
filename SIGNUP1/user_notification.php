<?php
session_start();
require 'connect.php'; // Adjust the path to your database connection file

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

$stmt = $conn->prepare("
    SELECT notifications.message, notifications.created_at
    FROM notifications
    INNER JOIN user_notifications ON notifications.id = user_notifications.notification_id
    WHERE user_notifications.user_id = ?
    ORDER BY notifications.created_at DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Notifications</title>
</head>
<body>
    <h2>My Notifications</h2>
    <?php while ($notification = $result->fetch_assoc()): ?>
        <div>
            <p><?php echo htmlspecialchars($notification['message']); ?></p>
            <p><small><?php echo htmlspecialchars($notification['created_at']); ?></small></p>
        </div>
    <?php endwhile; ?>
</body>
</html>
