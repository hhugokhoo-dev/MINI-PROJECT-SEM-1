<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// must be logged in
   if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
       header('Location: /system/login.php');
       exit;
   }

// get the car (pending.php?id=3)
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die('Car not found. <a href="browse.php">Back</a>');
}

$error = '';
$done  = false;

// when the form is submitted, save the rental as pending
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['start_date'];
    $end   = $_POST['end_date'];

    if ($start < date('Y-m-d')) {
        $error = 'Pick-up date cannot be in the past.';
    } elseif ($end < $start) {
        $error = 'Return date must be after the pick-up date.';
    } else {
        $days  = max(1, (int)((strtotime($end) - strtotime($start)) / 86400));
        $total = $days * $car['price_per_day'];

        $stmt = $pdo->prepare(
            "INSERT INTO rentals (user_id, vehicle_id, start_date, end_date, total_price, status)
             VALUES (?, ?, ?, ?, ?, 'pending')"
        );
        $stmt->execute([$_SESSION['user']['id'], $id, $start, $end, $total]);

        $done = true;
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

        <!-- rental saved: show pending -->
        <h1>Request Received</h1>
        <div class="card">
            <div class="body">
                <span class="badge">⏳ PENDING</span>

                <table>
                    <tr><td>Car</td><td><?php echo $car['brand'] . ' ' . $car['model']; ?></td></tr>
                    <tr><td>Pick-up date</td><td><?php echo date('d M Y', strtotime($start)); ?></td></tr>
                    <tr><td>Return date</td><td><?php echo date('d M Y', strtotime($end)); ?></td></tr>
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

        <!-- choose dates -->
        <h1>Rent This Car</h1>

        <div class="card">
            <img src="<?php echo $car['image']; ?>" alt="">
            <div class="body">
                <h2><?php echo $car['brand'] . ' ' . $car['model']; ?></h2>
                <p class="specs">
                    <?php echo $car['engine']; ?><br>
                    <?php echo $car['transmission_drive']; ?><br>
                    <?php echo $car['horsepower']; ?>hp · 0–100 km/h: <?php echo $car['acceleration_0_100']; ?> s
                </p>
                <p class="price">RM<?php echo number_format($car['price_per_day']); ?> <span>/day</span></p>
            </div>
        </div>

        <div class="card">
            <div class="body">
                <h2>Choose your dates</h2>

                <?php if ($error): ?>
                    <div class="error"><?php echo $error; ?></div>
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