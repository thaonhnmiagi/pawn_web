<?php
session_start();

if (isset($_POST["submit"])) {
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
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $type = $_POST['type'];
    $password = $_POST['password'];
    // $confirm_password = $_POST['confirm_password'];

    // var_dump($id, $fullname, $phone, $address, $type, $password, $confirm_password);
    var_dump($id, $fullname, $phone, $address, $type, $password);

    // $query = "INSERT INTO users VALUES (,$id,'$fullname','$phone','$address','$type','$password','$confirm_password')";
    $query = "INSERT INTO users VALUES ($id,'$fullname','$phone','$address','$type','$password')";

    if (mysqli_query($conn, $query)) {
        echo "Record inserted successfully";
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
        <link rel="stylesheet" href="/web/css/register_customer.css">
    </head>

    <body>
        <section id="header">
            <a href="#"><img src="/web/img/logo.png" class="logo" alt=""></a>

            <div>
                <ul id="navbar">
                    <li><a href="/views/home/index.php"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                    <?php
                    if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
                        echo '<li><a class="active" href="register_customer.php">Đăng ký khách hàng</a></li>';
                        echo '<li><a href="register_pawn_info.php">Đăng ký cầm đồ</a></li>';
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
            <div class="register_form">
                <form action="" method="post" enctype="multipart/form-data">
                    <h2>Đăng ký khách hàng</h2>
                    <div class="input_box">
                        <input type="text" id="input_user_id" name="id" placeholder="Nhập CMND" required>
                        <i class="fa-solid fa-id-card input_user_id"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="fullname" name="fullname" placeholder="Nhập họ và tên" required>
                        <i class="fa-solid fa-user fullname"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
                        <i class="fa-solid fa-phone phone"></i>
                    </div>

                    <div class="input_box">
                        <input type="text" id="address" name="address" placeholder="Nhập địa chỉ" required>
                        <i class="fa-solid fa-location-dot address"></i>
                    </div>

                    <div class="select_box">
                        <select name="type" id="type" required>
                            <option value="" selected hidden>Chọn loại khách hàng</option>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>

                    <div class="input_box">
                        <input type="password" id="input_password" name="password" placeholder="Nhập mật khẩu" required>
                        <i class="fa-solid fa-lock password"></i>
                        <i class="fa-solid fa-eye-slash password_hide"></i>
                    </div>

                    <!-- <div class="input_box">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                        <i class="fa-solid fa-lock password"></i>
                        <i class="fa-solid fa-eye-slash password_hide"></i>
                    </div> -->

                    <button type="submit" class="button" name="submit">Đăng ký</button>
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