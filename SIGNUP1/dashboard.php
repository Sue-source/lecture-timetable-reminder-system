<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    // Check if the user has the right role to access this page
    if ($_SESSION['role'] != 'student') {
        echo "You do not have permission to access this page.";
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        header {
            background-color:skyblue;
            padding: 10px;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            display: inline-block;
            margin-right: 20px;
        }
        nav ul li a {
            text-decoration: none;
            color: #007bff;
        }
        nav ul li a:hover {
            color: #0056b3;
        }
        /* Add styles for the aside panel */
        aside {
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 0px;
            width: 200px;
            position: fixed;
            left: 0;
            height: 100%;
            overflow-y: auto;/* Adjust the width as needed */
        }
        aside ul {
            list-style-type: none;
            padding: 0;
        }
        aside ul li {
            margin-bottom: 10px;
        }
        aside ul li a {
            text-decoration: none;
            color: #333;
        }
        aside ul li a:hover {
            color: #555;
        }

        #logout{

            position:absolute;
            top: 20px;
            right: 20px;
            border-radius:50%;
            background-color:#04AA6D;
            text-color:white;
            padding: 10px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome Student!</h1>
        <a href="logout.php" id="logout">Logout</a>
    </header>
    <aside>
        <ul>
            <li><a href="stud_viewtimetable.php">View Timetable</a></li>
            <li><a href="set_reminder.php">Set Reminders</a></li>
            <li><a href="student_profle.php">my profile</a></li>
            <li><a href="view_reminder1.php">view reminders</a></li>
            <li><a href="sampletimetable.php">sample table</a></li>
        </ul>
    </aside>
</body>
</html>