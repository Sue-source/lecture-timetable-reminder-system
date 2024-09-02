<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lecture_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Handle form submission to add new timetable entry
        $course_name = $_POST['course_name'];
        $course_code = $_POST['course_code'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $venue = $_POST['venue'];
        $lecturer = $_POST['lecturer'];
        $faculty = $_POST['faculty'];
        $department = $_POST['department'];
        $day = isset($_POST['day']) ? $_POST['day'] : ''; // Add day input field

        $sql = "INSERT INTO timetable (course_name, course_code, start_time, end_time, venue, lecturer, faculty, department, day)
                VALUES ('$course_name', '$course_code', '$start_time', '$end_time', '$venue', '$lecturer', '$faculty', '$department', '$day')";
        if ($conn->query($sql) === TRUE) {
            echo "New record added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    if (isset($_POST['edit'])) {
        // Handle form submission to edit timetable entry
        $id = $_POST['id'];
        $course_name = $_POST['course_name'];
        $course_code = $_POST['course_code'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $venue = $_POST['venue'];
        $lecturer = $_POST['lecturer'];
        $faculty = $_POST['faculty'];
        $department = $_POST['department'];
        $day = isset($_POST['day']) ? $_POST['day'] : ''; // Add day input field

        $sql = "UPDATE timetable SET course_name='$course_name', course_code='$course_code', start_time='$start_time', end_time='$end_time', venue='$venue', lecturer='$lecturer', faculty='$faculty', department='$department', day='$day' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        // Handle form submission to delete timetable entry
        $id = $_POST['id'];
        $sql = "DELETE FROM timetable WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Fetch and display timetable data
$sql = "SELECT * FROM timetable";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Timetable Management</title>
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
        .form-container {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 0 10px #ccc;
            max-width: 500px;
            margin: 20px auto;
        }
        form input, form button {
            display: block;
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
        }
        form button {
            background: #50b3a2;
            border: 0;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            width: calc(50% - 20px);
            float: left;
            margin-right: 20px;
        }
        form button:nth-child(2) {
            float: right;
            margin-right: 0;
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
        .btn {
            padding: 5px 10px;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
    
        }
        .btn-edit {
            background: #2980b9;
        }
        .btn-delete {
            background: red;
    
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Timetable</h1>
    </header>
    <div class="container">
        <div class="form-container">
            <form method="post" action="timetable.php">
                <input type="hidden" name="id" id="id">
                <input type="text" name="course_name" id="course_name" placeholder="Course Name">
                <input type="text" name="course_code" id="course_code" placeholder="Course Code">
                <input type="time" name="start_time" id="start_time">
                <input type="time" name="end_time" id="end_time">
                <input type="text" name="venue" id="venue" placeholder="Venue">
                <input type="text" name="lecturer" id="lecturer" placeholder="Lecturer">
                <input type="text" name="faculty" id="faculty" placeholder="Faculty">
                <input type="text" name="department" id="department" placeholder="Department">
                <input type="text" name="day" id="day" placeholder="Day of Lecture">
                <div class="clearfix">
                    <button type="submit" name="add">Add</button>
                    <button type="submit" name="edit">Edit</button>
                </div>
            </form>
        </div>
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
                <th>Day of lecture</th>
                <th>Actions</th>
            </tr>
            <?php
            // Display timetable rows
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
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
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <button type='submit' name='delete' class='btn btn-delete'>Delete</button>
                                </form>
                                <button class='btn btn-edit' onclick='editEntry(".json_encode($row).")'>Edit</button>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No records found</td></tr>";
            }

            // Close MySQLi connection
            $conn->close();
            ?>
        </table>
    </div>
    <script>
        function editEntry(row) {
            document.getElementById('id').value = row.id;
            document.getElementById('course_name').value = row.course_name;
            document.getElementById('course_code').value = row.course_code;
            document.getElementById('start_time').value = row.start_time;
            document.getElementById('end_time').value = row.end_time;
            document.getElementById('venue').value = row.venue;
            document.getElementById('lecturer').value = row.lecturer;
            document.getElementById('faculty').value = row.faculty;
            document.getElementById('department').value = row.department;
            document.getElementById('day').value = row.day;
        }
    </script>
</body>
</html>
