<?php

// Connect to the database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password_db  = "";
$dbname = "teachersdb";

$conn = new mysqli($servername, $username, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>