<?php
// profile_process.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Validate data (you can add more validation as needed)
    // Example: Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Connect to your database (adjust the database details)
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

    // Prepare SQL query to insert data into the database
    $sql = "INSERT INTO students (name, email, phone) VALUES ('$name', '$email', '$phone')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <style>
    /* Basic styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f8ff; /* Sky blue background */
        text-align: center;
    }
    form {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    input[type="text"], input[type="email"], input[type="tel"] {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        box-sizing: border-box;
    }
    input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .profile-link {
        margin-top: 20px;
    }
    .profile-link a {
        display: inline-block;
        margin: 10px;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
    }
    .profile-link a:hover {
        background-color: #45a049; /* Darker shade on hover */
    }
</style>

</head>
<body>
    <form action="student_profile.php" method="post">
        <h2>Student Profile</h2>
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="phone">Phone:</label><br>
        <input type="tel" id="phone" name="phone" required><br><br>
        
        <input type="submit" value="Submit">
    </form>

    <!-- Link to view profile -->
    <div class="profile-link">
        <a href="view_profile.php">View Profile</a>
        <a href="update_profile.php">update Profile</a>
    
    </div>
</body>
</html>
