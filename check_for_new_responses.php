<?php
session_start();
include('db_connection.php');

$teacherName = $_SESSION["teacher_name"];

// SQL query to check for new responses
$query = "SELECT COUNT(*) AS newResponses
          FROM tasks
          JOIN answers ON tasks.taskID = answers.taskID
          WHERE tasks.teacherName = '$teacherName'
          AND answers.timeStamp > (SELECT MAX(lastCheck) FROM teacher_notifications WHERE teacherName = '$teacherName')";

$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $newResponses = $row['newResponses'];

    // Update last check timestamp in the teacher_notifications table
    $updateTimestampQuery = "INSERT INTO teacher_notifications (teacherName, lastCheck) VALUES ('$teacherName', NOW())
                            ON DUPLICATE KEY UPDATE lastCheck = NOW()";

    $conn->query($updateTimestampQuery);

    echo $newResponses;
} else {
    echo "0";
}

$conn->close();
?>
