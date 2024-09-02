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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Timetable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #50b3a2;
            color: #fff;
            padding: 20px;
            border-bottom: #2980b9 3px solid;
            text-align: center;
        }
        header h1 {
            margin: 0;
        }
        table {
            width: 50%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #50b3a2;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .download-btn {
            background: #2980b9;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
        .download-btn:hover {
            background: #1f6391;
        }
    </style>
</head>
<body>
    <header>
        <h1>Timetable</h1>
    </header>
    <div class="container">
        <h2>Timetable</h2>
        <table>
            <tr>
                
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Venue</th>
                <th>Lecturer</th>
                <th>Faculty</th>
                <th>Department</th>
                <th>day of lecture</th>
            </tr>
            <?php
            $sql = "SELECT * FROM timetable";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        
                            <td>{$row['course_name']}</td>
                            <td>{$row['course_code']}</td>
                            <td>{$row['start_time']}</td>
                            <td>{$row['end_time']}</td>
                            <td>{$row['venue']}</td>
                            <td>{$row['lecturer']}</td>
                            <td>{$row['faculty']}</td>
                            <td>{$row['department']}</td>
                            <td>{$row['day']}</td>
                        </tr>";
                }
            }
            $conn->close();
            ?>
        </table>
        <a href="download_timetable.php" class="download-btn">Download Timetable</a>
    </div>
</body>
</html>
