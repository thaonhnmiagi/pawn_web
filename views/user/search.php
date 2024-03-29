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

// Fetch data for select options 'interest_rates'
$typeOptions = array();
$sql = "SELECT * FROM interest_rates";
$resultSelect = $conn->query($sql);
if ($resultSelect->num_rows > 0) {
    while ($row = $resultSelect->fetch_assoc()) {
        $typeOptions[] = $row;
    }
}

if (isset($_GET['pawnDetailID']) || isset($_GET['pawnInfoID'])) {
    $pawn_detail_id = $_GET['pawnDetailID'];
    $pawn_info_id = $_GET['pawnInfoID'];
    $pawn_status = 2; // 0: Hết thời gian gia hạn, 1: Trong thời gian gia hạn, 2: đã (xóa) trả hàng và thanh toán

    $sqlPawnInfo = "SELECT * FROM pawn_info WHERE id = '$pawn_info_id'";
    $result = $conn->query($sqlPawnInfo);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $interest_rate_id = $row['interest_rate_id'];
        $start_date = $row['start_date'];
        $end_date =  $row['end_date'];
        $extend_date = $row['extend_date'];
        $price = $row['price'];
        $interest_rate_price = $row['interest_rate_price'];

        if ($pawn_detail_id) {
            $sqlPawnDetail = "DELETE FROM pawn_product_detail WHERE id = '$pawn_detail_id'";

            if (mysqli_query($conn, $sqlPawnDetail)) {
                deletePawnInfoAndInsertHistory($conn, $user_id, $pawn_info_id, $pawn_detail_id, $interest_rate_id, $pawn_status, $start_date, $end_date, $extend_date, $price, $interest_rate_price);
            } else {
                echo "Error: " . $sqlPawnDetail . "<br>" . mysqli_error($conn);
            }
        } else {
            deletePawnInfoAndInsertHistory($conn, $user_id, $pawn_info_id, $pawn_detail_id, $interest_rate_id, $pawn_status, $start_date, $end_date, $extend_date, $price, $interest_rate_price);
        }
    }
}

function deletePawnInfoAndInsertHistory($conn, $user_id, $pawn_info_id, $pawn_detail_id, $interest_rate_id, $pawn_status, $start_date, $end_date, $extend_date, $price, $interest_rate_price)
{
    $sql = "DELETE FROM pawn_info WHERE id = '$pawn_info_id'";
    if (mysqli_query($conn, $sql)) {
        $history_id = time() . mt_rand(1000, 9999);
        $insert_at = date('Y-m-d H:i:s');
        $prepayment = 0;
        $profit = $price * ($interest_rate_price / 100) + $price;

        $queryHistory = "INSERT INTO history VALUES ($history_id, $user_id, '$pawn_info_id', '$pawn_detail_id', '$interest_rate_id', $pawn_status, '$start_date', '$end_date', '$extend_date', $price, 0, '$interest_rate_price', $prepayment, $profit, '$insert_at');";
        if (mysqli_query($conn, $queryHistory)) {
            header("Location: /views/user/search.php");
        } else {
            echo "Error: " . $queryHistory . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
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
    <link rel="stylesheet" href="/web/css/search.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</head>

<body>
    <section id="header">
        <a href="#"><img src="/web/img/logo.png" class="logo" alt=""></a>

        <div class="navbar-header">
            <ul id="navbar">
                <li><a href="/views/home/index.php"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                <?php
                if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
                    echo '<li><a href="register_user.php">Đăng ký khách hàng</a></li>';
                    echo '<li><a href="register_pawn_info.php">Đăng ký cầm đồ</a></li>';
                    echo '<li><a href="/views/user/dashboard.php">Thống kê</a></li>';
                }
                ?>
                <li><a class="active" href="search.php">Tìm kiếm</a></li>
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
                        <input type="text" id="id" name="id" placeholder="Nhập CMND">
                        <i class="fa-solid fa-id-card user_id"></i>
                    </div>

                    <div class="input_box">
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu">
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
        <div class="search_form">
            <form action="" method="post" enctype="multipart/form-data">
                <h2>Tìm kiếm thông tin cầm đồ</h2>
                <div id="error_message" style="color: red;"></div>
                <div class="input_box">
                    <input type="text" id="input_pawn_id" name="id" placeholder="Nhập ID của món hàng" oninput="searchInfo()">
                    <i class="fa-solid fa-id-card input_user_id"></i>
                </div>

                <div class="input_box">
                    <input type="text" id="input_user_id" name="id" placeholder="Nhập CMND" oninput="searchInfo()">
                    <i class="fa-solid fa-id-card input_user_id"></i>
                </div>

                <div class="select_box">
                    <select name="type" id="type" onchange="searchInfo()">
                        <option value="" selected hidden>Chọn loại hàng hóa</option>
                        <?php
                        foreach ($typeOptions as $option) {
                            echo "<option value=\"{$option['id']}\" data-price=\"{$option['price']}\" data-time=\"{$option['time']}\" data-type=\"{$option['type']}\">{$option['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="input_box">
                    <input type="text" id="product_detail" name="id" placeholder="Nhập chi tiết sản phẩm" oninput="searchInfo()">
                    <i class="fa-solid fa-gift product"></i>
                </div>

                <div class="input_box">
                    <input type="text" id="start_date" name="start_date" placeholder="Ngày bắt đầu" oninput="searchInfo()">
                    <i class="fa-regular fa-calendar-days time start_date"></i>
                </div>

                <div class="input_box">
                    <input type="text" id="end_date" name="end_date" placeholder="Ngày kết thúc" oninput="searchInfo()">
                    <i class="fa-regular fa-calendar-days time end_date"></i>
                </div>

                <div class="select_box">
                    <select name="id" id="pawn_status" onchange="searchInfo()">
                        <option value="" selected hidden>Chọn trạng thái sản phẩm</option>
                        <option value="0">Hết thời gian gia hạn</option>
                        <option value="1">Trong thời gian gia hạn</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="search_table">
            <div class="print-button-container">
                <button id="previewPrint">In hóa đơn</button>
            </div>

            <table id="searchTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Loại hàng hóa</th>
                        <th>Chi tiết sản phẩm</th>
                        <th>Giá cầm</th>
                        <th>Thuế cầm</th>
                        <th>Thời gian cầm</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày hết hạn</th>
                        <th>Trạng thái sản phẩm</th>
                        <th>Kho giữ hàng</th>
                        <th class="actions">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="printDialog" class="dialog-overlay hidden">
            <div class="dialog-content">
                <button id="closePreview" class="close-preview-button">
                    <i class="fas fa-times"></i>
                </button>
                <table id="previewTable" class="preview-table">
                </table>
                <div class="print-button-container" style="width: 100%;">
                    <button id="printToPaper">In hóa đơn</button>
                </div>
            </div>
        </div>

        <div id="confirmationModal" class="modal" style="display: none;">
            <div class="modal-content">
                <i class="fa-solid fa-xmark close"></i>
                <p>Bạn có chắc chắn muốn xóa thông tin cầm đồ này?</p>
                <button id="confirmDelete">Có</button>
                <button id="cancelDelete">Không</button>
            </div>
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
    <script src="/web/js/search.js"></script>
</body>

</html>