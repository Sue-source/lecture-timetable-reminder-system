<?php
// profile_update.php

// Connect to your MySQL database (replace with your credentials)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'lecture_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = '';

// Assuming you have a session or some identifier for the student
// Replace with actual logic to fetch the student's details
$student_id = 1; // Example student ID, replace with actual logic

// Fetch student details
$sql = "SELECT * FROM students WHERE id = $student_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
} else {
    echo "Student not found";
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];

    // Update student details in the database
    $update_sql = "UPDATE students SET name='$new_name', email='$new_email', phone='$new_phone' WHERE id=$student_id";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Profile updated successfully";
    } else {
        $message = "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0; /* Light gray background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #87CEEB; /* Sky blue background */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="email"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-bottom: 10px;
            color: #4CAF50;
            font-weight: bold;
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
    
    <div class="form-container">
        <h1>Update Profile</h1>
        <div class="message" id="message"><?php echo $message; ?></div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>

    <script>
        // Wait for the document to load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if message is not empty and display it
            var message = document.getElementById('message');
            if (message.innerHTML.trim() !== '') {
                message.style.display = 'block';
            }

            // Hide message after 5 seconds
            setTimeout(function() {
                message.style.display = 'none';
            }, 5000); // 5000 milliseconds = 5 seconds
        });
    </script>
</body>
</html>
