<?php
// PHP code starts here
session_start();
// open the session so we can check whether someone is logged in
?>
<!-- PHP ends here; plain HTML starts below -->
<!DOCTYPE html>
<!-- tells the browser this is an HTML5 page -->
<html lang="en">
<!-- the page starts; its language is English -->
<head>
<!-- the invisible information starts -->
    <meta charset="UTF-8">
    <!-- use UTF-8 so all characters display correctly -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- make the page fit phone screens -->
    <title>DRIVORA - Car Rental</title>
    <!-- the text shown on the browser tab -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- warm up the connection to Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- warm up the connection to the font files -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- load the Inter font -->
    <link rel="stylesheet" href="style.css">
    <!-- load our own style file (it is in the same folder as this page) -->
</head>
<!-- end of the invisible information -->
<body>
<!-- the visible part starts -->

    <header class="navbar">
    <!-- the top navigation bar starts -->
        <div class="logo">
        <!-- the logo box -->
            DRIV<span>O</span>RA
            <!-- the brand name; the O is coloured by CSS -->
        </div>
        <!-- end of the logo box -->
        <nav>
        <!-- the menu links start -->
            <a href="index.php" class="active">Home</a>
            <!-- link to this page; "active" highlights it -->
            <a href="/customer/browse.php">Cars</a>
            <!-- link to the car list page -->
            <a href="#about">About Us</a>
            <!-- jump down to the part of this page with id="about" -->
            <a href="#contact">Contact</a>
            <!-- jump down to the part of this page with id="contact" -->
        </nav>
        <!-- end of the menu links -->

        <div>
        <!-- a box for the buttons on the right side -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <!-- PHP: if someone is logged in, show what is below -->
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <!-- PHP: if the user is admin, show the line below -->
                    <a href="/admin/categories.php" class="nav-btn" style="background: #555;">Admin Panel</a>
                    <!-- a grey button to the admin page -->
                <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <!-- PHP: otherwise, if the user is staff, show the line below -->
                    <a href="/staff/rentals.php" class="nav-btn" style="background: #555;">Staff Panel</a>
                    <!-- a grey button to the staff page -->
                <?php else: ?>
                <!-- PHP: otherwise the user is a customer, show the line below -->
                    <a href="/customer/my-rentals.php" class="nav-btn" style="background: #555;">My Rentals</a>
                    <!-- a grey button to the customer's own rentals -->
                <?php endif; ?>
                <!-- PHP: end of the role check -->

                <a href="/system/logout.php" class="nav-btn">Logout</a>
                <!-- the Logout button (shown to everyone who is logged in) -->
            <?php else: ?>
            <!-- PHP: otherwise nobody is logged in, show the line below -->
                <a href="/system/login.php" class="nav-btn">Login / Book Now</a>
                <!-- the Login button -->
            <?php endif; ?>
            <!-- PHP: end of the logged-in check -->
        </div>
        <!-- end of the buttons box -->
    </header>
    <!-- end of the navigation bar -->

    <section class="hero">
    <!-- the big top section starts -->
        <div class="hero-content">
        <!-- the text side of the big section -->
            <p class="small-title">PREMIUM CAR RENTAL</p>
            <!-- the small title above the heading -->
            <h1>YOUR JOURNEY.<br><span>YOUR WAY.</span></h1>
            <!-- the big heading; <br> makes a new line; the span part gets its own colour -->
            <p class="hero-text">
            <!-- the paragraph under the heading -->
                Discover the perfect car for every journey. Easy booking, flexible rental plans and reliable vehicles at your fingertips.
                <!-- the text of the paragraph -->
            </p>
            <!-- end of the paragraph -->
            <div class="hero-buttons">
            <!-- a box for the two buttons -->
                <a href="/customer/browse.php" class="primary-btn">Explore Cars →</a>
                <!-- the main button; it goes to the car list -->
                <a href="#about" class="secondary-btn">Learn More</a>
                <!-- the second button; it jumps down to the About part -->
            </div>
            <!-- end of the buttons box -->
        </div>
        <!-- end of the text side -->
        <div class="hero-car">
        <!-- the picture side of the big section -->
            <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=85" alt="Premium Sports Car">
            <!-- a car picture from the internet; alt is the text shown if the picture cannot load -->
        </div>
        <!-- end of the picture side -->
    </section>
    <!-- end of the big top section -->

    <section class="search-section">
    <!-- the search area starts -->
        <div class="search-box">
        <!-- the white search box -->
            <div class="search-item">
            <!-- one item: the location -->
                <label>Pick-up Location</label>
                <!-- the small title of this item -->
                <div class="input">📍<span>George Town, Penang</span></div>
                <!-- a fixed text showing the location (it is only for show) -->
            </div>
            <!-- end of the location item -->
            <div class="search-item">
            <!-- one item: the pick-up date -->
                <label>Pick-up Date</label>
                <!-- the small title of this item -->
                <div class="input">📅<span>24 Sep 2026</span></div>
                <!-- a fixed text showing a date (it is only for show) -->
            </div>
            <!-- end of the pick-up item -->
            <div class="search-item">
            <!-- one item: the return date -->
                <label>Return Date</label>
                <!-- the small title of this item -->
                <div class="input">📅<span>27 Sep 2026</span></div>
                <!-- a fixed text showing a date (it is only for show) -->
            </div>
            <!-- end of the return item -->

            <button class="search-btn" onclick="location.href='/customer/browse.php'">
            <!-- a button; when clicked, the browser goes to the car list -->
                Find a Car
                <!-- the text on the button -->
            </button>
            <!-- end of the button -->
        </div>
        <!-- end of the search box -->
    </section>
    <!-- end of the search area -->

    <section class="why-us" id="about">
    <!-- the About part starts; id="about" is what the About Us links jump to -->
        <!-- PUT YOUR ORIGINAL "WHY US" CONTENT HERE (this part was empty in the file you sent) -->
    </section>
    <!-- end of the About part -->

    <section class="cta" id="contact">
    <!-- the bottom call-to-action part starts; id="contact" is what the Contact links jump to -->
        <div>
        <!-- a box for the text -->
            <p>READY TO HIT THE ROAD?</p>
            <!-- the small line -->
            <h2>Your next adventure<br>starts here.</h2>
            <!-- the big line; <br> makes a new line -->
        </div>
        <!-- end of the text box -->

        <?php if (isset($_SESSION['user_id'])): ?>
        <!-- PHP: if someone is logged in, show the line below -->
                       <a href="/customer/browse.php">Book Your Car →</a>
            <!-- go straight to the car list -->
        <?php else: ?>
        <!-- PHP: otherwise, show the line below -->
            <a href="/system/login.php">Book Your Car →</a>
            <!-- go to the login page first -->
        <?php endif; ?>
        <!-- PHP: end of the if -->
    </section>
    <!-- end of the bottom part -->

    <footer>
    <!-- the footer starts -->
        <div class="footer-logo">DRIV<span>O</span>RA</div>
        <!-- the logo in the footer -->
        <p>Premium cars. Simple booking.<br>Unforgettable journeys.</p>
        <!-- the slogan; <br> makes a new line -->
        <div class="footer-links">
        <!-- a box for the footer links -->
            <a href="index.php">Home</a>
            <!-- link to this page -->
            <a href="/customer/browse.php">Cars</a>
            <!-- link to the car list -->
            <a href="#about">About</a>
            <!-- jump to the About part -->
            <a href="#contact">Contact</a>
            <!-- jump to the Contact part -->
        </div>
        <!-- end of the footer links -->
        <div class="copyright">
        <!-- the copyright box -->
            © 2026 DRIVORA. All Rights Reserved.
            <!-- the copyright text -->
        </div>
        <!-- end of the copyright box -->
    </footer>
    <!-- end of the footer -->

</body>
<!-- end of the visible part -->
</html>
<!-- end of the page -->