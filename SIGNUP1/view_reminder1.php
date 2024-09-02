<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "User ID not found in session. Please make sure you are logged in.";
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM reminders WHERE user_id = $user_id ORDER BY reminder_date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reminders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td.action-links a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        td.action-links a:hover {
            text-decoration: underline;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Reminders</h1>
        <table>
            <thead>
                <tr>
                    <th>Lecture</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $reminder_date = date('Y-m-d', strtotime($row['reminder_date']));
                        $reminder_time = date('H:i:s', strtotime($row['reminder_time'])); // Adjust format based on your stored time format
                        echo "<tr>";
                        echo "<td>{$row['lecture']}</td>";
                        echo "<td>{$reminder_date}</td>";
                        echo "<td>{$reminder_time}</td>";
                        echo "<td class='action-links'>
                                <a href='edit_reminder.php?id={$row['id']}'>Edit</a>
                                <a href='delete_reminder.php?id={$row['id']}'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No reminders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
