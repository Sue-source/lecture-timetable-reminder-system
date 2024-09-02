<?php
session_start();

// Set the default time zone
date_default_timezone_set('Europe/Istanbul');

// Database connection (adjust these parameters as per your setup)
$host = 'localhost';
$dbname = 'lecture_db';
$username = 'root';
$password = '';

// Create a new mysqli connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query database to fetch user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    if ($stmt) {
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Valid credentials, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store user role in session

            // Insert login activity
            $activity_time = date('Y-m-d H:i:s');
            $stmt_activity = $conn->prepare("
                INSERT INTO user_activity (user_id, activity_type, activity_time) 
                VALUES (?, 'login', ?)
                ON DUPLICATE KEY UPDATE activity_time = VALUES(activity_time)
            ");
            if ($stmt_activity) {
                $stmt_activity->bind_param('is', $user['id'], $activity_time);
                $stmt_activity->execute();
            }

            // Redirect to dashboard based on role
            switch ($user['role']) {
                case 'Student':
                    header("Location: student_home.php");
                    exit();
                case 'University':
                    header("Location: Admin_home.php");
                    exit();
                case 'lecturer':
                    header("Location: lecturer_home.php");
                    exit();
                default:
                    // Handle unknown roles
                    header("Location: login_form.php?error=unknown_role");
                    exit();
            }
        } else {
            // Invalid credentials, redirect back to login form with error message
            header("Location: login_form.php?error=invalid_credentials");
            exit();
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            margin-top: 50px;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type=text], input[type=email], input[type=password] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>User Login</h2>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials'): ?>
            <p class="error">Invalid username/email or password.</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 'unknown_role'): ?>
            <p class="error">Unknown user role.</p>
        <?php endif; ?>
        <form action="login_form.php" method="post">
            <input type="text" name="username" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
