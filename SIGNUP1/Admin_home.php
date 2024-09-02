<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header('Location: login_form.php');
        exit();
    }

    // Check if the user has the right role to access this page
    if ($_SESSION['role'] != 'University') {
        echo "You do not have permission to access this page.";
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        header {
            background-color: #007bff; /* Change to sky blue */
            padding: 2px;
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
            color: #333; /* Change link color */
        }
        nav ul li a:hover {
            color: #0056b3;
        }
        /* Add styles for the sidebar panel */
        .sidebar {
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 0px;
            width: 200px;
            position: fixed;
            left: 0;
            height: 100%;
            overflow-y: auto;
        }
        .sidebar ul {
            padding: 0;
        }
        .sidebar ul li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .sidebar ul li a {
            text-decoration:none;
            color: #333;
        }
        .sidebar ul li a:hover {
            color: #0056b3;
        }
        .content {
            margin-left: 260px; /* Adjust this value based on your sidebar width */
            padding: 20px;
        }
        #logout{

position:absolute;
top: 20px;
right: 20px;
border-radius:50%;
background-color:#04AA6D;
color:white;
padding: 10px;
text-decoration: none;
}
    </style>
</head>
<body>
    <header>
        <h1>Welcome, Admin!</h1>
        <a href="logout.php" id="logout">Logout</a>
    </header>
    <div class="sidebar">
        <ul>
            <li><a href="timetable.php">Display Timetable</a></li>
            <li><a href= "add_user.php">manage users</a></li>
            <li><a href= "admin_notification.php">notification</a></li>
            <li><a href= "report.php">Reports</a></li>
           
           
        </ul>
    </div>
    <div class="content">
        <!-- Your main content goes here -->
        <!-- Add other content as needed -->
    </div>
</body>
</html>

