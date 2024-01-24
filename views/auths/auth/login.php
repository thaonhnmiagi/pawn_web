<?php
session_start();

$hostname = "localhost";
$username = "root";
$password = "";
$database = "webpawn";

// Database connection
$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE id = '$id' AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['type'] == "admin") {
        $_SESSION['user'] = 'admin';
        header("Location: /views/home/index.php");
    } else {
        $_SESSION['user'] = 'customer';
        header("Location: /views/home/index.php");
    }
} else {
    echo "Invalid username or password.";
}

$conn->close();
