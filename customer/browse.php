<?php
// Start or resume session to access user details
session_start();

// Include database connection file ($pdo)
require_once __DIR__ . '/../config/db.php';

// Check if user is currently logged in
$logged_in = isset($_SESSION['user_id']);

// Allow renting only if visitor is not logged in OR logged in as a 'customer'
$can_rent  = !$logged_in || (isset($_SESSION['role']) && $_SESSION['role'] === 'customer');

// Fetch all vehicle categories from the database ordered by category ID
$categories = $pdo->query("SELECT * FROM categories ORDER BY categories_id")->fetchAll(PDO::FETCH_ASSOC);

// Extract category names and automatically expand MPV to its full name
$category_names = [];
foreach ($categories as $c) {
    $name = $c['categories_name'];
    // Append full name if category is MPV
    if (strtoupper($name) === 'MPV') {
        $name = 'MPV (Multi-Purpose Vehicle)';
    }
    $category_names[] = $name;
}
// Join all category names into a single string separated by ' · '
$subtitle = implode(' · ', $category_names);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Cars - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/browse.css">
</head>

<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span style="color:#f26b21;">O</span>RA</a>
        <ul>
            <li><a href="/mainpage/index.php">Home</a></li>
            <?php if ($logged_in): ?>

                <!-- Check if the logged-in user is a customer -->
                 <!-- make sure only customer acc can see enter my rental -->
                <?php if ($_SESSION['role'] === 'customer'): ?>
                    <li><a href="my-rentals.php">My Rentals</a></li>
                <?php endif; ?>

                <li><a href="/system/logout.php">Logout</a></li>

            <?php else: ?>

                <!-- Visitor is not logged in -->
                <li><a href="/system/login.php">Login</a></li>
                <li><a href="/system/register.php">Register Now</a></li>

            <?php endif; ?>
        </ul>
    </nav>

    <header class="header-bg">
        <div class="header-content">
            <!-- Output concatenated category names in uppercase safely -->
            <p class="subtitle"><?php echo htmlspecialchars(strtoupper($subtitle)); ?></p>
        </div>
    </header>

    <section class="rental-terms">
        <p class="desc-text"><strong>Rental Requirements</strong></p>
        <ul>
            <li>Minimum age: 21 years old</li>
            <li>Valid driving license (held for at least 2 years)</li>
            <li>Valid IC / Passport required at pick-up</li>
            <li>Refundable security deposit applies</li>
            <li>Late return and damage charges apply as per rental agreement</li>
        </ul>
    </section>

    <?php 
    // Outer Loop: Iterate through each category ($c) in the $categories array
    // every cycle means handle car category
    foreach ($categories as $c): 

        // Prepare SQL query to select all vehicles under current category ID
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE categories_id = ? ORDER BY vehicle_id");
        
        // Execute query with current category ID
        $stmt->execute([$c['categories_id']]);
        
        // Retrieve all matching vehicles as an associative array
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If no vehicles exist in this category, skip rendering and move to next category
        if (count($cars) === 0) continue;

        // Format category header name (Add full name for MPV)
        $cat_title = $c['categories_name'];
        if (strtoupper($cat_title) === 'MPV') {
            $cat_title = 'MPV (Multi-Purpose Vehicle)';
        }
    ?>

        <h2 class="category-heading">
            <?php echo htmlspecialchars($cat_title); ?> 
            <span><?php echo htmlspecialchars($c['seats_label']); ?></span>
        </h2>

        <div class="product-grid">
            <?php 
            // Inner Loop: Iterate through each vehicle ($v) belonging to the current category
            foreach ($cars as $v): 
            ?>
            <article class="product-card">
                <img class="card-img" src="<?php echo htmlspecialchars($v['image']); ?>" alt="">
                <div class="card-body">
                    <!-- <h3><?php echo htmlspecialchars($v['brand'] . ' ' . $v['model']); ?></h3> -->
                    
                    <p class="desc"><?php echo htmlspecialchars($v['engine']); ?></p>
                    <p class="desc"><?php echo htmlspecialchars($v['horsepower']); ?> hp · <?php echo htmlspecialchars($v['transmission_drive']); ?></p>

                    <div class="card-footer">
                        <span class="car-tag firstdesc"><?php echo htmlspecialchars($v['feature_tag']); ?></span>
                    </div>
<!-- format price like 1599.00 to 1599 -->
                    <p class="card-price">RM<?php echo number_format($v['price_per_day'], 0); ?> <span>/day</span></p>

                    <?php 
                    // Check if current user role is allowed to rent (Guests or Customers only)
                    // if is admin or staff login then dont have any butt

                    if ($can_rent): 
                        // Check if the car is available for booking
                        if ($v['status'] === 'available'): 
                    ?>
                    
                            <button class="rent-btn" onclick="location.href='pending.php?id=<?php echo $v['vehicle_id']; ?>'">RENT NOW</button>
                        <?php else: ?>
                            <button class="rent-btn" disabled>NOT AVAILABLE</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </article>
            <?php 
            // End of Inner Loop (Vehicles)
            endforeach; 
            ?>
        </div>

    <?php 
    // End of Outer Loop (Categories)
    endforeach; 
    ?>

    <footer>
        <p>©️ 2026 DRIVORA | Drive further. Rent smarter.</p>
    </footer>

</body>
</html>