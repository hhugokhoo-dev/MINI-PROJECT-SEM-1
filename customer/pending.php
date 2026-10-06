<?php
session_start();
require_once __DIR__ . '/../config/db.php';   // this file must create the $pdo connection

// 1. Customer must be logged in
if (!isset($_SESSION['user'])) {
    header('Location: /system/login.php');
    exit;
}

// 2. Find the car the customer clicked (pending.php?id=3)
$vehicle_id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
$stmt->execute([$vehicle_id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die('Car not found. <a href="browse.php">Back to Browse</a>');
}

$error  = '';
$rental = null;   // stays null until the rental is saved

// 3. When the form is submitted, save the rental as "pending"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['start_date'] ?? '';
    $end   = $_POST['end_date'] ?? '';

    if ($start === '' || $end === '') {
        $error = 'Please choose both dates.';
    } elseif ($start < date('Y-m-d')) {
        $error = 'Pick-up date cannot be in the past.';
    } elseif ($end < $start) {
        $error = 'Return date must be after the pick-up date.';
    } else {
        // find the logged-in customer by email
        $user_id = $_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? null;

        if (!$user_id) {
            $error = 'Your account was not found. Please log in again.';
        } else {
            $days  = max(1, (int)((strtotime($end) - strtotime($start)) / 86400));
            $total = $days * $car['price_per_day'];

            $ins = $pdo->prepare(
                "INSERT INTO rentals (user_id, vehicle_id, start_date, end_date, total_price, status)
                 VALUES (?, ?, ?, ?, ?, 'pending')"
            );
            $ins->execute([$user_id, $vehicle_id, $start, $end, $total]);

            $rental = [
                'id'    => $pdo->lastInsertId(),
                'start' => $start,
                'end'   => $end,
                'days'  => $days,
                'total' => $total,
            ];
        }
    }
}

function e($text) {
    return htmlspecialchars((string)$text);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Request - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Quicksand', sans-serif; background: #f6f3ee; color: #222; }
        nav { background: #111; padding: 18px 40px; display: flex; justify-content: space-between; align-items: center; }
        nav .logo { font-family: 'Playfair Display', serif; font-size: 24px; color: #fff; text-decoration: none; letter-spacing: 2px; }
        nav .logo span { color: #f26b21; }
        nav ul { list-style: none; display: flex; gap: 24px; }
        nav a { color: #ddd; text-decoration: none; font-weight: 500; }
        .wrap { max-width: 760px; margin: 40px auto; padding: 0 20px; }
        h1 { font-family: 'Playfair Display', serif; margin-bottom: 20px; }
        .card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,.08); margin-bottom: 24px; }
        .card img { width: 100%; height: 280px; object-fit: cover; display: block; }
        .card .body { padding: 22px 26px; }
        .card h2 { font-family: 'Playfair Display', serif; margin-bottom: 8px; }
        .specs { color: #666; font-size: 15px; line-height: 1.7; }
        .price { color: #f26b21; font-size: 22px; font-weight: 700; margin-top: 12px; }
        .price span { color: #888; font-size: 14px; font-weight: 500; }
        label { display: block; font-weight: 600; margin: 14px 0 6px; }
        input[type=date] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit; font-size: 16px; }
        .btn { display: inline-block; margin-top: 22px; padding: 14px 28px; background: #f26b21; color: #fff; border: 0; border-radius: 8px; font-family: inherit; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn.grey { background: #444; margin-left: 8px; }
        .error { background: #fde8e8; color: #b42318; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .badge { display: inline-block; background: #fff3cd; color: #8a6100; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 18px 0; }
        td { padding: 10px 0; border-bottom: 1px solid #eee; }
        td:last-child { text-align: right; font-weight: 600; }
        .note { color: #666; font-size: 14px; line-height: 1.6; margin-top: 10px; }
    </style>
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

    <?php if ($rental): ?>

        <!-- ===== After saving: rental is pending ===== -->
        <h1>Request Received</h1>
        <div class="card">
            <div class="body">
                <span class="badge">⏳ PENDING CONFIRMATION</span>
                <table>
                    <tr><td>Rental No.</td><td>#<?php echo e($rental['id']); ?></td></tr>
                    <tr><td>Car</td><td><?php echo e($car['brand'] . ' ' . $car['model']); ?></td></tr>
                    <tr><td>Pick-up date</td><td><?php echo e($rental['start']); ?></td></tr>
                    <tr><td>Return date</td><td><?php echo e($rental['end']); ?></td></tr>
                    <tr><td>Rental days</td><td><?php echo e($rental['days']); ?></td></tr>
                    <tr><td>Total price</td><td>RM<?php echo number_format($rental['total'], 2); ?></td></tr>
                </table>
                <p class="note">
                    Your request is waiting for our staff to confirm.
                    Please bring your IC / Passport and driving license when you pick up the car.
                </p>
                <a href="my-rentals.php" class="btn">View My Rentals</a>
                <a href="browse.php" class="btn grey">Browse More Cars</a>
            </div>
        </div>

    <?php else: ?>

        <!-- ===== Before saving: choose dates ===== -->
        <h1>Rent This Car</h1>

        <div class="card">
            <img src="<?php echo e($car['image']); ?>" alt="<?php echo e($car['model']); ?>">
            <div class="body">
                <h2><?php echo e($car['brand'] . ' ' . $car['model']); ?></h2>
                <p class="specs">
                    <?php echo e($car['engine']); ?><br>
                    <?php echo e($car['transmission_drive']); ?><br>
                    <?php echo e($car['horsepower']); ?>hp · 0–100 km/h: <?php echo e($car['acceleration_0_100']); ?> s
                </p>
                <p class="price">RM<?php echo number_format($car['price_per_day']); ?> <span>/day</span></p>
            </div>
        </div>

        <div class="card">
            <div class="body">
                <h2>Choose your dates</h2>

                <?php if ($error): ?>
                    <div class="error"><?php echo e($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <label for="start_date">Pick-up date</label>
                    <input type="date" id="start_date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>

                    <label for="end_date">Return date</label>
                    <input type="date" id="end_date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required>

                    <button type="submit" class="btn">Submit Rental Request</button>
                    <a href="browse.php" class="btn grey">Cancel</a>
                </form>

                <p class="note">
                    Your request will be saved as <b>pending</b> until our staff confirms it.
                    Refundable deposit and late-return charges apply as per the rental agreement.
                </p>
            </div>
        </div>

    <?php endif; ?>

    </div>

</body>
</html>