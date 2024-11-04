<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บไซต์ข่าวสาร</title>
    <style>
        /* CSS พื้นฐาน */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .nav {
            background-color: #555;
            overflow: hidden;
        }
        .nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .nav a:hover {
            background-color: #111;
        }
        .main-content {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
        }
        .news-item {
            flex: 1 1 300px;
            border: 1px solid #ddd;
            margin: 10px;
            padding: 15px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .news-item h2 {
            font-size: 18px;
        }
        .news-item p {
            font-size: 14px;
            color: #555;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>เว็บไซต์ข่าวสาร</h1>
        <p>ข่าวสารอัพเดทล่าสุดที่คุณไม่ควรพลาด!</p>
    </div>

    <div class="nav">
        <a href="#home">หน้าหลัก</a>
        <a href="#world">ข่าวโลก</a>
        <a href="#sports">ข่าวกีฬา</a>
        <a href="#entertainment">ข่าวบันเทิง</a>
        <a href="#technology">ข่าวเทคโนโลยี</a>
    </div>

    <div class="main-content">
        <!-- ตัวอย่างข่าวสาร -->
        <div class="news-item">
            <h2>หัวข้อข่าวที่ 1</h2>
            <p>รายละเอียดของข่าวที่ 1 เพื่อให้ผู้เข้าชมได้รับข้อมูลที่ครบถ้วน.</p>
        </div>
        <div class="news-item">
            <h2>หัวข้อข่าวที่ 2</h2>
            <p>รายละเอียดของข่าวที่ 2 เป็นข้อมูลสำคัญที่ควรรู้.</p>
        </div>
        <div class="news-item">
            <h2>หัวข้อข่าวที่ 3</h2>
            <p>รายละเอียดของข่าวที่ 3 เพื่อให้ความรู้และการวิเคราะห์เกี่ยวกับเหตุการณ์.</p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 เว็บไซต์ข่าวสาร - อัพเดทข่าวที่เชื่อถือได้</p>
    </div>

</body>
</html>
