<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);

    // อัปเดตตาราง user และคอลัมน์ ID ตัวใหญ่ ตามรูป image_89919f.jpg
    if (!empty($_FILES['profile_img']['tmp_name'])) {
        $imgData = addslashes(file_get_contents($_FILES['profile_img']['tmp_name']));
        $conn->query("UPDATE user SET Profile_Img = '{$imgData}' WHERE ID = '$user_id'");
    }

    $conn->query("UPDATE user SET Nickname = '$nickname' WHERE ID = '$user_id'");

    header("Location: dashboard.php");
    exit();
}
?>