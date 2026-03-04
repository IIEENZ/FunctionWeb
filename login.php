<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. รับค่าจากฟอร์ม
    $email = mysqli_real_escape_string($conn_user, $_POST['email']);
    $password = $_POST['password'];

    // 2. แก้ไขจุดนี้: ใช้ $conn_user เพื่อดึงข้อมูลจาก user_db
    // และเปลี่ยนเงื่อนไข WHERE ให้ค้นหาจากคอลัมน์ Email (ตามค่าที่รับมา)
    $query = "SELECT * FROM user WHERE Email = '$email'";
    $result = $conn_user->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // 3. ตรวจสอบรหัสผ่าน (ตรวจสอบชื่อคอลัมน์ Password ใน DB ของคุณด้วย)
        if (password_verify($password, $user['Password'])) {
            // บันทึกค่าลง Session เพื่อใช้ใน Dashboard
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['user_name'] = $user['Name'];
            $_SESSION['role'] = $user['Role'];

            header("Location: dashboard.php");
            exit();
        } else {
            // รหัสผ่านผิด
            header("Location: login.php?error=1");
            exit();
        }
    } else {
        // ไม่พบอีเมลในระบบ
        header("Location: login.php?error=2");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Login - Orange Pastel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alert-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            z-index: 2000;
            transition: transform 0.5s ease-in-out;
            box-sizing: border-box;
        }
        .alert-success { background-color: #d4edda; color: #155724; border-bottom: 2px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border-bottom: 2px solid #f5c6cb; }
    </style>
</head>
<body>

    <?php
    $msg = "";
    $type = "";

    if (isset($_GET['success'])) {
        $msg = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
        $type = "alert-success";
    } elseif (isset($_GET['error'])) {
        $type = "alert-danger";
        if ($_GET['error'] == 1) $msg = "รหัสผ่านไม่ถูกต้อง ลองใหม่อีกครั้ง";
        if ($_GET['error'] == 2) $msg = "ไม่พบอีเมลนี้ในระบบ";
    }
    ?>

    <?php if ($msg != ""): ?>
        <div id="alert-box" class="alert-bar <?php echo $type; ?>">
            <?php echo $msg; ?>
        </div>
        <script>
            setTimeout(function () {
                var el = document.getElementById('alert-box');
                if(el) el.style.transform = 'translateY(-100%)';
            }, 5000);
        </script>
    <?php endif; ?>

    <div class="container" style="margin-top: 100px;">
        <form method="POST">
            <h2>เข้าสู่ระบบ</h2>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <button type="submit">ตกลง</button>
            <div class="switch-link">
                ยังไม่มีบัญชี? <a href="register.php">สมัครที่นี่</a>
            </div>
        </form>
    </div>

</body>
</html>