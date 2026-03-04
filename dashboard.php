<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 1. ดึงข้อมูลผู้ใช้จาก user_db (ใช้ $conn_user)
$query = "SELECT * FROM user WHERE ID = '$user_id'";
$result = $conn_user->query($query);
$user_data = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

// 2. ดึงรายวิชาจากตาราง tags ใน DB video (ใช้ $conn_video)
$tag_res = $conn_video->query("SELECT * FROM tags WHERE Tag_Type = 'Subject'");

$display_name = !empty($user_data['Nickname']) ? $user_data['Nickname'] : ($user_data['Name'] ?? 'User');

function getProfileImage($imgData, $class = "profile-pic-small")
{
    if (!empty($imgData)) {
        return '<img src="data:image/jpeg;base64,' . base64_encode($imgData) . '" class="' . $class . '">';
    } else {
        return '<i class="fas fa-user-circle" style="font-size: ' . ($class == "profile-img-large" ? "120px" : "40px") . ';"></i>';
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?php echo $role; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap');

        :root {
            --p-orange: #FFB347;
            --d-orange: #FF8C42;
        }

        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            overflow-x: hidden;
        }

        .navbar {
            background: var(--p-orange);
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-sizing: border-box;
        }

        .user-section {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            min-width: 160px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 1001;
        }

        .user-dropdown.show { display: block; }

        .user-dropdown a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            width: 260px;
            height: 100%;
            background: white;
            transition: 0.3s;
            z-index: 2000;
            padding-top: 80px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.active { left: 0; }

        .sidebar-item {
            display: block;
            padding: 15px 25px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .profile-pic-small {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .profile-img-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--p-orange);
            margin-bottom: 15px;
        }

        .main-container {
            margin-top: 90px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            background: white;
            font-family: 'Kanit';
            color: #333;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-submit {
            background: var(--d-orange);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            flex: 2;
            font-size: 16px;
            font-weight: 500;
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-cancel {
            background: #eee;
            color: #333;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            flex: 1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1500;
        }

        .overlay.active { display: block; }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .video-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 10px;
            text-align: left;
            position: relative;
        }

        /* เมนูจัดการวิดีโอ (ขวาล่าง) */
        .video-options {
            position: absolute;
            bottom: 15px; 
            right: 15px;
            cursor: pointer;
            z-index: 10;
            color: #666;
        }

        .video-actions-dropdown {
            display: none;
            position: absolute;
            bottom: 25px; 
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.15);
            width: 120px;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .video-actions-dropdown.show { display: block; }

        .video-actions-dropdown a {
            display: block;
            padding: 10px 15px;
            font-size: 14px;
            text-decoration: none;
            color: #333;
            text-align: left;
        }

        .video-actions-dropdown a:hover { background: #f5f5f5; }
        .video-actions-dropdown a.delete-text { color: #ff4d4d; border-top: 1px solid #eee; }
    </style>
</head>

<body>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <nav class="navbar">
        <div onclick="toggleSidebar()" style="cursor:pointer; font-size:24px;"><i class="fas fa-bars"></i></div>

        <div class="user-section" id="profileBtn">
            <span><?php echo htmlspecialchars($display_name); ?></span>
            <?php echo getProfileImage($user_data['Profile_Img'] ?? null, "profile-pic-small"); ?>
            <div class="user-dropdown" id="userDropdown">
                <a href="dashboard.php?page=edit"><i class="fas fa-user-cog"></i> Edit Profile</a>
                <a href="logout.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <a href="dashboard.php" class="sidebar-item"><i class="fas fa-home"></i> หน้าหลัก</a>
        <a href="dashboard.php?page=study_video" class="sidebar-item"><i class="fas fa-video"></i> Study Video</a>
    </aside>

    <main class="main-container">
        <?php
        $page = $_GET['page'] ?? 'home';

        if ($page == 'edit'): ?>
            <div class="welcome-card">
                <h2>แก้ไขโปรไฟล์</h2>
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <?php echo getProfileImage($user_data['Profile_Img'] ?? null, "profile-img-large"); ?>
                    <div class="form-group">
                        <label>เปลี่ยนรูปโปรไฟล์</label>
                        <input type="file" name="profile_img" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>ชื่อเล่น</label>
                        <input type="text" name="nickname"
                            value="<?php echo htmlspecialchars($user_data['Nickname'] ?? ''); ?>">
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn-submit">บันทึก</button>
                        <a href="dashboard.php" class="btn-cancel">ยกเลิก</a>
                    </div>
                </form>
            </div>

        <?php elseif ($page == 'add_video'): ?>
            <div class="welcome-card">
                <h2 style="color: var(--d-orange);">เพิ่มวิดีโอการสอน</h2>
                <form id="videoForm" action="save_video.php" method="POST">
                    <div class="form-group">
                        <label>ชื่อคลิปวิดีโอ</label>
                        <input type="text" id="v_title" name="title" required placeholder="ตั้งชื่อวิดีโอ">
                    </div>
                    <div class="form-group">
                        <label>ผู้สอน</label>
                        <input type="text" value="<?php echo htmlspecialchars($user_data['Name'] ?? 'User'); ?>" readonly
                            style="background: #f5f5f5;">
                        <input type="hidden" name="author_name"
                            value="<?php echo htmlspecialchars($user_data['Name'] ?? 'User'); ?>">
                    </div>
                    <div class="form-group">
                        <label>เลือกวิชา</label>
                        <select id="v_subject" name="sub_name" required onchange="updateCode()">
                            <option value="">-- เลือกวิชา --</option>
                            <?php
                            if ($tag_res && $tag_res->num_rows > 0) {
                                while ($t = $tag_res->fetch_assoc()) {
                                    echo "<option value='{$t['Tag_Value']}' data-code='" . ($t['Subject_Code'] ?? '') . "'>{$t['Tag_Value']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>รหัสวิชา</label>
                        <input type="text" id="v_code" name="sub_code" readonly style="background:#f9f9f9;"
                            placeholder="รหัสวิชาอัตโนมัติ">
                    </div>
                    <div class="form-group">
                        <label>URL วิดีโอ</label>
                        <input type="url" id="v_url" name="url" required placeholder="ลิงก์วิดีโอ">
                    </div>
                    <div class="btn-group">
                        <button type="submit" id="v_btn" class="btn-submit" disabled>อัปโหลด</button>
                        <a href="dashboard.php?page=study_video" class="btn-cancel">ยกเลิก</a>
                    </div>
                </form>
            </div>
            <script>
                function updateCode() {
                    const sel = document.getElementById('v_subject');
                    const code = sel.options[sel.selectedIndex].getAttribute('data-code');
                    document.getElementById('v_code').value = code || "";
                    check();
                }
                function check() {
                    const t = document.getElementById('v_title').value;
                    const s = document.getElementById('v_subject').value;
                    const u = document.getElementById('v_url').value;
                    const btn = document.getElementById('v_btn');
                    if (t && s && u) {
                        btn.disabled = false;
                        btn.style.background = "#FF8C42";
                    } else {
                        btn.disabled = true;
                        btn.style.background = "#ccc";
                    }
                }
                document.getElementById('v_title').oninput = check;
                document.getElementById('v_url').oninput = check;
            </script>

        <?php elseif ($page == 'edit_video'): 
            $id = intval($_GET['id'] ?? 0);
            $v_edit = $conn_video->query("SELECT * FROM videos WHERE Video_ID = $id")->fetch_assoc();
            $subjects = $conn_video->query("SELECT * FROM tags WHERE Tag_Type = 'Subject'");
            if (!$v_edit) { header("Location: dashboard.php?page=study_video"); exit(); }
        ?>
            <div class="welcome-card">
                <h2 style="color: var(--d-orange);">แก้ไขวิดีโอการสอน</h2>
                <form action="update_video.php" method="POST">
                    <input type="hidden" name="video_id" value="<?php echo $v_edit['Video_ID']; ?>">
                    <div class="form-group">
                        <label>ชื่อคลิปวิดีโอ</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($v_edit['Title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>เลือกวิชาใหม่</label>
                        <select name="sub_name" required>
                            <?php while($s = $subjects->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($s['Tag_Value']); ?>" 
                                    <?php echo ($v_edit['Subject_Name'] == $s['Tag_Value']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['Tag_Value']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>URL วิดีโอ</label>
                        <input type="url" name="url" value="<?php echo htmlspecialchars($v_edit['Video_URL']); ?>" required>
                    </div>
                    <div class="btn-group">
                        <button type="submit" class="btn-submit">บันทึกการแก้ไข</button>
                        <a href="dashboard.php?page=study_video" class="btn-cancel">ยกเลิก</a>
                    </div>
                </form>
            </div>

        <?php elseif ($page == 'play_video'): 
    $id = intval($_GET['id'] ?? 0);
    $v_play = $conn_video->query("SELECT * FROM videos WHERE Video_ID = $id")->fetch_assoc();
    $url = $v_play['Video_URL'];

    // ตรวจสอบว่าเป็นลิงก์ Google Drive หรือไม่
    if(strpos($url, 'drive.google.com') !== false) {
        // เปลี่ยน /view หรือพารามิเตอร์อื่นๆ ให้เป็น /preview เพื่อให้เล่นใน iframe ได้
        if (strpos($url, '/view') !== false) {
            $url = str_replace('/view', '/preview', $url);
        } elseif (strpos($url, '/edit') !== false) {
             $url = str_replace('/edit', '/preview', $url);
        }
        
        // ตัด Query String ออก (?usp=sharing ฯลฯ) เพื่อความถูกต้อง
        $url = explode('?', $url)[0];
        if (substr($url, -8) !== '/preview') {
            $url .= '/preview';
        }
    } 
    // ส่วนจัดการ YouTube (Embed)
    elseif(strpos($url, 'v=') !== false) { 
        $vid = explode('v=', $url)[1]; 
        $url = "https://www.youtube.com/embed/".explode('&', $vid)[0]; 
    }
?>
    <div class="welcome-card" style="max-width: 900px; text-align: left;">
        <a href="dashboard.php?page=study_video" style="text-decoration:none; color:#666;"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
        <h2 style="color:var(--d-orange); margin: 15px 0 5px;"><?php echo htmlspecialchars($v_play['Title']); ?></h2>
        
        <div style="position:relative; padding-bottom:56.25%; height:0; border-radius:15px; overflow:hidden; background:#000;">
            <iframe src="<?php echo $url; ?>" style="position:absolute; top:0; left:0; width:100%; height:100%;" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

        <?php elseif ($page == 'study_video'): ?>
            <div class="welcome-card" style="max-width: 900px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h2 style="margin:0; color:var(--d-orange);">Study Video</h2>
                    <?php if ($role != 'นักศึกษา'): ?>
                        <a href="dashboard.php?page=add_video"
                            style="background:var(--d-orange); color:white; padding:10px 15px; border-radius:8px; text-decoration:none;">+
                            เพิ่มวิดีโอ</a>
                    <?php endif; ?>
                </div>
                <div class="video-grid">
                    <?php
                    // ดึงวิดีโอจาก DB video (ใช้ $conn_video)
                    $v_res = $conn_video->query("SELECT * FROM videos ORDER BY Created_At DESC");
                    if ($v_res && $v_res->num_rows > 0) {
                        while ($v = $v_res->fetch_assoc()) { ?>
                            <div class='video-card'>
                                <?php if ($role != 'นักศึกษา'): ?>
                                    <div class="video-options" onclick="toggleVideoMenu(event, 'v-menu-<?php echo $v['Video_ID']; ?>')">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <div id="v-menu-<?php echo $v['Video_ID']; ?>" class="video-actions-dropdown">
                                            <a href="dashboard.php?page=edit_video&id=<?php echo $v['Video_ID']; ?>"><i class="fas fa-edit"></i> แก้ไข</a>
                                            <a href="delete_video.php?id=<?php echo $v['Video_ID']; ?>" class="delete-text" onclick="return confirm('ยืนยันการลบวิดีโอนี้?')"><i class="fas fa-trash-alt"></i> ลบ</a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div style='background:#eee; height:80px; border-radius:5px; margin-bottom:10px;'></div>
                                <strong><?php echo htmlspecialchars($v['Subject_Name']); ?></strong><br>
                                <small>โดย <?php echo htmlspecialchars($v['Author_Name']); ?></small>
                                <p style='font-size:14px; margin:5px 0;'><?php echo htmlspecialchars($v['Title']); ?></p>
                                <a href="dashboard.php?page=play_video&id=<?php echo $v['Video_ID']; ?>" style='display:inline-block; background:var(--d-orange); color:white; padding:8px 12px; border-radius:5px; text-decoration:none; font-size:13px; margin-top:5px;'>เข้าดูวิดีโอ</a>
                            </div>
                        <?php }
                    } else {
                        echo "<p style='grid-column: 1/-1;'>ยังไม่มีวิดีโอในระบบ</p>";
                    }
                    ?>
                </div>
            </div>

        <?php else: ?>
            <div class="welcome-card">
                <?php echo getProfileImage($user_data['Profile_Img'] ?? null, "profile-img-large"); ?>
                <h1>Welcome, <?php echo htmlspecialchars($display_name); ?></h1>
                <p>ระบบจัดการเรียนการสอนออนไลน์พาสเทลส้ม</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        }

        const profileBtn = document.getElementById('profileBtn');
        const userDropdown = document.getElementById('userDropdown');

        if (profileBtn) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });
        }

        // ฟังก์ชันเปิด/ปิดเมนูสามจุด
        function toggleVideoMenu(event, menuId) {
            event.stopPropagation();
            document.querySelectorAll('.video-actions-dropdown').forEach(m => {
                if(m.id !== menuId) m.classList.remove('show');
            });
            document.getElementById(menuId).classList.toggle('show');
        }

        window.onclick = function (e) {
            if (profileBtn && !profileBtn.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
            document.querySelectorAll('.video-actions-dropdown').forEach(m => m.classList.remove('show'));
        }
    </script>
</body>

</html>