<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. รับค่าและป้องกัน SQL Injection โดยใช้ $conn_user
    $name = mysqli_real_escape_string($conn_user, $_POST['name']);
    $email = mysqli_real_escape_string($conn_user, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // ป้องกันการแฮ็กส่งค่า "ผู้ดูแล" มาจากหน้าเว็บ
    if ($role == "ผู้ดูแล") {
        $role = "นักศึกษา";
    }

    // 2. แก้ไขจุดนี้: ใช้ $conn_user เพื่อบันทึกลงตาราง user ใน user_db
    $sql = "INSERT INTO `user` (`Name`, `Email`, `Password`, `Role`) 
            VALUES ('$name', '$email', '$password', '$role')";

    if ($conn_user->query($sql) === TRUE) {
        // ส่งกลับหน้า login พร้อมตัวแปร success
        header("Location: login.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn_user->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Register - Orange Pastel</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <form method="POST">
            <h2>สมัครสมาชิก</h2>
            <input type="text" name="name" placeholder="ชื่อ-นามสกุล" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="password" name="password" placeholder="รหัสผ่าน" required>
            <select name="role">
                <option value="นักศึกษา">นักศึกษา</option>
                <option value="อาจารย์">อาจารย์</option>
            </select>
            <button type="submit">ลงทะเบียน</button>
            <div class="switch-link">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></div>
        </form>
    </div>
</body>

</html>