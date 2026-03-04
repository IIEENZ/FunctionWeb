<?php

session_start();

include 'db.php';

if ($_SESSION['role'] == 'นักศึกษา') {
    header("Location: dashboard.php");
    exit();
}



// ดึงรายวิชาจากตาราง tags มาทำ Dropdown

// ดึงวิชาจากตาราง tags ใน video
$tags = $conn_video->query("SELECT * FROM tags WHERE Tag_Type='Subject'");

// บันทึกวิดีโอลงตาราง videos ใน video
$sql = "INSERT INTO videos (Title, Author_Name, ...) VALUES (...)";
$conn_video->query($sql);

?>



<!DOCTYPE html>

<html lang="th">

<head>

    <meta charset="UTF-8">

    <title>เพิ่มคลิปวิดีโอ</title>

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: #FFF5E1;
            display: flex;
            justify-content: center;
            padding: 50px;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            color: #FF8C42;
            text-align: center;
        }

        .input-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .btn-submit {
            background: #FF8C42;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>

</head>

<body>



    <div class="form-card">

        <h2>เพิ่มวิดีโอการสอน</h2>

        <form action="save_video.php" method="POST">

            <div class="input-group">

                <label>ชื่อคลิปวิดีโอ</label>

                <input type="text" name="title" required>

            </div>

            <div class="input-group">

                <label>รหัสวิชา</label>

                <input type="text" name="sub_code" placeholder="เช่น EN0101" required>

            </div>

            <div class="input-group">

                <label>เลือกวิชา</label>

                <select name="sub_name" required>

                    <?php while ($t = $tag_res->fetch_assoc()): ?>

                        <option value="<?php echo $t['Tag_Value']; ?>"><?php echo $t['Tag_Value']; ?></option>

                    <?php endwhile; ?>

                </select>

            </div>

            <div class="input-group">

                <label>URL วิดีโอ (YouTube / Drive ลิงก์)</label>

                <input type="url" name="url" required>

            </div>

            <button type="submit" class="btn-submit">อัปโหลดวิดีโอ</button>

            <a href="study_video.php"
                style="display:block; text-align:center; margin-top:15px; color:#999; text-decoration:none;">ยกเลิก</a>

        </form>

    </div>



</body>

</html>