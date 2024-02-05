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

            $sqlUser = "SELECT * FROM users WHERE id = '$userId'";
            $resultUser = $conn->query($sqlUser);

            if ($resultUser->num_rows > 0) {
                $rowUser = $resultUser->fetch_assoc();
                $fullName = $rowUser['fullname'];
                $phone = $rowUser['phone'];
                $address = $rowUser['address'];
                $userType = $rowUser['type'];
            }

            $interestRateID = $row['interest_rate_id'];
            $currentImage = $row['image'];
            $price = (float) $row['price'];
            $startDate = date("d-m-Y", strtotime($row['start_date']));
            $endDate = date("d-m-Y", strtotime($row['end_date']));
            $warehouseID = $row['warehouse_id'];
        }
    }
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

// Fetch 'stores' data for select options
$warehouseOptions = array();
$sql = "SELECT * FROM stores";
$resultStore = $conn->query($sql);
if ($resultStore->num_rows > 0) {
    while ($row = $resultStore->fetch_assoc()) {
        $warehouseOptions[] = $row;
    }
}

// Register pawn info
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $price = $_POST['price'];

    $start_date_str = $_POST['start_date'];
    $end_date_str = $_POST['end_date'];
    $formatted_start_date = DateTime::createFromFormat('d-m-Y', $start_date_str);
    $formatted_end_date = DateTime::createFromFormat('d-m-Y', $end_date_str);
    $start_date = $formatted_start_date->format('Y-m-d');
    $end_date = $formatted_end_date->format('Y-m-d');

    $warehouse = $_POST['warehouse'];

    if (!empty($_FILES["image"]["tmp_name"])) {
        $tmp_name = $_FILES["image"]["tmp_name"];
        $data = file_get_contents($tmp_name);
        $image = $conn->real_escape_string($data);
        $query = "UPDATE pawn_info SET image = '$image', price = '$price', start_date = '$start_date', end_date = '$end_date', warehouse_id = '$warehouse' WHERE id = '$id'";
    } else {
        $query = "UPDATE pawn_info SET price = '$price', start_date = '$start_date', end_date = '$end_date', warehouse_id = '$warehouse' WHERE id = '$id'";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: /views/user/search.php");
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
                        echo '<li><a class="active" href="update_pawn_info.php">Cập nhật thông tin cầm đồ</a></li>';
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
                    <h2>Cập nhật thông tin cầm đồ</h2>
                    <div class="input_box">
                        <input type="text" id="input_user_id" name="id" placeholder="Nhập CMND" oninput="checkUser()" value="<?php echo $userId ?>" required readonly>
                        <i class="fa-solid fa-id-card input_user_id"></i>
                        <div id="error_message" style="color: red;"></div>
                    </div>

                    <div class="input_box">
                        <input type="text" id="fullname" name="fullname" placeholder="Nhập họ và tên" value="<?php echo $fullName ?>" required readonly>
                        <i class="fa-solid fa-user fullname"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="phone" name="phone" placeholder="Nhập số điện thoại" value="<?php echo $phone ?>" required readonly>
                        <i class="fa-solid fa-phone phone"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="address" name="address" placeholder="Nhập địa chỉ" value="<?php echo $address ?>" required readonly>
                        <i class="fa-solid fa-location-dot address"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="user_type" name="user_type" placeholder="Loại khách hàng" value="<?php echo $userType ?>" required readonly>
                        <i class="fa-solid fa-user user_type"></i>
                    </div>

                    <div class="select_box">
                        <select name="type" id="type" onchange="updateFields()" required>
                            <option value="" selected hidden>Chọn loại hàng hóa</option>
                            <?php
                            foreach ($typeOptions as $option) {
                                $selected = ($option['id'] == $interestRateID) ? 'selected' : '';
                                echo "<option value=\"{$option['id']}\" data-price=\"{$option['price']}\" data-time=\"{$option['time']}\" data-type=\"{$option['type']}\" $selected>{$option['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="upload_image">
                        <label for="image">Tải hình ảnh lên</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <?php if (!empty($currentImage)) : ?>
                            <img src="display_image.php?id=<?php echo $pawnInfoID; ?>" alt="Image">
                        <?php endif; ?>
                    </div>

                    <div class="input_box">
                        <input type="text" id="formatPrice" name="formatPrice" placeholder="Giá" value="<?php echo $price ?>" required>
                        <input type="hidden" id="price" name="price" value="<?php echo $price ?>">
                        <i class="fa-solid fa-money-check-dollar price"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="interest_rate" name="interest_rate" placeholder="Thuế cầm" required>
                        <i class="fa-solid fa-money-check-dollar interest_rate"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="time" name="time" placeholder="Thời gian cầm" required readonly>
                        <i class="fa-regular fa-calendar-days time"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="start_date" name="start_date" placeholder="Ngày bắt đầu" value="<?php echo $startDate ?>" required>
                        <i class="fa-regular fa-calendar-days time start_date"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="end_date" name="end_date" placeholder="Ngày kết thúc" value="<?php echo $endDate ?>" required>
                        <i class="fa-regular fa-calendar-days time end_date"></i>
                    </div>

                    <div class="select_box">
                        <select name="warehouse" id="warehouse" required>
                            <option value="" selected hidden>Chọn kho hàng lưu trữ</option>
                            <?php
                            foreach ($warehouseOptions as $option) {
                                $selected = ($option['id'] == $warehouseID) ? 'selected' : '';
                                echo "<option value=\"{$option['id']}\" $selected>{$option['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="button" name="submit">Cập nhật</button>
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

        <script>
            function checkUser() {
                var userIdValue = $('#input_user_id').val();

                $.ajax({
                    type: 'POST',
                    url: 'check_user.php',
                    data: {
                        id: userIdValue
                    },
                    success: function(response) {
                        var data = JSON.parse(response);

                        if (data.error) {
                            $('#error_message').html(data.error);
                            $('#fullname').val(null);
                            $('#phone').val(null);
                            $('#address').val(null);
                            $('#user_type').val(null);
                        } else {
                            $('#fullname').val(data.fullname);
                            $('#phone').val(data.phone);
                            $('#address').val(data.address);
                            $('#user_type').val(data.user_type);
                            $('#error_message').html('');
                        }
                    }
                })
            }
        </script>

        <script src="/web/js/script.js"></script>
        <script src="/web/js/re_pawn_info_script.js"></script>
    </body>

    </html>
<?php
} else {
    header('Location: index.php');
}
?>