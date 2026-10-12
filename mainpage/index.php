<?php
// Start session to access user authentication data
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRIVORA - Car Rental</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">
            DRIV<span>O</span>RA
        </div>

        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="/customer/browse.php">Cars</a>
            <a href="#about">About Us</a>
        
        </nav>

        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Render dashboard link based on authenticated user role -->
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/admin/categories.php" class="nav-btn" style="background: #555;">Admin Panel</a>
                <?php elseif ($_SESSION['role'] === 'staff'): ?>
                    <a href="/staff/rentals.php" class="nav-btn" style="background: #555;">Staff Panel</a>
                <?php else: ?>
                    <a href="/customer/my-rentals.php" class="nav-btn" style="background: #555;">My Rentals</a>
                <?php endif; ?>

                <a href="/system/logout.php" class="nav-btn">Logout</a>
            <?php else: ?>
                <!-- Visitor is not logged in -->
                <a href="/system/login.php" class="nav-btn">Login and Book Now</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <p class="small-title">PREMIUM CAR RENTAL</p>
            <h1>YOUR JOURNEY.<br><span>YOUR WAY.</span></h1>

            <p class="hero-text">
                Discover the perfect car for every journey. Easy booking, flexible rental plans, and reliable vehicles at your fingertips.
            </p>

            <div class="hero-buttons">
                <a href="/customer/browse.php" class="primary-btn">Explore Cars →</a>
            </div>
        </div>

        <div class="hero-car">
            <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85" alt="Premium Sports Car">
        </div>
    </section>

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

            <button class="search-btn" onclick="location.href='/customer/browse.php'">
                Find a Car
            </button>
        </div>
    </section>

    <!-- About Us / Why Choose DRIVORA Section -->
    <section class="why-us" id="about">
        <div class="section-heading">
            <p>WHY CHOOSE DRIVORA</p>
            <h2>Driven by Excellence,<br>Tailored for Luxury.</h2>
            <span>Experience the ultimate driving experience with transparent pricing and a top-tier vehicle fleet.</span>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="icon">🏎️</div>
                <h3>Premium Fleet</h3>
                <p>Handpicked luxury sedans, sports coupes, and spacious MPVs maintained to peak performance standards.</p>
            </div>

            <div class="feature-card">
                <div class="icon">⚡</div>
                <h3>Instant Booking</h3>
                <p>Reserve your dream car in seconds with transparent pricing, zero hidden fees, and instant online confirmation.</p>
            </div>

            <div class="feature-card">
                <div class="icon">🛡️</div>
                <h3>Full Peace of Mind</h3>
                <p>Comprehensive insurance coverage and dedicated customer support to ensure a worry-free rental journey.</p>
            </div>

            <div class="feature-card">
                <div class="icon">📍</div>
                <h3>Prime Pickups</h3>
                <p>Convenient pickup and drop-off points located strategically across key hubs in Penang and beyond.</p>
            </div>
        </div>
    </section>

    <section class="cta" id="contact">
        <div>
            <p>READY TO HIT THE ROAD?</p>
            <h2>Your next adventure<br>starts here.</h2>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/customer/browse.php">Book Your Car →</a>
        <?php else: ?>
            <a href="/system/login.php">Book Your Car →</a>
        <?php endif; ?>
    </section>

    <footer>
        <div class="footer-logo">DRIV<span>O</span>RA</div>
        <p>Premium cars. Simple booking.<br>Unforgettable journeys.</p>

        <div class="footer-links">
            <a href="index.php">Home</a>
            <a href="/customer/browse.php">Cars</a>
            <a href="#about">About</a>
        </div>

        <div class="copyright">
            © 2026 DRIVORA. All Rights Reserved.
        </div>
    </footer>

</body>
</html>