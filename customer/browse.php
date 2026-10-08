<?php
session_start();
require_once __DIR__ . '/../config/db.php';

function e($text) {
    return htmlspecialchars((string)$text);
}

// is someone logged in? only customers (or visitors) see the RENT NOW button
$logged_in = isset($_SESSION['user']);
$can_rent  = !$logged_in || $_SESSION['user']['role'] === 'customer';

// all categories (Coupe, Sedan, MPV ...)
$categories = $pdo->query("SELECT * FROM categories ORDER BY categories_id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse-Cars</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/browse.css">
</head>
<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span style="color:#f26b21;">O</span>RA</a>
        <ul>
            <li><a href="/mainpage/index.php">Home</a></li>
            <?php if ($logged_in): ?>
                <?php if ($_SESSION['user']['role'] === 'customer'): ?>
                    <li><a href="my-rentals.php">My Rentals</a></li>
                <?php endif; ?>
                <li><a href="/system/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="/system/login.php">Login</a></li>
                <li><a href="/system/register.php">Register Now</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <header class="header-bg">
        <div class="header-content">
            <p class="subtitle"><?php echo e(strtoupper(implode(' · ', array_column($categories, 'categories_name')))); ?></p>
        </div>
    </header>

    <section class="skin-description">
        <p class="desc-text"><strong>Rental Requirements</strong></p>
        <ul>
            <li>Minimum age: 21 years old</li>
            <li>Valid driving license (held for at least 2 years)</li>
            <li>Valid IC / Passport required at pick-up</li>
            <li>Refundable security deposit applies</li>
            <li>Late return and damage charges apply as per rental agreement</li>
        </ul>
    </section>

    <?php foreach ($categories as $c): ?>

        <?php
        // the cars in this category
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE categories_id = ? ORDER BY vehicle_id");
        $stmt->execute([$c['categories_id']]);
        $cars = $stmt->fetchAll();

        // skip categories that have no cars
        if (count($cars) === 0) {
            continue;
        }
        ?>

        <h2 class="category-heading">
            <?php echo e($c['categories_name']); ?> <span><?php echo e($c['seats_label']); ?></span>
        </h2>

        <div class="product-grid">

            <?php foreach ($cars as $v): ?>
            <article class="product-card">
                <img class="card-img" src="<?php echo e($v['image']); ?>" alt="">
                <div class="card-body">
                    <h3><?php echo e($v['brand'] . ' ' . $v['model']); ?></h3>
                    <p class="desc"><?php echo e($v['engine']); ?></p>
                    <p class="desc"><?php echo e($v['transmission_drive']); ?></p>
                    <p class="desc"><?php echo e($v['horsepower']); ?>hp</p>
                    <p class="desc">0–100 km/h : <?php echo e($v['acceleration_0_100']); ?> s</p>

                    <div class="card-footer">
                        <span class="car-tag firstdesc"><?php echo e($v['feature_tag']); ?></span>
                        <span class="car-tag seconddesc">“<?php echo e($v['slogan']); ?>”</span>
                    </div>

                    <p class="card-price">RM<?php echo number_format($v['price_per_day']); ?> <span>/day</span></p>

                    <?php if ($can_rent): ?>
                        <?php if ($v['status'] === 'available'): ?>
                            <button class="rent-btn" onclick="location.href='pending.php?id=<?php echo $v['vehicle_id']; ?>'">RENT NOW</button>
                        <?php else: ?>
                            <button class="rent-btn" disabled>NOT AVAILABLE</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>

        </div>

    <?php endforeach; ?>

    <footer>
        <p>©️ 2026 DRIVORA | Drive further. Rent smarter.</p>
    </footer>

</body>
</html>