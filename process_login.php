<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect login credentials
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Connect to the database (replace with your database credentials)
    include('db_connection.php');
    // Retrieve hashed password from the database
    $sql = "SELECT id, fullName, email, password FROM teachers WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, set session variables or redirect to a welcome page
            session_start();
            $_SESSION["teacher_id"] = $row["id"];
            $_SESSION["teacher_name"] = $row["fullName"];
            header("Location: welcome_teacher.php");
            exit();
        } else {
            echo "<p>Invalid password</p>";
        }
    } else {
        echo "<p>Teacher with email '$email' not found</p>";
    }

    $conn->close();
}
?>
