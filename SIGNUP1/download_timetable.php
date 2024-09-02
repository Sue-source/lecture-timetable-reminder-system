<?php
// Database connection
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

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=timetable.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('ID', 'Course Name', 'Course Code', 'Start Time', 'End Time', 'Venue', 'Lecturer', 'Faculty', 'Department'));

$sql = "SELECT * FROM timetable";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
?>
