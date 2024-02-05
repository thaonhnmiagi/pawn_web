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

// display_image.php
$imageId = $_GET['id'];
$sql = "SELECT image FROM pawn_info WHERE id = '$imageId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imageData = $row['image'];

    header("Content-Type: image/jpeg");
    echo $imageData;
} else {
    echo "Image not found";
}
