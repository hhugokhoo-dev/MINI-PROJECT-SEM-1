<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// only staff can open this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header('Location: /system/login.php');
    exit;
}

// Confirm button clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE rentals SET status = 'confirmed' WHERE rental_id = ? AND status = 'pending'");
    $stmt->execute([$_POST['rental_id']]);

    header('Location: rentals.php');
    exit;
}

// all rentals with customer and car details (pending first)
$rentals = $pdo->query(
    "SELECT r.*, u.name, u.email, v.brand, v.model
     FROM rentals r
     JOIN users u ON r.user_id = u.user_id
     JOIN vehicles v ON r.vehicle_id = v.vehicle_id
     ORDER BY r.status = 'pending' DESC, r.created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Applications - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/panel.css">
</head>
<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span>O</span>RA <small>STAFF</small></a>
        <ul>
            <li><a href="rentals.php">Rentals</a></li>
            <li><a href="vehicles.php">Vehicles</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <h1>Rental Applications</h1>

        <table>
            <tr>
                <th>No.</th>
                <th>Customer</th>
                <th>Car</th>
                <th>Dates</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($rentals as $r): ?>
            <tr>
                <td>#<?php echo $r['rental_id']; ?></td>
                <td>
                    <?php echo htmlspecialchars($r['name']); ?><br>
                    <small><?php echo htmlspecialchars($r['email']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($r['brand'] . ' ' . $r['model']); ?></td>
                <td><?php echo date('d M', strtotime($r['start_date'])); ?> – <?php echo date('d M Y', strtotime($r['end_date'])); ?></td>
                <td>RM<?php echo number_format($r['total_price'], 2); ?></td>
                <td><span class="status <?php echo $r['status']; ?>"><?php echo $r['status']; ?></span></td>
                <td>
                    <?php if ($r['status'] === 'pending'): ?>
                        <form method="post">
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <button class="ok">Confirm</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if (count($rentals) === 0): ?>
            <p class="empty">No rental applications yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>