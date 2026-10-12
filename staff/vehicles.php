<?php
// Enforce staff authentication guard
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['staff']);

// Include database connection configuration ($pdo)
require_once __DIR__ . '/../config/db.php';

$message = '';

// Handle vehicle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'];

    // Integrity constraint check: verify if vehicle is linked to existing rental records
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE vehicle_id = ?");
    $stmt->execute([$vehicle_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Prevent deletion if rental history exists
        $message = 'This car already has rentals, so it cannot be deleted. Set its status to maintenance instead.';
    } else {
        // Safe to delete if no rental records exist
        $stmt = $pdo->prepare("DELETE FROM vehicles WHERE vehicle_id = ?");
        $stmt->execute([$vehicle_id]);
        header('Location: vehicles.php');
        exit;
    }
}

// Fetch all vehicles with category names joined from categories table
$sql = "SELECT vehicles.*, categories.categories_name
        FROM vehicles
        JOIN categories ON vehicles.categories_id = categories.categories_id
        ORDER BY vehicles.vehicle_id";

$vehicles = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/panel.css">
</head>
<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span>O</span>RA <small>STAFF</small></a>
        <ul>
            <li><a href="rentals.php">Rentals</a></li>
            <li><a href="vehicles.php" class="active">Vehicles</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <div class="top-bar">
            <h1>Manage Vehicles</h1>
            <a href="vehicle-form.php" class="btn">+ Add Car</a>
        </div>

        <?php if ($message != ''): ?>
            <!-- Display constraint check warning message -->
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Car</th>
                <th>Category</th>
                <th>Price / day</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($vehicles as $v): ?>
            <tr>
                <td>#<?php echo $v['vehicle_id']; ?></td>
                <td><?php echo htmlspecialchars($v['brand'] . ' ' . $v['model']); ?></td>
                <td><?php echo htmlspecialchars($v['categories_name']); ?></td>
                <td>RM<?php echo number_format($v['price_per_day'], 2); ?></td>
                <td><span class="status <?php echo strtolower($v['status']); ?>"><?php echo strtoupper($v['status']); ?></span></td>
                <td>
                    <a href="vehicle-form.php?id=<?php echo $v['vehicle_id']; ?>" class="btn grey">Edit</a>
                    
                    <!-- Deletion form with client-side confirmation -->
                    <form method="post" onsubmit="return confirm('Delete this car?');">
                        <input type="hidden" name="vehicle_id" value="<?php echo $v['vehicle_id']; ?>">
                        <button class="no">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>