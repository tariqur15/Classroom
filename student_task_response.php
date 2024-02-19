<?php

session_start();

// Check if teacher is logged in
if (!isset($_SESSION["teacher_id"]) || !isset($_SESSION["teacher_name"])) {
    header("Location: teacher_login.html");
    exit();
}

// Include your database connection code here if not done already
include('db_connection.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve teacher information
$teacherID = $_SESSION["teacher_id"];
$teacherName = $_SESSION["teacher_name"];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $selectedSemester = isset($_GET["semester"]) ? $_GET["semester"] : "";

// SQL query to fetch task details and answer details using JOIN with ORDER BY clause
$fullQuery = "SELECT tasks.taskID, tasks.task, answers.studentID, answers.studentName, answers.semester, answers.mistakeCount, answers.timeStamp
              FROM tasks
              JOIN answers ON tasks.taskID = answers.taskID
              WHERE tasks.teacherName = '$teacherName'";

// Add semester filter
if (!empty($selectedSemester)) {
    $fullQuery .= " AND answers.semester = '$selectedSemester'";
}

// Order the results by timestamp in descending order
$fullQuery .= " ORDER BY answers.timeStamp DESC";

$resultFull = $conn->query($fullQuery);

    echo '<div style="text-align: right; margin-top: 10px;">
    <a href="welcome_teacher.php">Go Portal</a>  | <a href="teacher_task_board.php">Go Task Board</a>| <a href="teacher_login.html">Logout</a> 
          </div>';

    echo '<h2>Student Response</h2>';

    // Display the filter form
    echo '<div style="text-align: center;">
            <form method="GET" action="">
                <label for="semester">Filter by Semester:</label>
                <select name="semester" id="semester">
                    <option value="">All Semesters</option>';
    $uniqueSemesters = $conn->query("SELECT DISTINCT semester FROM answers");
    while ($rowSemester = $uniqueSemesters->fetch_assoc()) {
        $semesterOption = $rowSemester['semester'];
        echo '<option value="' . $semesterOption . '"';
        if ($semesterOption == $selectedSemester) {
            echo ' selected';
        }
        echo '>' . $semesterOption . '</option>';
    }
    echo '</select>
                <input type="submit" value="Filter">
            </form>
        </div>';

    if ($resultFull->num_rows > 0) {
        echo "<style>
                h2 {
                    text-align: center;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th, td {
                    border: 1px solid #dddddd;
                    text-align: center;
                    padding: 8px;
                }

                th {
                    background-color: #4CAF50;
                    color: white;
                }

                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                .view-answer-btn {
                    background-color: #581845;
                    color: white;
                    border: none;
                    padding: 8px 15px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 14px;
                    margin: 4px 2px;
                    cursor: pointer;
                    border-radius: 4px;
                    
                }
            </style>";

        echo "<table>";
        echo "<tr>
                <th>TaskID</th>
                <th>Task</th>
                <th>StudentID</th>
                <th>StudentName</th>
                <th>Semester</th>
                <th>MistakeCount</th>
                <th>TimeStamp</th>
                <th>Answer</th>
              </tr>";

        // Output data of each row
        while ($rowFull = $resultFull->fetch_assoc()) {
            // Output the desired data as table rows
            echo "<tr>
            <td>" . $rowFull['taskID'] . "</td>
            <td>" . $rowFull['task'] . "</td>
            <td>" . $rowFull['studentID'] . "</td>
            <td>" . $rowFull['studentName'] . "</td>
            <td>" . $rowFull['semester'] . "</td>
            <td>" . $rowFull['mistakeCount'] . "</td>
            <td>" . date("M j, Y h:i A", strtotime($rowFull['timeStamp'])) . "</td>

            <td><button class='view-answer-btn' onclick=\"location.href='teacher_view_answer.php?taskID=" . $rowFull['taskID'] . "&studentID=" . $rowFull['studentID'] . "'\">View Answer</button></td>
          </tr>";
        }

        echo "</table>";
    } else {
        echo "0 results";
    }
}

// Close the database connection
$conn->close();

?>


