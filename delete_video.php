<?php
session_start();
include 'db.php'; // ต้องมั่นใจว่าไฟล์นี้มี $conn_video

if ($_SESSION['role'] != 'นักศึกษา' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM videos WHERE Video_ID = $id";
    
    if ($conn_video->query($sql) === TRUE) {
        header("Location: dashboard.php?page=study_video");
    } else {
        echo "Error deleting: " . $conn_video->error;
    }
} else {
    header("Location: dashboard.php");
}
?>