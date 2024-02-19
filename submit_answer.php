
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grammar Checker</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 50px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #581845;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-button {
            background-color: #000000;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 0 10px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        .result {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .corrected-text {
            margin-top: 10px;
            color: #008000;
            font-weight: bold;
        }

        .mistakes {
            color: #ff0000;
            margin-top: 10px;
        }

        .mistake-details {
            margin-top: 10px;
            color: #ff0000;
        }
    </style>
</head>
<body>

<form action="" method="post">
    <label for="inputText">Enter Text:</label>
    <textarea id="inputText" name="inputText" required></textarea>

    <?php
    // Assuming task_id is passed through the URL
    $taskID = isset($_GET['task_id']) ? $_GET['task_id'] : '';
    echo '<input type="hidden" name="task_id" value="' . $taskID . '">';
    ?>

    <button type="submit">Submit</button>
    <button class="back-button" onclick="location.href='student_task_board.php'">Back</button>
</form>
</body>
</html>

<?php
// Start the session
session_start();

// Include database connection
include('db_connection.php');





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve student information and task ID
    $studentInfo = getStudentInfo($_SESSION['student_studentID']);
    $taskID = $_POST['task_id'];  // Retrieve task ID from the form

    // Define submission key
    $submissionKey = 'submission_' . $studentInfo['studentID'] . '_' . $taskID;

    // Check if the submission has already been made for this task ID in the session
    if (isset($_SESSION[$submissionKey]) && $_SESSION[$submissionKey]) {
        echo '<div class="result"> You have already submitted for this task.</div>';
        exit; // Exit to prevent further processing
    }

    // Check if the submission has already been made for this task ID in the database
    // Assuming you have a database connection
    $query = "SELECT * FROM submissions WHERE studentID = '{$studentInfo['studentID']}' AND taskID = '$taskID'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<div class="result"> You have already submitted for this task.</div>';
        exit; // Exit to prevent further processing
    }
}
    


// Function to retrieve student information from the 'students' table
function getStudentInfo($studentID)
{
    global $conn; // Use the global connection variable

    $studentID = mysqli_real_escape_string($conn, $studentID);

    $query = "SELECT studentID, fullName AS studentName, semester FROM students WHERE studentID = '$studentID'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    } else {
        return false;
    }
}

// Function to store data in the 'answers' table
function storeAnswerInDatabase($studentID, $studentName, $semester, $taskID, $inputText, $correctedText, $mistakeCount, $mistakeDetails)
{
    global $conn; // Use the global connection variable

    $studentID = mysqli_real_escape_string($conn, $studentID);
    $studentName = mysqli_real_escape_string($conn, $studentName);
    $semester = mysqli_real_escape_string($conn, $semester);
    $taskID = mysqli_real_escape_string($conn, $taskID);
    $inputText = mysqli_real_escape_string($conn, $inputText);
    $correctedText = mysqli_real_escape_string($conn, $correctedText);
    $mistakeCount = mysqli_real_escape_string($conn, $mistakeCount);
    $mistakeDetails = mysqli_real_escape_string($conn, $mistakeDetails);

    $query = "INSERT INTO answers (studentID, studentName, semester, taskID, inputText, correctedText, mistakeCount, mistakeDetails, timeStamp)
              VALUES ('$studentID', '$studentName', '$semester', '$taskID', '$inputText', '$correctedText', '$mistakeCount', '$mistakeDetails', NOW())";

    if (mysqli_query($conn, $query)) {
        echo '<div class="result">Task response stored successfully.</div>';
    } else {
        echo '<div class="result">Error storing task response: ' . mysqli_error($conn) . '</div>';
    }
}



// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... Your existing code ...

    // API call and grammar check logic here
    $apiUrl = 'http://127.0.0.1:5000/';
    $params = [
        'input' => $_POST['inputText'],
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo '<div class="result">Error: API call failed - ' . curl_error($ch) . '</div>';
    } else {
        // Decode the JSON response
        $data = json_decode($response, true);

        // Display input, corrected text, and mistakes count
        if (isset($data['input']) && isset($data['corrected_text']) && isset($data['mistakes'])) {
            echo '<div class="result">';
            echo '<br><strong>Input:</strong> ' . htmlspecialchars($data['input']) . '<br>';
            echo '<div class="corrected-text"><strong>Corrected Text:</strong> ' . htmlspecialchars($data['corrected_text']) . '</div>';
            echo '<div class="mistakes"><strong>Mistakes Count:</strong> ' . $data['mistakes'] . '</div>';

            // Display mistake details if available
            if (isset($data['mistake_details'])) {
                echo '<div class="mistake-details"><strong>Mistake Details:</strong>';
                foreach ($data['mistake_details'] as $mistake) {
                    echo '<div>' . htmlspecialchars($mistake['message']) . '</div>';
                }
                echo '</div>';
            }

            echo '</div>';

            // Retrieve student information
            $studentInfo = getStudentInfo($_SESSION['student_studentID']);

            // Check if the submission has already been made for this task ID in the database
            $query = "SELECT * FROM submissions WHERE studentID = '{$studentInfo['studentID']}' AND taskID = '$taskID'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                // Insert the submission into the submissions table
                $insertQuery = "INSERT INTO submissions (studentID, taskID) VALUES ('{$studentInfo['studentID']}', '$taskID')";
                if (mysqli_query($conn, $insertQuery)) {
                    // Mark the submission as made in the session
                    $_SESSION[$submissionKey] = true;

// Store data in the 'answers' table
if ($studentInfo) {
    // Store mistake details if available
    $storedMistakeDetails = '';
    if (isset($data['mistake_details'])) {
        $mistakeMessages = [];
        foreach ($data['mistake_details'] as $mistake) {
            $mistakeMessages[] = htmlspecialchars($mistake['message']);
        }
        $storedMistakeDetails = implode('<br>', $mistakeMessages);
    }

    // Now you can use $storedMistakeDetails as needed, for example, storing it in a database.
    // Example: $db->insert('your_table', ['mistakes' => $data['mistakes'], 'mistake_details' => $storedMistakeDetails]);

    storeAnswerInDatabase(
        $studentInfo['studentID'],
        $studentInfo['studentName'],
        $studentInfo['semester'],
        $taskID,
        $data['input'],
        $data['corrected_text'],
        $data['mistakes'],
        $storedMistakeDetails
    );
} else {
                        echo '<div class="result">Error: Student information not found.</div>';
                    }
                } else {
                    echo '<div class="result">Error storing submission in the database: ' . mysqli_error($conn) . '</div>';
                }
            } else {
                echo '<div class="result">You have already submitted for this task.</div>';
            }
        } else {
            echo '<div class="result">Error: Invalid API response format.</div>';
        }

        // Close the API call connection
        curl_close($ch);
    }
}
?>