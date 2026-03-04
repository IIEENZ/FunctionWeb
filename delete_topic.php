<?php
session_start();
include 'db.php';

// ตรวจสอบว่าส่ง ID มาและล็อกอินอยู่หรือไม่
if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $tid = intval($_GET['id']);
    $uid = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // เช็คสิทธิ์ก่อนลบ: ต้องเป็นเจ้าของกระทู้หรือแอดมิน
    $stmt = $conn_chat->prepare("SELECT User_ID FROM chat_topics WHERE Topic_ID = ?");
    $stmt->bind_param("i", $tid);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && ($result['User_ID'] == $uid || $role == 'ผู้ดูแล' || $role == 'Admin')) {
        // 1. ลบคอมเมนต์ที่เกี่ยวข้องทั้งหมดก่อน เพื่อไม่ให้ข้อมูลค้าง (Foreign Key)
        $del_comm = $conn_chat->prepare("DELETE FROM chat_comments WHERE Topic_ID = ?");
        $del_comm->bind_param("i", $tid);
        $del_comm->execute();

        // 2. ลบหัวข้อกระทู้
        $del_topic = $conn_chat->prepare("DELETE FROM chat_topics WHERE Topic_ID = ?");
        $del_topic->bind_param("i", $tid);
        $del_topic->execute();
    }
}

// ลบเสร็จส่งกลับหน้าเดิม
header("Location: dashboard.php?page=chat_board");
exit();
?>