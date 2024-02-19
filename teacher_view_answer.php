<?php
session_start();

// Include your database connection code here if not done already
include('db_connection.php');

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"]) || !isset($_SESSION["teacher_name"])) {
    header("Location: teacher_login.html");
    exit();
}

// Check if taskID and studentID are provided as parameters
if (!isset($_GET["taskID"]) || !isset($_GET["studentID"])) {
    echo "Invalid parameters";
    exit();
}

$taskID = $_GET["taskID"];
$studentID = $_GET["studentID"];

// SQL query to fetch data from answers table
$query = "SELECT tasks.task, answers.taskID, answers.studentName, answers.studentID, answers.inputText, answers.correctedText, answers.mistakeCount, answers.mistakeDetails
          FROM answers
          JOIN tasks ON answers.taskID = tasks.taskID
          WHERE answers.taskID = $taskID AND answers.studentID = $studentID";

$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching data: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Answer</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #900C3F;
        }

        .answer-container {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: block;
            width: 50%;



        }

        p {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }

        strong {
            color: #01060d;
        }

        .mistake-count {
            color: #ff0000;
            font-weight: bold;
        }

        .mistake-details {
            color: #ff0000;
        }

        a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            background-color: #e6e6e6;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 0 10px;
            display: inline-block;
        }

        .corrected-answer {
            margin-top: 10px;
            color: #008000;
            font-weight: bold;
        }


    </style>
</head>
<body>
    <h2>View Answer</h2>

    <?php
    if ($result) {
        // Display the data
        echo '<div class="answer-container">';
        echo "<p><strong>Student Name:</strong> " . $row['studentName'] . "</p>";
        echo "<p><strong>Student ID:</strong> " . $row['studentID'] . "</p>";
        echo '</div>';
        echo '<div class="answer-container">';
        echo "<p><strong>Task:</strong> " . $row['task'] . "</p>";
        echo "<p><strong>Answer:</strong> " . $row['inputText'] . "</p>";
        echo "<p><strong>Corrected Answer:</strong> <span class='corrected-answer'>" . $row['correctedText'] . "</span></p>";
        echo '</div>';
        echo '<div class="answer-container">';
        echo "<p><strong>Mistake Count:</strong> <span class='mistake-count'>" . $row['mistakeCount'] . "</span></p>";
        echo "<p><strong>Mistake Details:</strong> <br> <br> <span class='mistake-details'>" . $row['mistakeDetails'] . "</span></p>";
        echo '</div>';
    } else {
        echo "<p>Error fetching data: " . mysqli_error($conn) . "</p>";
    }
    ?>

    <br>
    <a href="student_task_response.php">Back</a>
    <a href="teacher_login.html">Log Out</a>
</body>
</html>