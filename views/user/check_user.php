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

// Fetch data for user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['id'];
    $sql = "SELECT * FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = array(
            'fullname' => $row['fullname'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'user_type' => $row['type']
        );
        echo json_encode($response);
    } else {
        $response = array('error' => 'Không tìm thấy khách hàng. Định danh của khách hàng không tồn tại.');
        echo json_encode($response);
    }
}
