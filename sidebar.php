<!-- sidebar.php -->
<div class="sidebar" id="sidebar">
    <h2>เมนูหลัก</h2>
    <ul>
        <li><a href="user_dashboard.php">หน้าแรก</a></li>
        <li><a href="user_product.php">สินค้า</a></li>
        <li><a href="user_article.php">สถานที่ท่องเที่ยว</a></li>
        <li><a href="contact.php">เกี่ยวกับเรา</a></li>
    </ul>
    <a href="logout.php"><button class="logout-btn">Logout</button></a>
</div>
<style>
            /* Sidebar CSS */
            .sidebar {
            position: fixed;
            left: -250px; /* ซ่อน Sidebar ที่ตำแหน่งเริ่มต้น */
            top: 0;
            height: 100%;
            width: 250px;
            background-color: #004d40;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
            z-index: 100;
        }

        .sidebar.active {
            left: 0; /* แสดง Sidebar เมื่อมี class active */
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            background-color: #00796b;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #005f56;
        }

        .content {
            padding: 20px;
            transition: margin-left 0.3s ease;
            margin-left: 0; /* ระยะห่างจากด้านซ้าย */
        }

        .content.shifted {
            margin-left: 250px; /* ขยับเนื้อหาเมื่อ Sidebar ถูกเปิด */
        }

        .toggle-btn {
            position: fixed;
            left: 10px;
            top: 10px;
            background-color: #004d40;
            color: white;
            padding: 8px;
            cursor: pointer;
            border: none;
            z-index: 101;
        }

        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }
</style>








    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>เมนูหลัก</h2>
        <ul>
            <li><a href="admin.php">หน้าแรก</a></li>
            <li><a href="product.php">สินค้า</a></li>
            <li><a href="article.php">สถานที่ท่องเที่ยว</a></li>
            <li><a href="layout.php">เกี่ยวกับเรา</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
        <a href="logout.php"><button class="btn logout">Logout</button></a>
    </div>

    <!-- ปุ่มเปิด-ปิด Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <style>
    /* Sidebar CSS */
        .sidebar {
            position: fixed;
            left: -250px; /* ซ่อน Sidebar ที่ตำแหน่งเริ่มต้น */
            top: 0;
            height: 100%;
            width: 250px;
            background-color: #004d40;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
            z-index: 100;
        }

        .sidebar.active {
            left: 0; /* แสดง Sidebar เมื่อมี class active */
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            background-color: #00796b;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #005f56;
        }

        .content {
            padding: 20px;
            transition: margin-left 0.3s ease;
            margin-left: 0; /* ระยะห่างจากด้านซ้าย */
        }

        .content.shifted {
            margin-left: 250px; /* ขยับเนื้อหาเมื่อ Sidebar ถูกเปิด */
        }

        .toggle-btn {
            position: fixed;
            left: 10px;
            top: 10px;
            background-color: #004d40;
            color: white;
            padding: 8px;
            cursor: pointer;
            border: none;
            z-index: 101;
        }

        .logout-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }
        </style>