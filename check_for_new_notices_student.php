<?php
session_start();
include('db_connection.php');

// Retrieve student ID from the session
$studentID = isset($_SESSION["student_studentID"]) ? $_SESSION["student_studentID"] : "Error";

// SQL query to check for new notices
$query = "SELECT COUNT(*) AS newNoticesCount
          FROM notices
          WHERE semester = (SELECT semester FROM students WHERE studentID = '$studentID')
          AND created_at > (SELECT MAX(lastCheck) FROM notice_board_notifications WHERE studentID = '$studentID')";

$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $newNoticesCount = $row['newNoticesCount'];

    // Update last check timestamp in the notice_board_notifications table
    $updateTimestampQuery = "INSERT INTO notice_board_notifications (studentID, lastCheck) VALUES ('$studentID', NOW())
                            ON DUPLICATE KEY UPDATE lastCheck = NOW()";

    $conn->query($updateTimestampQuery);

    echo $newNoticesCount;
} else {
    echo "0";
}

$conn->close();
?>
