<?php
date_default_timezone_set('Europe/Istanbul'); // Ensure timezone is set

require 'connect.php'; // Adjust the path to your database connection file
session_start(); // Start the session to access user information

// Insert user activity
if (isset($_SESSION['user_id'])) {
    // The current logged-in user's ID
    $user_id = $_SESSION['user_id'];
    
    // Determine the activity type based on user action
    $activity_type = isset($_GET['action']) && $_GET['action'] == 'logout' ? 'logout' : 'login'; 
    
    // Current timestamp
    $activity_time = date('Y-m-d H:i:s'); 
    
    // Prepare and execute the SQL statement to insert activity
    $insertActivityStmt = $conn->prepare("
        INSERT INTO user_activity (user_id, activity_type, activity_time) 
        VALUES (?, ?, ?)
    ");
    $insertActivityStmt->bind_param('iss', $user_id, $activity_type, $activity_time);
    
    if ($insertActivityStmt->execute()) {
        // Uncomment the line below if debugging is needed
        // echo ucfirst($activity_type) . " activity logged successfully for user ID: $user_id<br>";
    } else {
        // Uncomment the line below if debugging is needed
        // echo "Error logging " . $activity_type . " activity: " . $insertActivityStmt->error . "<br>";
    }
}

// Determine tomorrow's date and day of the week
$tomorrowDate = date('Y-m-d', strtotime('+1 day'));
$tomorrowDay = date('l', strtotime($tomorrowDate));

// Debugging output
// echo "Fetching lectures for: $tomorrowDate ($tomorrowDay)<br>";

// Fetch upcoming lectures for tomorrow
$upcomingLecturesStmt = $conn->prepare("
    SELECT course_name, course_code, venue, start_time, end_time, faculty, department, day 
    FROM timetable 
    WHERE day = ? 
      AND start_time >= ?
      AND start_time <= ?
");
$now = date('H:i:s');
$startOfDay = '00:00:00'; // Start of the day
$endOfDay = '23:59:59'; // End of the day
$upcomingLecturesStmt->bind_param('sss', $tomorrowDay, $startOfDay, $endOfDay);
$upcomingLecturesStmt->execute();

if ($upcomingLecturesStmt->error) {
    echo "Query Error: " . $upcomingLecturesStmt->error . "<br>";
}

$upcomingLectures = $upcomingLecturesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
if (empty($upcomingLectures)) {
    // Uncomment the line below if debugging is needed
    // echo "No lectures found for tomorrow.<br>";
}

// Fetch reminders sent
$remindersSentStmt = $conn->prepare("SELECT * FROM reminders_sent");
$remindersSentStmt->execute();
$remindersSent = $remindersSentStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch timetable report
$timetableStmt = $conn->prepare("SELECT * FROM timetable");
$timetableStmt->execute();
$timetable = $timetableStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch notification summary
$notificationSummaryStmt = $conn->prepare("
    SELECT n.id, n.message, u.username, un.created_at 
    FROM notifications n 
    JOIN user_notifications un ON n.id = un.notification_id 
    JOIN users u ON u.id = un.user_id
");
$notificationSummaryStmt->execute();
$notificationSummary = $notificationSummaryStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch user activity report
$userActivityReportStmt = $conn->prepare("
    SELECT u.id, u.username, u.role,
           MAX(CASE WHEN a.activity_type = 'login' THEN a.activity_time END) AS last_login,
           MAX(CASE WHEN a.activity_type = 'logout' THEN a.activity_time END) AS last_logout,
           CASE 
               WHEN MAX(CASE WHEN a.activity_type = 'login' THEN a.activity_time END) IS NOT NULL 
                    AND (MAX(CASE WHEN a.activity_type = 'logout' THEN a.activity_time END) IS NULL 
                        OR MAX(CASE WHEN a.activity_type = 'logout' THEN a.activity_time END) < 
                        MAX(CASE WHEN a.activity_type = 'login' THEN a.activity_time END))
               THEN 'Online'
               ELSE 'Offline'
           END AS activity_status
    FROM users u
    LEFT JOIN user_activity a ON u.id = a.user_id
    GROUP BY u.id, u.username, u.role
");
$userActivityReportStmt->execute();
$userActivityReport = $userActivityReportStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count number of users who have logged in
$loginCountStmt = $conn->prepare("
    SELECT COUNT(DISTINCT user_id) AS login_count
    FROM user_activity
    WHERE activity_type = 'login'
");
$loginCountStmt->execute();
$loginCount = $loginCountStmt->get_result()->fetch_assoc()['login_count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lecture Timetable Reminder System Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            color: #2980b9;
            border-bottom: 1px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: #fff;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1c40f;
            color: #fff;
        }
        button {
            padding: 10px 15px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: #3498db;
        }
        p {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Lecture Timetable Reminder System Report</h1>

    <!-- Add the Update Button -->
    <form method="post" action="report.php">
        <button type="submit">Update Report</button>
    </form>

    <h2>Number of Users Logged In</h2>
    <p>Number of users who have logged in: <?= htmlspecialchars($loginCount) ?></p>

    <h2>Upcoming Lectures for Tomorrow</h2>
    <?php if (!empty($upcomingLectures)): ?>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Venue</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Faculty</th>
                    <th>Department</th>
                    <th>Day</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingLectures as $lecture): ?>
                    <tr>
                        <td><?= htmlspecialchars($lecture['course_name']) ?></td>
                        <td><?= htmlspecialchars($lecture['course_code']) ?></td>
                        <td><?= htmlspecialchars($lecture['venue']) ?></td>
                        <td><?= htmlspecialchars($lecture['start_time']) ?></td>
                        <td><?= htmlspecialchars($lecture['end_time']) ?></td>
                        <td><?= htmlspecialchars($lecture['faculty']) ?></td>
                        <td><?= htmlspecialchars($lecture['department']) ?></td>
                        <td><?= htmlspecialchars($lecture['day']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No lectures scheduled for tomorrow.</p>
    <?php endif; ?>

    <h2>Reminders Sent</h2>
    <?php if (!empty($remindersSent)): ?>
        <table>
            <thead>
                <tr>
                    <th>Sent Date</th>
                    <th>Recipient Name</th>
                    <th>Recipient Email</th>
                    <th>Lecture Details</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($remindersSent as $reminder): ?>
                    <tr>
                        <td><?= htmlspecialchars($reminder['sent_date']) ?></td>
                        <td><?= htmlspecialchars($reminder['recipient_name']) ?></td>
                        <td><?= htmlspecialchars($reminder['recipient_email']) ?></td>
                        <td><?= htmlspecialchars($reminder['lecture_details']) ?></td>
                        <td><?= htmlspecialchars($reminder['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No reminders sent.</p>
    <?php endif; ?>

    <h2>Timetable Report</h2>
    <?php if (!empty($timetable)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Day</th>
                    <th>Venue</th>
                    <th>Faculty</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timetable as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['id']) ?></td>
                        <td><?= htmlspecialchars($entry['course_name']) ?></td>
                        <td><?= htmlspecialchars($entry['course_code']) ?></td>
                        <td><?= htmlspecialchars($entry['start_time']) ?></td>
                        <td><?= htmlspecialchars($entry['end_time']) ?></td>
                        <td><?= htmlspecialchars($entry['day']) ?></td>
                        <td><?= htmlspecialchars($entry['venue']) ?></td>
                        <td><?= htmlspecialchars($entry['faculty']) ?></td>
                        <td><?= htmlspecialchars($entry['department']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No timetable data available.</p>
    <?php endif; ?>

    <h2>Notification Summary</h2>
    <?php if (!empty($notificationSummary)): ?>
        <table>
            <thead>
                <tr>
                    <th>Notification ID</th>
                    <th>Message</th>
                    <th>Recipient</th>
                    <th>Date Sent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notificationSummary as $notification): ?>
                    <tr>
                        <td><?= htmlspecialchars($notification['id']) ?></td>
                        <td><?= htmlspecialchars($notification['message']) ?></td>
                        <td><?= htmlspecialchars($notification['username']) ?></td>
                        <td><?= htmlspecialchars($notification['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No notifications sent.</p>
    <?php endif; ?>

    <h2>User Activity Report</h2>
    <?php if (!empty($userActivityReport)): ?>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Last Logout</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userActivityReport as $activity): ?>
                    <tr>
                        <td><?= htmlspecialchars($activity['id']) ?></td>
                        <td><?= htmlspecialchars($activity['username']) ?></td>
                        <td><?= htmlspecialchars($activity['role']) ?></td>
                        <td><?= htmlspecialchars($activity['last_login']) ?></td>
                        <td><?= htmlspecialchars($activity['last_logout']) ?></td>
                        <td><?= htmlspecialchars($activity['activity_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No user activity data available.</p>
    <?php endif; ?>

</body>
</html>
