<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $sub_code = mysqli_real_escape_string($conn, $_POST['sub_code']);
    $sub_name = mysqli_real_escape_string($conn, $_POST['sub_name']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);

    // รับชื่อจริงที่ส่งมาจากฟอร์ม
    $author = mysqli_real_escape_string($conn, $_POST['author_name']);

    // บันทึกลงตาราง videos
    $sql = "INSERT INTO videos (Title, Author_Name, Subject_Code, Subject_Name, Video_URL) 
            VALUES ('$title', '$author', '$sub_code', '$sub_name', '$url')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php?page=study_video");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>