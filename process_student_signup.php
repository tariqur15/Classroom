<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection code here if not done already
    // For demonstration purposes, assuming your database connection is in a separate file named "db_connection.php"

    // Include database connection file
    include('db_connection.php');

    // Collect form data
    $fullName = $_POST["fullName"];
    $semester = $_POST["semester"];
    $studentID = $_POST["studentID"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password

    // Insert data into the database
    $query = "INSERT INTO students (fullName, semester, studentID, password) VALUES ('$fullName', '$semester', '$studentID', '$password')";

    if (mysqli_query($conn, $query)) {
        // Redirect to the student login page after successful signup
        header("Location: student_login.html");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Redirect to the signup form if accessed directly without form submission
    header("Location: process_student_signup.php");
    exit();
}
?>
