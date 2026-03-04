<?php
include 'db.php';

// --- ตั้งค่าข้อมูล Admin ตรงนี้ ---
$admin_name = "Ming";
$admin_email = "admin@example.com";
$admin_password = "123456789"; // นี่คือรหัสที่คุณจะใช้ Login
$admin_role = "ผู้ดูแล";
// -----------------------------

// เข้ารหัสรหัสผ่านให้เป็นแบบที่ระบบยอมรับ
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO `user` (`Name`, `Email`, `Password`, `Role`) 
        VALUES ('$admin_name', '$admin_email', '$hashed_password', '$admin_role')";

if ($conn->query($sql) === TRUE) {
    echo "สร้างบัญชีผู้ดูแลสำเร็จ!<br>";
    echo "Email: $admin_email <br>";
    echo "Password: $admin_password <br>";
    echo "<a href='login.php'>ไปหน้า Login</a>";
} else {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
}
?>