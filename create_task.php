<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"]) || !isset($_SESSION["teacher_name"])) {
    header("Location: teacher_login.html");
    exit();
}

// Include your database connection code here if not done already
// For demonstration purposes, assuming your database connection is in a separate file named "db_connection.php"
include('db_connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $semester = $_POST["semester"];
    $taskContent = $_POST["taskContent"];

    // Insert task into the database
    $teacherID = $_SESSION["teacher_id"];
    $teacherName = $_SESSION["teacher_name"];

    $query = "INSERT INTO tasks (teacherID, teacherName, semester, task) VALUES ('$teacherID', '$teacherName', '$semester', '$taskContent')";

    if (mysqli_query($conn, $query)) {
        $success_message = "Task created and sent successfully.";
    } else {
        $error_message = "Error creating task: " . mysqli_error($conn);
    }
}

// Retrieve the list of semesters for the dropdown
$semesterQuery = "SELECT DISTINCT semester FROM students";
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
    <title>Teacher Task</title>
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

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 20px;
            color: green;
        }

        .error-message {
            margin-top: 20px;
            color: red;
        }
    </style>
</head>
<body>

    <h2>Create Task</h2>

    <?php
    // Display success or error message if any
    if (isset($success_message)) {
        echo '<p class="message">' . $success_message . '</p>';
    } elseif (isset($error_message)) {
        echo '<p class="error-message">' . $error_message . '</p>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="semester">Select Semester:</label>
        <select id="semester" name="semester" required>
            <option value="">None</option>
            <?php
            foreach ($semesters as $sem) {
                echo '<option value="' . $sem . '">' . $sem . '</option>';
            }
            ?>
        </select>

        <label for="taskContent">Task Details:</label>
        <textarea id="taskContent" name="taskContent" rows="4" required></textarea>

        <button type="submit">Create Task</button>
    </form>

    <br>
    <a href="teacher_task_board.php">Go to Task Board</a>  |  <a href="welcome_teacher.php">Go Portal</a>   |   <a href="teacher_login.html">Log Out</a>

</body>
</html>
