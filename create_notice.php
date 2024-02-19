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

// Initialize error message
$error_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $semester = $_POST["semester"];
    $noticeContent = $_POST["noticeContent"];

    // Check if "None" is selected
if ($semester === 'none') {
    $error_message = "Please select a semester.";
} else {
    // Modify the query to handle "All Semester"
    if ($semester === 'all') {
        // Insert notice into the database for all semesters (1 to 8)
        $teacherID = $_SESSION["teacher_id"];
        $teacherName = $_SESSION["teacher_name"];

        $query = "INSERT INTO notices (teacherID, teacherName, semester, content) 
                  SELECT DISTINCT '$teacherID', '$teacherName', semester, '$noticeContent' FROM students WHERE semester BETWEEN 1 AND 8";

        if (mysqli_query($conn, $query)) {
            $success_message = "Notice created and sent successfully to all semesters.";
        } else {
            $error_message = "Error creating notice: " . mysqli_error($conn);
        }
    } else {
        // Insert notice into the database for a specific semester
        $teacherID = $_SESSION["teacher_id"];
        $teacherName = $_SESSION["teacher_name"];

        $query = "INSERT INTO notices (teacherID, teacherName, semester, content) 
                  VALUES ('$teacherID', '$teacherName', '$semester', '$noticeContent')";

        if (mysqli_query($conn, $query)) {
            $success_message = "Notice created and sent successfully.";
        } else {
            $error_message = "Error creating notice: " . mysqli_error($conn);
        }
    }
}
}


// Retrieve the list of semesters for the dropdown
$semesterQuery = "SELECT DISTINCT semester FROM students WHERE semester BETWEEN 1 AND 8";
$semesterResult = mysqli_query($conn, $semesterQuery);
$semesters = [];

if ($semesterResult) {
    while ($row = mysqli_fetch_assoc($semesterResult)) {
        $semesters[] = $row["semester"];
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
    <link rel="stylesheet" type="text/css" href="style.css">

    <title>Teacher Notice Board</title>
    <!-- Add your styles here -->
</head>
<body>

    <h2>Create Notice</h2>

    <?php
    // Display success or error message if any
    if (isset($success_message)) {
        echo '<p style="color: green;">' . $success_message . '</p>';
    } elseif (isset($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="semester">Select Semester:</label>
        <select id="semester" name="semester" required>
            <option value="none">None</option>
            <option value="all">All Semester</option>
            <?php
            foreach ($semesters as $sem) {
                echo '<option value="' . $sem . '">' . $sem . '</option>';
            }
            ?>
        </select>

        <br><br>

        <label for="noticeContent">Notice Content:</label>
        <textarea id="noticeContent" name="noticeContent" rows="4" cols="50" required></textarea>

        <button type="submit">Create Notice</button>
    </form>

    <br>
    <a href="teacher_notice_board.php">Go to Notice Board</a>
    <br><br>
    <a href="teacher_login.html">Log Out</a>

</body>
</html>
