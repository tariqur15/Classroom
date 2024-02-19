<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"])) {
    header("Location: teacher_login.html");
    exit();
}

// Include your database connection code here if not done already
// For demonstration purposes, assuming your database connection is in a separate file named "db_connection.php"
include('db_connection.php');

// Check if a notice is being deleted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_notice'])) {
    $deleteNoticeID = $_POST['delete_notice_id'];

    // Perform the delete operation (make sure to validate permissions if needed)
    $deleteQuery = "DELETE FROM notices WHERE noticeID = '$deleteNoticeID'";
    mysqli_query($conn, $deleteQuery);

    // Redirect to refresh the page after deletion
    header("Location: teacher_notice_board.php");
    exit();
}

// Retrieve notices from the database
$teacherID = $_SESSION["teacher_id"];
$query = "SELECT * FROM notices WHERE teacherID = '$teacherID' ORDER BY created_at DESC";
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
    <title>Teacher Notice Board</title>
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
            text-align: center;
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

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .delete-button,
        .update-button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-button {
            background-color: #ff3333;
            color: white;
            font-weight: bold;
        }

        .update-button {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <h2>Notice Board</h2>

    <table>
      

        <?php
    // Display notices if any
    if (!empty($notices)) {
        echo '<table>';
        echo '<tr>';
        echo '<th>SL No</th>';
        echo '<th>Notice</th>';
        echo '<th>Semester</th>';
        echo '<th>Created At</th>';
        echo '<th>Actiont</th>';
        echo '</tr>';

        $slNo = 1;
        foreach ($notices as $notice) {
        echo '<tr>';
        echo '<td>' . $slNo++ . '</td>';
        echo '<td>' . $notice["content"] . '</td>';
        echo '<td>' . $notice["semester"] . '</td>';
        $formattedDateTime = date("M j, Y h:i A", strtotime($notice["created_at"]));
        echo '<td>' . $formattedDateTime . '</td>';
        echo '<td class="button-container">';
        echo '<form method="post" action="teacher_notice_board.php">';
        echo '<input type="hidden" name="delete_notice_id" value="' . $notice["noticeID"] . '">';
        echo '<button class="delete-button" type="submit" name="delete_notice">Delete</button>';
        echo '</form>';
        echo '<a class="update-button" href="update_teacher_notice.php?notice_id=' . $notice["noticeID"] . '">Update</a>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No notices available.</td></tr>';
}
?>

    </table>

    <br>
    <a href="teacher_login.html">Log Out</a>
    <a href="create_notice.php">Create New Notice</a>
    <a href="welcome_teacher.php">Portal</a>

</body>
</html>
