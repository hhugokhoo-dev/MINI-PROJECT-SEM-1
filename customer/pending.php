<?php
// Enforce customer authentication guard
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['customer']);

// Include database connection configuration ($pdo)
require_once __DIR__ . '/../config/db.php';

// Retrieve vehicle ID from GET request URL parameter
$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Fetch selected vehicle record from database
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if vehicle exists in database
if (!$car) {
    die('Car not found. <a href="browse.php">Back</a>');
    // end the loading to prevent show error
}

// Ensure vehicle status is available for booking
if ($car['status'] !== 'available') {
    // prevent repeat rental
    die('Sorry, this car is not available now. <a href="browse.php">Back</a>');
}

// Initialize form handling variables
$error = '';

// boolean,default is false means havent pass,if pass then show true
$done = false;

$start = '';
$end = '';
$days = 0;
$total = 0;

// Handle rental request form submission (POST)
// if first enter the page with GET request,skip to html
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    // Date Validation 1: Pick-up date cannot be in the past
    if ($start < date('Y-m-d')) {
        $error = 'Pick-up date cannot be in the past.';
    
    // Date Validation 2: Return date must be on or after pick-up date
    } elseif ($end < $start) {
        $error = 'Return date must be after the pick-up date.';
    
    } else {
        // Calculate total rental duration in days
        // using unix timestamp(use time second since 1970 then -minus both start and end )
        // 86400 is second in one days,use total second /86400 to get total days
        $days = (strtotime($end) - strtotime($start)) / 86400;
        if ($days < 1) {
            $days = 1; // Minimum booking duration is 1 day
        }

        // Calculate total rental cost
        $total = $days * $car['price_per_day'];

        // Insert new rental request with 'pending' status
        $stmt = $pdo->prepare("INSERT INTO rentals (user_id, vehicle_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        // ??prevent sql injection

        $stmt->execute([$_SESSION['user_id'], $id, $start, $end, $total]);

        $done = true; // Mark transaction as completed for view rendering
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Request - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/pending.css">
</head>
<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span>O</span>RA</a>
        <ul>
            <li><a href="browse.php">Browse Cars</a></li>
            <li><a href="my-rentals.php">My Rentals</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">

    <?php if ($done): ?>
        <!-- Render confirmation screen after successful submission -->
        <h1>Request Received</h1>
        <div class="card">
            <div class="body">
                <span class="badge">⏳ PENDING</span>

                <table>
                    <!-- echo=print ,html--=change danger symbol to safety symbol to prevent XSS
                    change the bad word first so that the browser wont run  -->
                    <tr><td>Car</td><td><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></td></tr>
                    <tr><td>Pick-up date</td><td><?php echo htmlspecialchars($start); ?></td></tr>
                    <tr><td>Return date</td><td><?php echo htmlspecialchars($end); ?></td></tr>
                    <tr><td>Rental days</td><td><?php echo $days; ?></td></tr>
                    <tr><td>Total price</td><td>RM<?php echo number_format($total, 2); ?></td></tr>
                </table>

                <p class="note">
                    Your request is waiting for staff to confirm.
                    Please bring your IC / Passport and driving license when you pick up the car.
                </p>

                <a href="my-rentals.php" class="btn">My Rentals</a>
                <a href="browse.php" class="btn grey">Browse More Cars</a>
            </div>
        </div>

    <?php else: ?>
        <!-- Render vehicle details and booking date form -->
        <h1>Rent This Car</h1>

        <div class="card">
            <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="">
            <div class="body">
                <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                <p class="specs">
                    <?php echo htmlspecialchars($car['engine']); ?><br>
                    <?php echo htmlspecialchars($car['transmission_drive']); ?><br>
                    <?php echo htmlspecialchars($car['horsepower']); ?>hp · 0–100 km/h: <?php echo htmlspecialchars($car['acceleration_0_100']); ?> s
                </p>
                <p class="price">RM<?php echo number_format($car['price_per_day']); ?> <span>/day</span></p>
            </div>
        </div>

        <div class="card">
            <div class="body">
                <h2>Choose your dates</h2>

                <?php if ($error != ''): ?>
                    <!-- Display date validation error message -->
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <label>Pick-up date</label>
                    <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>

                    <label>Return date</label>
                    <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required>

                    <button type="submit" class="btn">Submit Request</button>
                    <a href="browse.php" class="btn grey">Cancel</a>
                </form>

                <p class="note">Your request stays <b>pending</b> until staff confirms it.</p>
            </div>
        </div>

    <?php endif; ?>

    </div>

</body>
</html>