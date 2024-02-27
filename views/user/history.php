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
        <link rel="stylesheet" href="/web/css/history.css">
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
                        echo '<li><a class="active" href="history.php">Lịch sử</a></li>';
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
            <!-- <div class="search_form">
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

                    <div class="input_box">
                        <input type="text" id="pawn_status" name="id" placeholder="Nhập trạng thái sản phẩm" oninput="searchInfo()">
                        <i class="fa-solid fa-gift product"></i>
                    </div>
                </form>
            </div> -->

            <div class="history_table">
                <div class="print-button-container">
                    <button id="previewPrint">In hóa đơn</button>
                </div>
                <table id="historyTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Loại hàng hóa</th>
                            <th>Chi tiết sản phẩm</th>
                            <th>Giá cầm</th>
                            <th>Thuế cầm</th>
                            <th>Trạng thái sản phẩm</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày hết hạn</th>
                            <th>Ngày gia hạn</th>
                            <th>Kho giữ hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['userID']) && isset($_GET['pawnInfoID'])) {
                            $user_id = $_GET['userID'];
                            $pawn_info_id = $_GET['pawnInfoID'];
                            $sql = "SELECT history.*, pawn_info.warehouse_id, interest_rates.name";

                            if (isset($_GET['pawnDetailID']) && !empty($_GET['pawnDetailID'])) {
                                $sql .= ", pawn_product_detail.product_detail
                                FROM history
                                JOIN pawn_info ON history.pawn_info_id = pawn_info.id
                                JOIN interest_rates ON history.interest_rate_id = interest_rates.id
                                LEFT JOIN pawn_product_detail ON history.pawn_detail_id = pawn_product_detail.id
                                WHERE history.user_id = '$user_id' AND history.pawn_info_id = '$pawn_info_id';";
                            } else {
                                $sql .= " FROM history
                                JOIN pawn_info ON history.pawn_info_id = pawn_info.id
                                JOIN interest_rates ON history.interest_rate_id = interest_rates.id
                                WHERE history.user_id = '$user_id' AND history.pawn_info_id = '$pawn_info_id';";
                            }

                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $start_date = date("d-m-Y", strtotime($row['start_date']));
                                    $end_date = date("d-m-Y", strtotime($row['end_date']));
                                    $extend_date = $row['extend_date'] != '0000-00-00 00:00:00' ? date("d-m-Y", strtotime($row['extend_date'])) : '00-00-0000';

                                    echo '<tr>';
                                    echo '<td>' . $row['id'] . '</td>';
                                    echo '<td>' . $row['user_id'] . '</td>';
                                    echo '<td>' . $row['name'] . '</td>';
                                    echo '<td>' . (!empty($_GET['pawnDetailID']) ? $row['product_detail'] : '') . '</td>';
                                    echo '<td>' . $row['price'] . '</td>';
                                    echo '<td>' . $row['interest_rate_price'] . '</td>';
                                    echo '<td>' . ($row['status'] === '0' ? 'Hết thời gian gia hạn' : ($row['status'] === '1' ? 'Trong thời gian gia hạn' : 'Đã trả hàng và thanh toán')) . '</td>';
                                    echo '<td>' . $start_date . '</td>';
                                    echo '<td>' . $end_date . '</td>';
                                    echo '<td>' . $extend_date . '</td>';
                                    echo '<td>' . $row['warehouse_id'] . '</td>';
                                    echo '</tr>';
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="printDialog" class="dialog-overlay hidden">
                <div class="dialog-content">
                    <button id="closePreview" class="close-preview-button">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="previewTable-content">
                        <table id="previewTable" class="preview-table">
                        </table>
                    </div>
                    <div class="print-button-container" style="width: 100%;">
                        <button id="printToPaper">In hóa đơn</button>
                    </div>
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
        <script src="/web/js/history.js"></script>
    </body>

    </html>
<?php
} else {
    header('Location: index.php');
}
?>