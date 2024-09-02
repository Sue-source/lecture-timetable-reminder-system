<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lecture_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Users (Example of adding multiple users at once)
if (isset($_POST['add_users'])) {
    $users = array(
        array('username' => 'user1', 'email' => 'user1@example.com', 'role' => 'student'),
        array('username' => 'user2', 'email' => 'user2@example.com', 'role' => 'lecturer'),
        // Add more users as needed
    );
    
    foreach ($users as $user) {
        $username = $user['username'];
        $email = $user['email'];
        $role = $user['role'];
        
        // Check if username already exists
        $sql_check = "SELECT * FROM users WHERE username='$username'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // Skipping insertion if username already exists
            continue;
        } else {
            // Insert new user
            $sql = "INSERT INTO users (username, email, role) VALUES ('$username', '$email', '$role')";
            $conn->query($sql);
        }
    }
}

// Remove User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_user'])) {
    $user_id = $_POST['user_id'];

    // Delete related records in reminders table first
    $sql_delete_reminders = "DELETE FROM reminders WHERE user_id='$user_id'";
    if ($conn->query($sql_delete_reminders) === TRUE) {
        // Now delete related records in user_notifications table
        $sql_delete_notifications = "DELETE FROM user_notifications WHERE user_id='$user_id'";
        if ($conn->query($sql_delete_notifications) === TRUE) {
            // Now delete the user
            $sql = "DELETE FROM users WHERE id='$user_id'";
            $conn->query($sql);
        }
    }
}

// Fetch Students
$sql_students = "SELECT * FROM users WHERE role='student'";
$result_students = $conn->query($sql_students);

// Fetch Lecturers
$sql_lecturers = "SELECT * FROM users WHERE role='lecturer'";
$result_lecturers = $conn->query($sql_lecturers);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<h2>Admin Manage Users</h2>

<form method="POST" action="">
    <button type="submit" name="add_users">Add Users</button>
</form>

<h3>Students</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result_students->num_rows > 0) {
        while($row = $result_students->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"]. "</td>
                    <td>" . $row["username"]. "</td>
                    <td>" . $row["email"]. "</td>
                    <td>
                        <form method='POST' action='' style='display:inline;'>
                            <input type='hidden' name='user_id' value='" . $row["id"]. "'>
                            <button type='submit' name='remove_user'>Remove</button>
                        </form>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No students found</td></tr>";
    }
    ?>
</table>

<h3>Lecturers</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result_lecturers->num_rows > 0) {
        while($row = $result_lecturers->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"]. "</td>
                    <td>" . $row["username"]. "</td>
                    <td>" . $row["email"]. "</td>
                    <td>
                        <form method='POST' action='' style='display:inline;'>
                            <input type='hidden' name='user_id' value='" . $row["id"]. "'>
                            <button type='submit' name='remove_user'>Remove</button>
                        </form>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No lecturers found</td></tr>";
    }
    ?>
</table>

</body>
</html>
