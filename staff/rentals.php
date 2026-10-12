<?php
// Enforce staff authentication guard
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['staff']);

// Include database connection configuration ($pdo)
require_once __DIR__ . '/../config/db.php';

// Handle rental status updates (Confirm or Cancel application)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rental_id = $_POST['rental_id'];
    $action = $_POST['action'];

    // Fetch existing rental record and current vehicle ID
    $stmt = $pdo->prepare("SELECT vehicle_id, status FROM rentals WHERE rental_id = ?");
    $stmt->execute([$rental_id]);
    $rental = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rental) {
        // Confirm pending rental and mark vehicle as 'rented'
        if ($action === 'confirm' && $rental['status'] === 'pending') {
            $stmt = $pdo->prepare("UPDATE rentals SET status = 'confirmed' WHERE rental_id = ?");
            $stmt->execute([$rental_id]);

            $stmt = $pdo->prepare("UPDATE vehicles SET status = 'rented' WHERE vehicle_id = ?");
            $stmt->execute([$rental['vehicle_id']]);

        // Cancel rental and restore vehicle availability if previously confirmed
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE rentals SET status = 'cancelled' WHERE rental_id = ?");
            $stmt->execute([$rental_id]);

            if ($rental['status'] === 'confirmed') {
                $stmt = $pdo->prepare("UPDATE vehicles SET status = 'available' WHERE vehicle_id = ?");
                $stmt->execute([$rental['vehicle_id']]);
            }
        }
    }

    // Refresh page to reflect updated status
    header('Location: rentals.php');
    exit;
}

// Fetch all rental applications joined with customer profile and vehicle info
$sql = "SELECT rentals.*, users.name, users.email, vehicles.brand, vehicles.model
        FROM rentals
        JOIN users ON rentals.user_id = users.user_id
        JOIN vehicles ON rentals.vehicle_id = vehicles.vehicle_id
        ORDER BY rentals.rental_id DESC";

$rentals = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
            <li><a href="rentals.php" class="active">Rentals</a></li>
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
                <th>Start</th>
                <th>End</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($rentals as $r): ?>
            <tr>
                <td>#<?php echo $r['rental_id']; ?></td>
                <td><?php echo htmlspecialchars($r['name']); ?> (<?php echo htmlspecialchars($r['email']); ?>)</td>
                <td><?php echo htmlspecialchars($r['brand'] . ' ' . $r['model']); ?></td>
                <td><?php echo htmlspecialchars($r['start_date']); ?></td>
                <td><?php echo htmlspecialchars($r['end_date']); ?></td>
                <td>RM<?php echo number_format($r['total_price'], 2); ?></td>
                <td><span class="status <?php echo strtolower($r['status']); ?>"><?php echo strtoupper($r['status']); ?></span></td>
                <td>
                    <?php if ($r['status'] === 'pending'): ?>
                        <!-- Form to confirm pending application -->
                        <form method="post">
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <button class="ok" name="action" value="confirm">Confirm</button>
                        </form>
                    <?php elseif ($r['status'] === 'confirmed'): ?>
                        <!-- Form to cancel confirmed rental -->
                        <form method="post" onsubmit="return confirm('Cancel this rental and make the car available again?');">
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <button class="no" name="action" value="cancel">Cancel</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if (count($rentals) == 0): ?>
            <!-- Empty state message if no applications exist -->
            <p class="empty">No rental applications yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>