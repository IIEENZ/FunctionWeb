<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn_chat, $_POST['title']);
    $desc = mysqli_real_escape_string($conn_chat, $_POST['description']);
    $duration = $_POST['expire_duration']; // รับค่าเช่น "1 month" หรือ "7 days"
    $user_id = $_SESSION['user_id'];

    // คำนวณวันหมดอายุจากเวลาปัจจุบัน
    $expire_date = date('Y-m-d H:i:s', strtotime("+" . $duration));

    $sql = "INSERT INTO chat_topics (Title, Description, User_ID, Expire_At) 
            VALUES ('$title', '$desc', '$user_id', '$expire_date')";

    if ($conn_chat->query($sql)) {
        header("Location: dashboard.php?page=chat_board");
    }
}
?>