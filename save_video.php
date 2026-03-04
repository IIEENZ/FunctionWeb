<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ใช้ $conn_video ในการ Escape String และ Query
    $title = mysqli_real_escape_string($conn_video, $_POST['title']);
    $sub_code = mysqli_real_escape_string($conn_video, $_POST['sub_code']);
    $sub_name = mysqli_real_escape_string($conn_video, $_POST['sub_name']);
    $url = mysqli_real_escape_string($conn_video, $_POST['url']);
    $author = mysqli_real_escape_string($conn_video, $_POST['author_name']);

    $sql = "INSERT INTO videos (Title, Author_Name, Subject_Code, Subject_Name, Video_URL) 
            VALUES ('$title', '$author', '$sub_code', '$sub_name', '$url')";

    // สั่งบันทึกเข้า DB video
    if ($conn_video->query($sql) === TRUE) {
        header("Location: dashboard.php?page=study_video");
        exit();
    } else {
        echo "Error: " . $conn_video->error;
    }
}
?>