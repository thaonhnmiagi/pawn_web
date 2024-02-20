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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $pawnInfoID = $_GET['id'];
    $sql = "SELECT * FROM pawn_info WHERE id = '$pawnInfoID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $userId = $row['user_id'];

            $sqlDetail = "SELECT * FROM pawn_product_detail WHERE user_id = '$userId' AND pawn_info_id = '$pawnInfoID'";
            $resultDetail = $conn->query($sqlDetail);

            if ($resultDetail->num_rows > 0) {
                $rowDetail = $resultDetail->fetch_assoc();
                $productDetail = $rowDetail['product_detail'];
                $pawnStatus = $rowDetail['pawn_status'];
            }
        }
    }
}

// Register/Update pawn detail
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id'])) {
    $user_id = $_POST['user_id'];
    $pawn_info_id = $_GET['id'];
    $product_detail = $_POST['product_detail'];
    $pawn_status = $_POST['pawn_status'];
    $pawn_detail_id = '';

    $sqlDetail = "SELECT * FROM pawn_product_detail WHERE user_id = '$user_id' AND pawn_info_id = '$pawn_info_id'";
    $resultDetail = $conn->query($sqlDetail);

    if ($resultDetail->num_rows > 0) {
        $pawn_detail_id = $resultDetail->fetch_assoc()['id'];
        $query = "UPDATE pawn_product_detail SET product_detail = '$product_detail', pawn_status = '$pawn_status' WHERE user_id = '$user_id' AND pawn_info_id = '$pawn_info_id'";
    } else {
        $pawn_detail_id = $user_id . mt_rand(1000, 9999);
        $query = "INSERT INTO pawn_product_detail VALUES ('$pawn_detail_id', $user_id, '$pawn_info_id', '$product_detail', '$pawn_status');";
    }

    if (mysqli_query($conn, $query)) {
        $sql = "SELECT * FROM pawn_info WHERE id = '$pawn_info_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $interest_rate_id = $row['interest_rate_id'];
                $start_date = $row['start_date'];
                $end_date = $row['end_date'];
                $price = $row['price'];
                $interest_rate_price = $row['interest_rate_price'];

                $history_id = time() . mt_rand(1000, 9999);
                $queryHistory = "INSERT INTO history VALUES ($history_id, $user_id, '$pawn_info_id', '$pawn_detail_id', '$interest_rate_id', $pawn_status, '$start_date', '$end_date', '', $price, '$interest_rate_price', '');";

                if (mysqli_query($conn, $queryHistory)) {
                    header("Location: /views/user/search.php");
                } else {
                    echo "Error: " . $queryHistory . "<br>" . mysqli_error($conn);
                }
            }
        }
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Trang Web Vay, Cầm Cố</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
        <link rel="stylesheet" href="/web/css/style.css">
        <link rel="stylesheet" href="/web/css/register_pawn_info.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>

    <body>
        <section id="header">
            <a href="#"><img src="/web/img/logo.png" class="logo" alt=""></a>

            <div>
                <ul id="navbar">
                    <li><a href="/views/home/index.php"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                    <?php
                    if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
                        echo '<li><a href="register_user.php">Đăng ký khách hàng</a></li>';
                        echo '<li><a href="register_pawn_info.php">Đăng ký cầm đồ</a></li>';
                        if (!empty($productDetail)) {
                            echo '<li><a class="active" href="register_pawn_detail.php">Cập nhật chi tiết thông tin cầm đồ</a></li>';
                        } else {
                            echo '<li><a class="active" href="register_pawn_detail.php">Thêm chi tiết thông tin cầm đồ</a></li>';
                        }
                        echo '<li><a href="search.php">Tìm kiếm</a></li>';
                    }
                    ?>
                    <li><a href="about.html">Về chúng tôi</a></li>
                    <li><a href="/views/home/contact.php">Liên hệ</a></li>
                    <li id="user_login"><a href="#" id="form_open"><i class="fa-solid fa-user"></i></a></li>
                    <a href="#" id="close"><i class="fa-solid fa-xmark"></i></a>
                </ul>
            </div>

            <div id="mobile">
                <a href="#" id="mobile_form_open"><i class="fa-solid fa-user"></i></a>
                <i id="bar" class="fas fa-outdent"></i>
            </div>
        </section>

        <section class="login">
            <div class="form_container">
                <i class="fa-solid fa-xmark form_close"></i>
                <!-- Login Form -->
                <div class="form login_form">
                    <form action="/views/auths/auth/login.php" method="post">
                        <h2>Đăng nhập</h2>
                        <div class="input_box">
                            <input type="text" id="id" name="id" placeholder="Nhập CMND" required>
                            <i class="fa-solid fa-id-card user_id"></i>
                        </div>

                        <div class="input_box">
                            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                            <i class="fa-solid fa-lock password"></i>
                            <i class="fa-solid fa-eye-slash password_hide"></i>
                        </div>

                        <div class="option_fiels">
                            <span class="checkbox">
                                <input type="checkbox" id="check">
                                <label for="check">Lưu</label>
                            </span>
                            <a href="#" class="forgot_pw">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="button">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </section>

        <section>
            <div class="register_pawn_form">
                <form action="" method="post" enctype="multipart/form-data">
                    <h2><?php echo (!empty($productDetail)) ? 'Cập nhật' : 'Đăng ký'; ?> chi tiết thông tin cầm đồ</h2>
                    <div class="input_box">
                        <input type="text" id="input_user_id" name="user_id" placeholder="Nhập CMND" value="<?php echo $userId ?>" required readonly>
                        <i class="fa-solid fa-id-card input_user_id"></i>
                        <div id="error_message" style="color: red;"></div>
                    </div>

                    <div class="input_box">
                        <input type="text" id="pawn_info_id" name="pawn_info_id" placeholder="Nhập CMND" value="<?php echo $pawnInfoID ?>" required readonly>
                        <i class="fa-solid fa-id-card input_user_id"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="product_detail" name="product_detail" placeholder="Nhập chi tiết cầm đồ" <?php echo (!empty($productDetail)) ? 'value="' . $productDetail . '"' : 'value=""'; ?> required>
                        <i class="fa-solid fa-info input_user_id"></i>
                    </div>

                    <div class="select_box">
                        <select name="pawn_status" id="pawn_status" required>
                            <option value="" selected>Chọn loại hàng hóa</option>
                            <option value="0" <?php echo (isset($pawnStatus) && $pawnStatus === '0') ? 'selected' : ''; ?>>Hết thời gian gia hạn</option>
                            <option value="1" <?php echo (isset($pawnStatus) && $pawnStatus === '1') ? 'selected' : ''; ?>>Trong thời gian gia hạn</option>
                        </select>
                    </div>

                    <button type="submit" class="button" name="submit"><?php echo (!empty($productDetail)) ? 'Cập nhật' : 'Đăng ký'; ?></button>
                </form>
            </div>
        </section>

        <footer class="section-p1">
            <div class="col">
                <img class="logo" src="/web/img/logo.png">
                <h4>Liên hệ</h4>
                <p><strong>Địa chỉ:</strong> 53 đường 37 KDC Vạn Phúc, Phường Hiệp Bình Phước, Thủ Đức</p>
                <p><strong>Số điện thoại:</strong> (+84) 2466 602 846</p>
                <p><strong>Thời gian:</strong> 08:00 - 17:50, Thứ Hai - Thứ Sáu</p>
            </div>

            <div class="col condition">
                <h4>Điều khoản & Điều kiện</h4>
                <a href="#">Điều khoản & Điều kiện</a>
                <a href="#">Chính sách bảo mật thông tin</a>
                <a href="#">Chính sách bảo quản tài sản</a>
            </div>

            <div class="col support">
                <h4>Hỗ trợ khách hàng</h4>
                <a href="#">Câu hỏi thường gặp</a>
                <a href="#">Liên hệ</a>
            </div>

            <div class="copyright">
                <p>Copyright ©Miagi Solution 2024</p>
            </div>
        </footer>

        <script src="/web/js/script.js"></script>
    </body>

    </html>
<?php
} else {
    header('Location: index.php');
}
?>