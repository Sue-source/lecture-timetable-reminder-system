<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header('Location: login_form.php');
        exit();
    }

    // Check if the user has the right role to access this page
    if ($_SESSION['role'] != 'Student') {
        echo "You do not have permission to access this page.";
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>student Dashboard</title>
    <link rel="stylesheet" href="student.css">

</head>
<body>
    <?php include'student_sidebar.php';?>
    <div class="content">
        <h1> </h1>
        <!-- Your main content goes here -->
        <!-- Add other content as needed -->
    </div>
</body>
</html>

