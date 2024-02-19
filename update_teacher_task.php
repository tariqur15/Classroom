<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"]) || !isset($_SESSION["teacher_name"])) {
    header("Location: teacher_login.html");
    exit();
}

// Include your database connection code here if not done already
include('db_connection.php');

// Check if the form is submitted for task update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_task'])) {
    $taskID = $_POST['task_id'];
    $updatedTask = $_POST['updated_task'];

    // Perform the update operation (make sure to validate permissions if needed)
    $updateQuery = "UPDATE tasks SET task = '$updatedTask' WHERE taskID = '$taskID'";
    mysqli_query($conn, $updateQuery);

    // Redirect to the task board after update
    header("Location: teacher_task_board.php");
    exit();
}

// Retrieve the task details for update
if (isset($_GET['task_id'])) {
    $taskID = $_GET['task_id'];
    $query = "SELECT * FROM tasks WHERE taskID = '$taskID'";
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $taskDetails = $row['task'];
    } else {
        // Handle error if necessary
        $taskDetails = "Error fetching task details.";
    }
} else {
    // Redirect to task board if task_id is not provided
    header("Location: teacher_task_board.php");
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
    <title>Update Task</title>
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
            width: 50%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            background-color: #e6e6e6;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <h2>Update Task</h2>

    <form method="post" action="update_teacher_task.php">
        <input type="hidden" name="task_id" value="<?php echo $taskID; ?>">
        <label for="updated_task">Updated Task Details:</label>
        <textarea id="updated_task" name="updated_task" rows="4" required><?php echo $taskDetails; ?></textarea>

        <button type="submit" name="update_task">Update Task</button>
    </form>

    <a href="teacher_task_board.php">Back to Task Board</a>

</body>
</html>


