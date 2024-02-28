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

$data = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $period = $_POST['period'];

    if ($period === 'custom') {
        $formatted_start_date = $_POST['start_date'];
        $formatted_end_date = $_POST['end_date'];
        $start_date = (new DateTime($formatted_start_date))->format('Y-m-d');
        $end_date = (new DateTime($formatted_end_date))->format('Y-m-d');
        $date_condition = "AND insert_at BETWEEN '$start_date' AND '$end_date'";
    } else {
        $current_date = date('Y-m-d H:i:s');

        if ($period === 'week') {
            $one_week_ago = date('Y-m-d H:i:s', strtotime('-1 week', strtotime($current_date)));
            $date_condition = "AND insert_at BETWEEN '$one_week_ago' AND '$current_date'";
        } else if ($period === 'month') {
            $one_month_ago = date('Y-m-d H:i:s', strtotime('-1 month', strtotime($current_date)));
            $date_condition = "AND insert_at BETWEEN '$one_month_ago' AND '$current_date'";
        } else {
            $one_year_ago = date('Y-m-d H:i:s', strtotime('-1 year', strtotime($current_date)));
            $date_condition = "AND insert_at BETWEEN '$one_year_ago' AND '$current_date'";
        }
    }

    $query = "SELECT 
        user_id,
        pawn_info_id,
        COALESCE(SUM(loan_amount), 0) AS total_loan_amount,
        SUM(
            CASE
                WHEN prepayment > 0 THEN prepayment
                WHEN prepayment = 0 AND profit IS NOT NULL AND status = 0 THEN profit - COALESCE(loan_amount, 0)
                ELSE 0
            END
        ) AS total_interest_rate,
        (SUM(
            CASE
                WHEN prepayment > 0 THEN prepayment
                WHEN prepayment = 0 AND profit IS NOT NULL AND status = 0 THEN profit - COALESCE(loan_amount, 0)
                ELSE 0
            END
        ) - COALESCE(SUM(loan_amount), 0)) AS total_profit
    FROM history
    WHERE 1 $date_condition
    GROUP BY user_id, pawn_info_id;
    ";

    $result = mysqli_query($conn, $query);

    $total_loan_amount = 0;
    $total_interest_rate = 0;
    $total_profit = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $total_loan_amount += $row["total_loan_amount"];
            $total_interest_rate += $row["total_interest_rate"];
            $total_profit += $row["total_profit"];
        }
        $data[] = array(
            "loan_amount_label" => 'Số tiền đã cho vay',
            "total_loan_amount" => $total_loan_amount,
            "interest_rate_label" => 'Số tiền thu về',
            "total_interest_rate" => $total_interest_rate,
            "profit_label" => 'Lợi nhuận',
            "total_profit" => $total_profit
        );
        echo json_encode($data);
    } else {
        echo json_encode(array("error" => "Không tìm thấy dữ liệu nào."));
    }
    exit;
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
        <link rel="stylesheet" href="/web/css/dashboard.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        echo '<li><a class="active" href="/views/user/dashboard.php">Thống kê</a></li>';
                    }
                    ?>
                    <li><a href="/views/user/search.php">Tìm kiếm</a></li>
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
            <div class="dashboard_form">
                <form id="statisticsForm" action="" method="post" enctype="multipart/form-data">
                    <h2>Thống kê doanh thu</h2>
                    <div class="row">

                        <div class="select_box">
                            <select name="period" id="period">
                                <option value="custom">Tùy chỉnh</option>
                                <option value="week">Tuần này</option>
                                <option value="month">Tháng này</option>
                                <option value="year">Năm nay</option>
                            </select>
                        </div>

                        <div class="input_box">
                            <input type="text" id="start_date" name="start_date" placeholder="Ngày bắt đầu">
                            <i class="fa-regular fa-calendar-days time start_date"></i>
                        </div>

                        <div class="input_box">
                            <input type="text" id="end_date" name="end_date" placeholder="Ngày kết thúc">
                            <i class="fa-regular fa-calendar-days time end_date"></i>
                        </div>
                    </div>

                    <button type="submit" class="button" name="submit">Tính toán thống kê</button>
                    <div id="dashboard_error_message" style="color: red;"></div>
                </form>

                <canvas id="myPieChart"></canvas>
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

        <script src="/web/js/dashboard.js"></script>
    </body>

    </html>
<?php
} else {
    header('Location: index.php');
}
?>