<?php
session_start();

// Check if the student is not logged in, redirect to the login page
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.html");
    exit();
}

// Log out logic
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: student_login.html");
    exit();
}

// Include your database connection code here if not done already
// For demonstration purposes, assuming your database connection is in a separate file named "db_connection.php"
include('db_connection.php');




// Retrieve student details from the database
$studentID = $_SESSION["student_studentID"];
$query = "SELECT fullName, semester FROM students WHERE studentID = '$studentID'";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $fullName = $row["fullName"];
    $semester = $row["semester"];
} else {
    // Handle error if necessary
    $fullName = "Error";
    $semester = "Error";
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
<script>
    $(document).ready(function() {
        // Check for new tasks and notices every 10 seconds
        setInterval(checkForNewTasks, 10000);
        function checkForNewTasks() {
            $.ajax({
                url: 'check_for_new_tasks_student.php',
                type: 'GET',
                success: function(taskResponse) {
                    updateNotificationBadge('taskBadge', taskResponse);
                },
                error: function(xhr, status, error) {
                    console.error('Error checking for new tasks:', error);
                }
            });

            // Check for new notices
            $.ajax({
                url: 'check_for_new_notices_student.php',
                type: 'GET',
                success: function(noticeResponse) {
                    updateNotificationBadge('noticeBadge', noticeResponse);
                },
                error: function(xhr, status, error) {
                    console.error('Error checking for new notices:', error);
                }
            });
        }

        function updateNotificationBadge(badgeId, newItemsCount) {
            var badge = $('#' + badgeId);

            if (newItemsCount > 0) {
                badge.text(newItemsCount);
                badge.show(); // Show the badge
            } else {
                badge.hide(); // Hide the badge
            }
        }
    });
</script>




    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        h2 {
            color: #3498db;
        }
        button {
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        #taskButton {
            background-color: #581845; /* Purple color */
            color: #fff;
            position: relative;
        }
        #taskButton, #noticeButton {
    position: relative;
}

#taskBadge, #noticeBadge {
    background-color: #ff5733;
    color: white;
    border-radius: 50%;
    padding: 4px 8px;
    font-size: 12px;
    position: absolute;
    top: -8px;
    right: -8px;
}

        #historyButton {
            background-color: #27ae60; /* Green color */
            color: #fff;
        }
        #noticeButton {
            background-color: #34495e; /* Dark Blue color */
            color: #fff;
        }
        #logoutButton {
            background-color: #e74c3c; /* Red color */
            color: #fff;
        }
        /* CSS to make the image circular */
        .profile-image {
            width: 200px; /* Adjust the width as needed */
            height: 200px; /* Adjust the height as needed */
            border-radius: 50%;
            display: block;
            margin: 0 auto; /* Center the image horizontally */
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo $fullName; ?>!</h2>
    <img src="https://webportal.ugv.edu.bd/Studentphoto/<?php echo $studentID; ?>.JPG" alt="Student Photo" class="profile-image">
    <p>Student ID: <?php echo $studentID; ?></p>
    <p>Semester: <?php echo $semester; ?></p>

     <!-- Buttons to navigate to other pages -->
    <button id="taskButton" onclick="location.href='student_task_board.php'">
        Task Board <span class="task-count-badge" id="taskBadge">0</span>
    </button>
    <button id="historyButton" onclick="location.href='task_history.php'">Task History</button>
    <button id="noticeButton" onclick="location.href='student_notice_board.php'">
    Notice Board <span class="notice-count-badge" id="noticeBadge">0</span>
</button>


    <!-- Add more content or features to the profile page as needed -->

    <br><br>
     <!-- Log Out Button -->
     <form method="post">
        <button id="logoutButton" type="submit" name="logout">Log Out</button>
    </form>

</body>
</html>