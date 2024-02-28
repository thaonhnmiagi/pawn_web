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
    $pawn_id = $_POST['pawn_id'];
    $user_id = $_POST['user_id'];
    $type = $_POST['type'];
    $product_detail = $_POST['product_detail'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $pawn_status = $_POST['pawn_status'];

    $sql = "SELECT * FROM pawn_info";

    if ($product_detail !== '' || $pawn_status !== '') {
        $sql .= " INNER JOIN pawn_product_detail ON pawn_info.id = pawn_product_detail.pawn_info_id AND pawn_info.user_id = pawn_product_detail.user_id WHERE 1";
        if ($product_detail !== '') {
            $sql .= " AND pawn_product_detail.product_detail like '%$product_detail%'";
        }
        if ($pawn_status !== '') {
            $sql .= " AND pawn_product_detail.pawn_status = $pawn_status";
        }
    } else {
        $sql .= " WHERE 1";
    }

    if ($pawn_id !== '') {
        $sql .= " AND pawn_info.id = '$pawn_id'";
    }

    if ($user_id !== '') {
        $sql .= " AND pawn_info.user_id = '$user_id'";
    }

    if ($type !== '') {
        $sql .= " AND pawn_info.interest_rate_id = '$type'";
    }

    if ($start_date !== '') {
        $sql .= " AND pawn_info.start_date = '$start_date'";
    }

    if ($end_date !== '') {
        $sql .= " AND pawn_info.end_date = '$end_date'";
    }

    $result = $conn->query($sql);
    $response = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $interest_rate_id = $row['interest_rate_id'];
            $sqlInterestRates = "SELECT * FROM interest_rates WHERE id = '$interest_rate_id'";
            $resultInterestRates = $conn->query($sqlInterestRates);
            $rowInterestRates = $resultInterestRates->fetch_assoc();

            $pawnID = $row['id'];
            $userID = $row['user_id'];

            if (isset($row['product_detail'])) {
                $pawnDetailID = $row['pawn_detail_id'];
                $productDetail = $row['product_detail'];
                $pawnStatus = $row['pawn_status'];
            } else {
                $sqlPawnDetail = "SELECT * FROM pawn_product_detail WHERE user_id = '$userID' AND pawn_info_id = '$pawnID'";
                $resultPawnDetail = $conn->query($sqlPawnDetail);

                if ($resultPawnDetail->num_rows > 0) {
                    while ($rowDetail = $resultPawnDetail->fetch_assoc()) {
                        $pawnDetailID = $rowDetail['id'];
                        $productDetail = $rowDetail['product_detail'];
                        $pawnStatus = $rowDetail['pawn_status'];
                    }
                } else {
                    $pawnDetailID = '';
                    $productDetail = '';
                    $pawnStatus = '';
                }
            }

            $response[] = array(
                'id' => $pawnID,
                'user_id' => $userID,
                'interest_rate_name' => $rowInterestRates['name'],
                'pawn_detail_id' => $pawnDetailID,
                'product_detail' => $productDetail,
                'price' => $row['price'],
                'interest_rate_price' => $row['interest_rate_price'],
                'interest_rate_time' => $rowInterestRates['time'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'pawn_status' => $pawnStatus,
                'warehouse_id' => $row['warehouse_id']
            );
        }
        echo json_encode($response);
    } else {
        $response = array('error' => 'Không tìm thấy thông tin cầm đồ. Hãy kiểm tra lại thông tin.');
        echo json_encode($response);
    }
}
