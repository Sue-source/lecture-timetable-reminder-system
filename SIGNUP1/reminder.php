<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "User ID not found in session. Please make sure you are logged in.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $lecture = $conn->real_escape_string($_POST['lecture']);
    $reminder_date = $conn->real_escape_string($_POST['reminder_date']);
    $reminder_time = $conn->real_escape_string($_POST['reminder_time']);

    // Insert the reminder into the database
    $sql = "INSERT INTO reminders (user_id, lecture, reminder_date, reminder_time)
            VALUES ('$user_id', '$lecture', '$reminder_date', '$reminder_time')";
    if ($conn->query($sql) === TRUE) {
        // Schedule notification
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    try {
                        const reminder_datetime = new Date('$reminder_date $reminder_time');
                        const currentTime = new Date();
                        const timeDiff = reminder_datetime.getTime() - currentTime.getTime();

                        console.log('Reminder DateTime:', reminder_datetime);
                        console.log('Current Time:', currentTime);
                        console.log('Time Difference (ms):', timeDiff);

                        if (timeDiff > 0) {
                            setTimeout(function() {
                                if (Notification.permission === 'granted') {
                                    showNotification('$lecture');
                                } else if (Notification.permission !== 'denied') {
                                    Notification.requestPermission().then(function(permission) {
                                        if (permission === 'granted') {
                                            showNotification('$lecture');
                                        }
                                    });
                                }
                            }, timeDiff);
                        } else {
                            console.error('Scheduled time is in the past');
                        }
                    } catch (error) {
                        console.error('Error scheduling notification:', error);
                    }
                });
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set Reminder</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #87CEEB;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="time"],
        .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .form-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <h1>Set Reminder for Upcoming Lecture</h1>
            <input type="text" name="lecture" placeholder="Lecture Name" required>
            <input type="date" name="reminder_date" required>
            <input type="time" name="reminder_time" required>
            <button type="submit">Set Reminder</button>
        </form>
    </div>
    <script>
        function showNotification(lecture) {
            const notification = new Notification('Reminder', {
                body: `Lecture '${lecture}' is about to start. Be prepared!`,
                icon: 'icon.png' // Optionally, you can add an icon
            });

            notification.onclick = function() {
                window.focus();
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        });
    </script>
</body>
</html>
