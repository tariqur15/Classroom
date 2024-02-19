<?php
session_start();
include('db_connection.php');

// Retrieve student ID from the session
$studentID = isset($_SESSION["student_studentID"]) ? $_SESSION["student_studentID"] : "Error";

// SQL query to check for new tasks
$query = "SELECT COUNT(*) AS newTasksCount
          FROM tasks
          WHERE semester = (SELECT semester FROM students WHERE studentID = '$studentID')
          AND created_at > (SELECT MAX(lastCheck) FROM student_notifications WHERE studentID = '$studentID')";

$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $newTasksCount = $row['newTasksCount'];

    // Update last check timestamp in the student_notifications table
    $updateTimestampQuery = "INSERT INTO student_notifications (studentID, lastCheck) VALUES ('$studentID', NOW())
                            ON DUPLICATE KEY UPDATE lastCheck = NOW()";

    $conn->query($updateTimestampQuery);

    echo $newTasksCount;
} else {
    echo "0";
}

$conn->close();
?>
