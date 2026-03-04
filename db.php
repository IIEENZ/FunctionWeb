<?php
$host = "localhost";
$user = "root";
$pass = "";

// 1. เชื่อมต่อฐานข้อมูลสำหรับข้อมูลผู้ใช้
$dbname_user = "user_db";
$conn_user = new mysqli($host, $user, $pass, $dbname_user);

// 2. เชื่อมต่อฐานข้อมูลสำหรับระบบวิดีโอ
$dbname_video = "video";
$conn_video = new mysqli($host, $user, $pass, $dbname_video);

// 3. เชื่อมต่อฐานข้อมูลสำหรับระบบ Chat Board (เพิ่มใหม่)
$dbname_chat = "chat_board";
$conn_chat = new mysqli($host, $user, $pass, $dbname_chat);

// ตรวจสอบการเชื่อมต่อทั้ง 3 ฐานข้อมูล
if ($conn_user->connect_error || $conn_video->connect_error || $conn_chat->connect_error) {
    die("Connection failed: " . ($conn_user->connect_error ?: ($conn_video->connect_error ?: $conn_chat->connect_error)));
}

// ตั้งค่าภาษาไทยให้ทุกการเชื่อมต่อ
$conn_user->set_charset("utf8mb4");
$conn_video->set_charset("utf8mb4");
$conn_chat->set_charset("utf8mb4");
?>