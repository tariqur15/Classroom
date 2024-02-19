<?php
session_start();

// Check if student is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: student_login.php");
    exit();
}

// Include your database connection code here if not done already
// For demonstration purposes, assuming your database connection is in a separate file named "db_connection.php"
include('db_connection.php');

// Retrieve the student's semester
$studentID = $_SESSION["student_studentID"];
$semesterQuery = "SELECT semester FROM students WHERE studentID = '$studentID'";
$semesterResult = mysqli_query($conn, $semesterQuery);

if ($semesterResult) {
    $row = mysqli_fetch_assoc($semesterResult);
    $semester = $row["semester"];
} else {
    // Handle error if necessary
    $semester = "Error";
}

// Retrieve tasks for the student's semester
$query = "SELECT * FROM tasks WHERE semester = '$semester' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$tasks = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Task Board</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #a633b8;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            background-color: #e6e6e6;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 0 10px;
        }

        .answer-btn {
            padding: 8px 16px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center
        }
    </style>
</head>
<body>

    <h2>Student Task Board</h2>

    <?php
    // Display tasks if any
    if (!empty($tasks)) {
        echo '<table>';
        echo '<tr>';
        echo '<th>Task ID</th>';
        echo '<th>Teacher</th>';
        echo '<th>Task Details</th>';
        echo '<th>Semester</th>';
        echo '<th>Created At</th>';
        echo '<th>Answer</th>';
        echo '</tr>';

        $slNo = 1;
        foreach ($tasks as $task) {
            echo '<tr>';
            echo '<td>' . $task["taskID"] . '</td>';
            echo '<td>' . $task["teacherName"] . '</td>';
            echo '<td>' . $task["task"] . '</td>';
            echo '<td>' . $task["semester"] . '</td>';
            $formattedDateTime = date("M j, Y h:i A", strtotime($task["created_at"]));
            echo '<td>' . $formattedDateTime . '</td>';
            // Add a form for submitting answers
            echo '<td> <a href="submit_answer.php?task_id=' . $task["taskID"] . '" class="answer-btn">Answer</a> </td>';

            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<p>No tasks available for your semester.</p>';
    }
    ?>

    <a href="welcome_student.php">Portal</a>
    <a href="student_login.html">Log Out</a>

</body>
</html>
