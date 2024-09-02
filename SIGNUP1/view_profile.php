<?php
// profile_view.php
// Connect to database
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

// Assuming you have a session or some identifier for the student
// Replace with actual logic to fetch the student's details
$student_id = 1; // Example student ID, replace with actual logic

// Fetch student details
$sql = "SELECT * FROM students WHERE id = $student_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9;'>";
        echo "<h2 style='color: #333; text-align: center;'>Student Profile</h2>";
        echo "<p style='color: #555;'><strong>Name:</strong> " . $row["name"]. "</p>";
        echo "<p style='color: #555;'><strong>Email:</strong> " . $row["email"]. "</p>";
        echo "<p style='color: #555;'><strong>Phone:</strong> " . $row["phone"]. "</p>";
        echo "</div>";
    }
} else {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9; text-align: center;'>";
    echo "<p style='color: #333;'>No results found</p>";
    echo "</div>";
}

$conn->close();
?>
