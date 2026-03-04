<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $topic_id = intval($_POST['topic_id']);
    $message = mysqli_real_escape_string($conn_chat, $_POST['message']);
    $user_id = $_SESSION['user_id'];

    if (!empty($message)) {
        $sql = "INSERT INTO chat_comments (Topic_ID, User_ID, Message) VALUES ($topic_id, $user_id, '$message')";
        $conn_chat->query($sql);
    }
}
header("Location: dashboard.php?page=chat_board");
exit();
?>