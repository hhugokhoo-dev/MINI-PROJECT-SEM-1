<?php
session_start();
// 可以在这里引入 db.php 如果首页需要动态展示车辆，目前暂时不需要
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRIVORA - Car Rental</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS (确保路径正确) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ================= NAVBAR ================= -->
    <header class="navbar">
        <div class="logo">
            DRIV<span>O</span>RA
        </div>
        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="/customer/browse.php">Cars</a>
            <a href="#about">About Us</a>
            <a href="#contact">Contact</a>
        </nav>
        
        <!-- 动态判断登录状态 -->
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- 如果已登录，根据角色跳去不同的后台 -->
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="/admin/users.php" class="nav-btn" style="background: #555;">Admin Panel</a>
                <?php elseif($_SESSION['role'] === 'staff'): ?>
                    <a href="/staff/vehicles.php" class="nav-btn" style="background: #555;">Staff Panel</a>
                <?php else: ?>
                    <a href="/customer/my-rentals.php" class="nav-btn" style="background: #555;">My Rentals</a>
                <?php endif; ?>
                
                <a href="/system/logout.php" class="nav-btn">Logout</a>
            <?php else: ?>
                <!-- 如果未登录 -->
                <a href="/system/login.php" class="nav-btn">Login / Book Now</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- ================= HERO ================= -->
    <section class="hero">
        <div class="hero-content">
            <p class="small-title">PREMIUM CAR RENTAL</p>
            <h1>YOUR JOURNEY.<br><span>YOUR WAY.</span></h1>
            <p class="hero-text">
                Discover the perfect car for every journey. Easy booking, flexible rental plans and reliable vehicles at your fingertips.
            </p>
            <div class="hero-buttons">
                <a href="/customer/browse.php" class="primary-btn">Explore Cars →</a>
                <a href="#about" class="secondary-btn">Learn More</a>
            </div>
        </div>
        <div class="hero-car">
            <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85" alt="Premium Sports Car">
        </div>
    </section>

    <!-- ================= SEARCH BOX ================= -->
    <section class="search-section">
        <div class="search-box">
            <div class="search-item">
                <label>Pick-up Location</label>
                <div class="input">📍<span>George Town, Penang</span></div>
            </div>
            <div class="search-item">
                <label>Pick-up Date</label>
                <div class="input">📅<span>24 Sep 2026</span></div>
            </div>
            <div class="search-item">
                <label>Return Date</label>
                <div class="input">📅<span>27 Sep 2026</span></div>
            </div>
            
            <!-- 修复了这里的乱码 BUG -->
            <button class="search-btn" onclick="location.href='/customer/browse.php'">
                Find a Car
            </button>
        </div>
    </section>

    <!-- ================= WHY US ================= -->
    <!-- 中间保持不变... -->
    <section class="why-us" id="about">
       <!-- 你的原代码 -->
    </section>

    <!-- ================= CTA ================= -->
    <section class="cta" id="contact">
        <div>
            <p>READY TO HIT THE ROAD?</p>
            <h2>Your next adventure<br>starts here.</h2>
        </div>
        <!-- 也可以改成动态判断 -->
        <a href="<?= isset($_SESSION['user_id']) ? '/customer/browse.php' : '/system/login.php' ?>">
            Book Your Car →
        </a>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer>
        <div class="footer-logo">DRIV<span>O</span>RA</div>
        <p>Premium cars. Simple booking.<br>Unforgettable journeys.</p>
        <div class="footer-links">
            <a href="index.php">Home</a>
            <a href="/customer/browse.php">Cars</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>
        <div class="copyright">
            © 2026 DRIVORA. All Rights Reserved.
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="script.js"></script>
</body>
</html>