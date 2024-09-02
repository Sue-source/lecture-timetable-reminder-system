<?php
session_start();
require 'connect.php'; // Adjust the path to your database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    // Insert notification into the database
    $stmt = $conn->prepare("INSERT INTO notifications (message) VALUES (?)");
    $stmt->bind_param('s', $message);
    $stmt->execute();
    $notification_id = $stmt->insert_id;

    // Fetch all users
    $stmt2 = $conn->prepare("SELECT id, email FROM users");
    $stmt2->execute();
    $result = $stmt2->get_result();

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'menensusan@gmail.com'; // Your Gmail email address
        $mail->Password = 'lcgyhqcbhgretoye'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set sender
        $mail->setFrom('menensusan@gmail.com', 'admin');

        while ($user = $result->fetch_assoc()) {
            $user_id = $user['id'];
            $email = $user['email'];

            // Insert user notification into the database
            $stmt3 = $conn->prepare("INSERT INTO user_notifications (user_id, notification_id) VALUES (?, ?)");
            $stmt3->bind_param('ii', $user_id, $notification_id);
            $stmt3->execute();

            // Add recipient
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'computer science Department';
            $mail->Body    = $message;

            // Send email
            $mail->send();

            // Clear all recipients for the next loop
            $mail->clearAddresses();
        }
        echo 'Message has been sent successfully.';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Send Notification</h2>
        <form method="POST" action="admin_notification.php">
            <textarea name="message" required placeholder="Enter your notification message here"></textarea>
            <br>
            <button type="submit">Send Notification</button>
        </form>
    </div>
</body>
</html>
