<?php

$HOSTNAME = "localhost";
$USERNAME = "root";
$PASSWORD = "";
$DATABASE = "lecture_db";

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Detect if the script is running in a web server context or CLI
if (php_sapi_name() !== 'cli') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if the form fields are set
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {
            // Sanitize form data to prevent SQL injection
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $role = mysqli_real_escape_string($conn, $_POST['role']);

            // SQL query to fetch information of registered users and find user match.
            $query = "SELECT username, password, role FROM users WHERE username=? AND password=? AND role=? LIMIT 1";

            // Create a prepared statement
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $username, $password, $role);

            // Execute the statement
            $stmt->execute();

            // Bind the result to variables
            $stmt->bind_result($username, $password, $role);

            // Fetch the result. If a row is returned, the user credentials are valid
            if ($stmt->fetch()) {
                // User credentials are valid. Start a new session and store the user's username and role in the session variables
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // Redirect the user to their respective dashboard
                if ($role == 'student') {
                    header("location: studentdashboard.php");
                } elseif ($role == 'university') {
                    header("location: universitydashboard.php");
                } elseif ($role == 'lecturer') {
                    header("location: lecture_home.php");
                }
                exit();
            } else {
                // User credentials are not valid. Show an error message
                echo "Invalid credentials.";
            }

            // Close the statement and the connection
            $stmt->close();
        }
    }
} else {
    // CLI context, nothing to do here for this specific script
    // You can include logging or other CLI-specific logic if needed
    echo "This script is not intended to run in CLI mode.";
}

?>
