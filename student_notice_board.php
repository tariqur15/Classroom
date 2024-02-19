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

// Retrieve notices for the student's semester
$query = "SELECT * FROM notices WHERE semester = '$semester' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$notices = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notices[] = $row;
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
    <title>Student Notice Board</title>
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
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        a {
            display: inline-block; /* Set display to inline-block */
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            background-color: #e6e6e6;
            padding: 10px 20px;
            border-radius: 5px;
            margin-right: 10px; /* Add margin-right for spacing */
        }

    </style>
</head>
<body>

    <h2>Notice Board</h2>

    <?php
    // Display notices if any
    if (!empty($notices)) {
        echo '<table>';
        echo '<tr>';
        echo '<th>SL No</th>';
        echo '<th>Teacher</th>';
        echo '<th>Notice</th>';
        echo '<th>Semester</th>';
        echo '<th>Created At</th>';
        echo '</tr>';

        $slNo = 1;
        foreach ($notices as $notice) {
            echo '<tr>';
            echo '<td>' . $slNo++ . '</td>';
            echo '<td>' . $notice["teacherName"] . '</td>';
            echo '<td>' . $notice["content"] . '</td>';
            echo '<td>' . $notice["semester"] . '</td>';
            $formattedDateTime = date("M j, Y h:i A", strtotime($notice["created_at"]));
            echo '<td>' . $formattedDateTime . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<p>No notices available for your semester.</p>';
    }
    ?>

<a href="welcome_student.php">Portal</a>     
<a href="student_login.html">Log Out</a>



</body>
</html>
