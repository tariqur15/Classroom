<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.html");
    exit();
}

// Log out logic
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: teacher_login.html");
    exit();
}

echo "<script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>";
echo "<script>
    $(document).ready(function() {
        // Check for new responses every 10 seconds
        setInterval(checkForNewResponses, 10000);

        function checkForNewResponses() {
            $.ajax({
                url: 'check_for_new_responses.php', // Create a new PHP file for checking new responses
                type: 'GET',
                success: function(response) {
                    if (response > 0) {
                        // Update the button HTML with the count
                        $('#taskResponseButton').html('Task Response <span class=\"response-count-badge\">' + response + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking for new responses:', error);
                }
            });
        }
    });
</script>";





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Teacher</title>
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
        #createTaskButton {
            background-color: #7030A0; /* Blue color */
            color: #fff;
        }
        #taskResponseButton {
            background-color: #27ae60; /* Green color */
            color: #fff;
        }
        #createNoticeButton {
            background-color: #34495e; /* Dark blue color */ 
            color: #fff;
        }
        #logoutButton {
            background-color: #e74c3c; /* Red color */
            color: #fff;
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo $_SESSION["teacher_name"]; ?>!</h2>
    <p>This is your welcome page. Explore The Options.</p>

    <!-- Buttons to navigate to other pages -->
    <button id="createTaskButton" onclick="location.href='create_task.php'">Create Task</button>
    <?php
echo "<style>
#taskResponseButton {
    position: relative;
}

#taskResponseButton .response-count-badge {
    background-color: #ff5733;
    color: white;
    border-radius: 50%;
    padding: 4px 8px;
    font-size: 12px;
    position: absolute;
    top: -8px;
    right: -8px;
}
</style>";



echo "<button id='taskResponseButton' onclick=\"location.href='student_task_response.php'\">
Task Response
</button>";


      ?>

    <button id="createNoticeButton" onclick="location.href='create_notice.php'">Notice</button>

    <!-- Log Out Button -->
    <form method="post">
        <button id="logoutButton" type="submit" name="logout">Log Out</button>
    </form>

</body>
</html>
