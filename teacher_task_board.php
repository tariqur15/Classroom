<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Task Board</title>

    
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
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
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
    margin-right: 5px; /* Adjust the right margin */
}

.update-button {
    background-color: #007bff;
    color: white;
    margin-left: 5px; /* Adjust the left margin */
}

    </style>
</head>
<body>



<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"]) || !isset($_SESSION["teacher_name"])) {
    header("Location: teacher_login.html");
    exit();
}

// Include your database connection code here if not done already
include('db_connection.php');

// Check if a notice is being deleted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_task'])) {
    $deleteTaskID = $_POST['delete_task_id'];

    // Perform the delete operation (make sure to validate permissions if needed)
    $deleteQuery = "DELETE FROM tasks WHERE taskID = '$deleteTaskID'";
    mysqli_query($conn, $deleteQuery);

    // Redirect to refresh the page after deletion
    header("Location: teacher_task_board.php");
    exit();
}

// Retrieve teacher information
$teacherID = $_SESSION["teacher_id"];
$teacherName = $_SESSION["teacher_name"];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $selectedSemester = isset($_GET["semester"]) ? $_GET["semester"] : "";

    // Modify the query to include semester filtering
    $query = "SELECT * FROM tasks WHERE teacherID = '$teacherID'";
    if (!empty($selectedSemester)) {
        $query .= " AND semester = '$selectedSemester'";
    }
    $query .= " ORDER BY created_at DESC";

    // Execute the modified query
    $result = mysqli_query($conn, $query);

    // Reset tasks array
    $tasks = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks[] = $row;
        }
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
    <title>Teacher Task Board</title>
    <style>
        /* Your existing styles remain unchanged */
    </style>
</head>
<body>

    <h2>Teacher Task Board</h2>

    <!-- Add the filter form to select semester -->
    <form method="GET" action="">
        <label for="semester">Filter by Semester:</label>
        <select name="semester" id="semester">
            <option value="">All Semesters</option>
            <?php
            $semesters = array_unique(array_column($tasks, 'semester'));
            foreach ($semesters as $semesterOption) {
                echo '<option value="' . $semesterOption . '"';
                if ($semesterOption == $selectedSemester) {
                    echo ' selected';
                }
                echo '>' . $semesterOption . '</option>';
            }
            ?>
        </select>
        <input type="submit" value="Filter">
    </form>

    <?php
    // Display tasks if any
    if (!empty($tasks)) {
        echo '<table>';
        echo '<tr>';
        echo '<th>Task ID</th>';
        echo '<th>Semester</th>';
        echo '<th>Task Details</th>';
        echo '<th>Created At</th>';
        echo '<th>Actiont</th>';
        echo '</tr>';

        foreach ($tasks as $task) {
            echo '<tr>';
            echo '<td>' . $task["taskID"] . '</td>';
            echo '<td>' . $task["semester"] . '</td>';
            echo '<td>' . $task["task"] . '</td>';
            $formattedDateTime = date("M j, Y h:i A", strtotime($task["created_at"]));
            echo '<td>' . $formattedDateTime . '</td>';
            echo '<td class="button-container">';
        echo '<form method="post" action="teacher_task_board.php">';
        echo '<input type="hidden" name="delete_task_id" value="' . $task["taskID"] . '">';
        echo '<button class="delete-button" type="submit" name="delete_task">Delete</button>';
        echo '</form>';
        echo '<a class="update-button" href="update_teacher_task.php?task_id=' . $task["taskID"] . '">Update</a>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<p>No tasks available.</p>';
    }
    ?>

    <br>
    <a href="create_task.php">Create Task</a>
    
    <a href="welcome_teacher.php">Go Back Portal</a>
    
    <a href="teacher_login.html">Log Out</a>
    

</body>
</html>
