<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect login credentials
    $studentID = $_POST["studentID"];
    $password = $_POST["password"];

    // Connect to the database (replace with your database credentials)
    include('db_connection.php');

    // Retrieve hashed password and student ID from the database
    $sql = "SELECT id, studentID, password FROM students WHERE studentID = '$studentID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, set session variables or redirect to a welcome page
            session_start();
            $_SESSION["student_id"] = $row["id"];
            $_SESSION["student_studentID"] = $row["studentID"];
            header("Location: welcome_student.php");
            exit();
        } else {
            echo "<p>Invalid password</p>";
        }
    } else {
        echo "<p>Student with student Id '$studentID' not found</p>";
    }

    $conn->close();
}
?>
