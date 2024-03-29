<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Web Vay, Cầm Cố</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="/web/css/style.css">
</head>

<body>
    <section id="header">
        <a href="#"><img src="/web/img/logo.png" class="logo" alt=""></a>

        <div>
            <ul id="navbar">
                <li><a class="active" href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                <?php
                if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
                    echo '<li><a href="/views/user/register_user.php">Đăng ký khách hàng</a></li>';
                    echo '<li><a href="/views/user/register_pawn_info.php">Đăng ký cầm đồ</a></li>';
                    echo '<li><a href="/views/user/dashboard.php">Thống kê</a></li>';
                }
                ?>
                <li><a href="/views/user/search.php">Tìm kiếm</a></li>
                <li><a href="about.html">Về chúng tôi</a></li>
                <li><a href="contact.php">Liên hệ</a></li>
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

    <div class="slider">
        <div class="slides">
            <input type="radio" name="radio-btn" id="radio1">
            <input type="radio" name="radio-btn" id="radio2">
            <input type="radio" name="radio-btn" id="radio3">
            <input type="radio" name="radio-btn" id="radio4">

            <div class="slide first">
                <img src="/web/img/images.jpg">
            </div>
            <div class="slide">
                <img src="/web/img/image_1.jpg">
            </div>
            <div class="slide">
                <img src="/web/img/image_2.jpg">
            </div>
            <div class="slide">
                <img src="/web/img/image_3.jpg">
            </div>

            <div class="navigation-auto">
                <div class="auto-btn1"></div>
                <div class="auto-btn2"></div>
                <div class="auto-btn3"></div>
                <div class="auto-btn4"></div>
            </div>
        </div>

        <div class="navigation-manual">
            <label for="radio1" class="manual-btn"></label>
            <label for="radio2" class="manual-btn"></label>
            <label for="radio3" class="manual-btn"></label>
            <label for="radio4" class="manual-btn"></label>
        </div>
    </div>

    <section class="introduce_index">
        <div class="container">
            <h4>WebPawn - Giải pháp tài chính đáng tin cậy</h4>
            <p class="section-description">Trải nghiệm dịch vụ cầm cố hoàn toàn mới với hệ thống phòng giao dịch trên toàn quốc. </p>
            <div class="content">
                <div class="col">
                    <img width="80" height="75" style="height: 75px" src="/web/img/image_3.jpg">
                    <span> Phòng giao dịch toàn quốc</span>
                </div>
                <div class="col">
                    <img width="80" height="75" style="height: 75px" src="/web/img/image_3.jpg">
                    <span>15 phút là có tiền</span>
                </div>
                <div class="col">
                    <img width="80" height="75" style="height: 75px" src="/web/img/image_3.jpg">
                    <span>Hợp đồng minh bạch</span>
                </div>
                <div class="col">
                    <img width="80" height="75" style="height: 75px" src="/web/img/image_3.jpg">
                    <span>Bảo mật thông tin</span>
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

    <script type="text/javascript">
        var counter = 1;
        setInterval(function() {
            document.getElementById('radio' + counter).checked = true;
            counter++;
            if (counter > 4) {
                counter = 1;
            }
        }, 4000);
    </script>

    <script src="/web/js/script.js"></script>
</body>

</html>