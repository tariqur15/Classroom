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

// Check if the form is submitted for notice update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_notice'])) {
    $noticeID = $_POST['notice_id'];
    $updatedContent = $_POST['updated_content'];

    // Perform the update operation (make sure to validate permissions if needed)
    $updateQuery = "UPDATE notices SET content = '$updatedContent' WHERE noticeID = '$noticeID'";
    mysqli_query($conn, $updateQuery);

    // Redirect to the notice board after update
    header("Location: teacher_notice_board.php");
    exit();
}

// Retrieve the notice details for update
if (isset($_GET['notice_id'])) {
    $noticeID = $_GET['notice_id'];
    $query = "SELECT * FROM notices WHERE noticeID = '$noticeID'";
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $noticeContent = $row['content'];
    } else {
        // Handle error if necessary
        $noticeContent = "Error fetching notice content.";
    }
} else {
    // Redirect to notice board if notice_id is not provided
    header("Location: teacher_notice_board.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Notice</title>
    <!-- Add your styles here -->
</head>
<body>

    <h2>Update Notice</h2>

    <form method="post" action="update_teacher_notice.php">
        <input type="hidden" name="notice_id" value="<?php echo $noticeID; ?>">
        <label for="updated_content">Updated Content:</label>
        <textarea id="updated_content" name="updated_content" rows="4" cols="50" required><?php echo $noticeContent; ?></textarea>

        <button type="submit" name="update_notice">Update Notice</button>
    </form>

    <br>
    <a href="teacher_notice_board.php">Back to Notice Board</a>

</body>
</html>
