<?php
date_default_timezone_set('Europe/Istanbul');
require 'connect.php'; // Adjust the path to your database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Log function
function logMessage($message) {
    $logFilePath = __DIR__ . '/log.txt'; // __DIR__ gives the directory of the current script
    file_put_contents($logFilePath, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the start time of the script
logMessage("Script started at: " . date('Y-m-d H:i:s'));

// Fetch upcoming lectures for the next day
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$tomorrowDay = date('l', strtotime('+1 day')); // Get the day of the week

logMessage("Tomorrow's Date: $tomorrow, Day of the Week: $tomorrowDay");

$stmt = $conn->prepare("
    SELECT course_name, course_code, venue, start_time, end_time, faculty, department, day 
    FROM timetable 
    WHERE day = ?
");
$stmt->bind_param('s', $tomorrowDay);
$stmt->execute();
$lectures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

logMessage("Lectures fetched: " . json_encode($lectures));

if (count($lectures) > 0) {
    // Fetch all students and lecturers, excluding admins
    $stmt2 = $conn->prepare("SELECT id, email, username FROM users WHERE role IN ('student', 'lecturer')");
    $stmt2->execute();
    $users = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

    logMessage("Users fetched: " . json_encode($users));

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
        $mail->setFrom('menensusan@gmail.com', 'Lecture Reminder');

        foreach ($users as $user) {
            $mail->addAddress($user['email'], $user['username']);

            // Prepare email content
            $mail->isHTML(true);
            $mail->Subject = 'Reminder: Upcoming Lecture Tomorrow';
            $body = '<h3>Upcoming Lectures for Tomorrow</h3>';
            foreach ($lectures as $lecture) {
                $body .= "<p><strong>Course Name:</strong> {$lecture['course_name']}<br>";
                $body .= "<strong>Course Code:</strong> {$lecture['course_code']}<br>";
                $body .= "<strong>Time:</strong> {$lecture['start_time']} - {$lecture['end_time']}<br>";
                $body .= "<strong>Venue:</strong> {$lecture['venue']}<br>";
                $body .= "<strong>Faculty:</strong> {$lecture['faculty']}<br>";
                $body .= "<strong>Department:</strong> {$lecture['department']}<br></p>";
            }
            $mail->Body = $body;

            // Send email
            if ($mail->send()) {
                $status = 'sent';
            } else {
                $status = 'failed';
            }

            // Insert reminder record into the database
            $lecture_details = json_encode($lectures); // Encode lecture details as JSON
            $stmt3 = $conn->prepare("INSERT INTO reminders_sent (sent_date, recipient_name, recipient_email, lecture_details, status) VALUES (NOW(), ?, ?, ?, ?)");
            $stmt3->bind_param('ssss', $user['username'], $user['email'], $lecture_details, $status);
            $stmt3->execute();

            // Log the result of each email send attempt
            logMessage("Email sent to {$user['email']} with status: $status");

            // Clear all recipients for the next loop
            $mail->clearAddresses();
        }
        logMessage('Reminders have been sent successfully.');
    } catch (Exception $e) {
        logMessage("Reminders could not be sent. Mailer Error: {$mail->ErrorInfo}");

        // Record the error
        $error_description = $conn->real_escape_string($mail->ErrorInfo);
        $stmt4 = $conn->prepare("INSERT INTO system_errors (error_date, description, resolved) VALUES (NOW(), ?, 0)");
        $stmt4->bind_param('s', $error_description);
        $stmt4->execute();
    }
} else {
    logMessage('No lectures scheduled for tomorrow.');
}

logMessage("Script ended at: " . date('Y-m-d H:i:s'));
?>
