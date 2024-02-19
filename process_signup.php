<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $fullName = $_POST["fullName"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password

    // Connect to the database (replace with your database credentials)
    include('db_connection.php');

    // Insert data into the database
    $sql = "INSERT INTO teachers (fullName, email, password) VALUES ('$fullName', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the teacher login page with success message
        header("Location: teacher_login.html?signup=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    // Redirect to the signup form if accessed directly without form submission
    header("Location: teacher.html");
    exit();
}
?>