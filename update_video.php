<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['role'] != 'นักศึกษา') {
    $id = intval($_POST['video_id']);
    $title = mysqli_real_escape_string($conn_video, $_POST['title']);
    $sub_name = mysqli_real_escape_string($conn_video, $_POST['sub_name']); // รับค่าจาก Dropdown
    $url = mysqli_real_escape_string($conn_video, $_POST['url']);

    // อัปเดตข้อมูลใหม่ลงในตาราง videos
    $sql = "UPDATE videos SET 
            Title = '$title', 
            Subject_Name = '$sub_name', 
            Video_URL = '$url' 
            WHERE Video_ID = $id";
            
    if ($conn_video->query($sql) === TRUE) {
        header("Location: dashboard.php?page=study_video");
        exit();
    } else {
        echo "Error: " . $conn_video->error;
    }
}
?>