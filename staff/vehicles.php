<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// only staff can open this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header('Location: /system/login.php');
    exit;
}

$message = '';

// Delete button clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM vehicles WHERE vehicle_id = ?");
        $stmt->execute([$_POST['vehicle_id']]);

        header('Location: vehicles.php');
        exit;
    } catch (PDOException $e) {
        // a car that already has rentals cannot be deleted
        $message = 'This car already has rentals, so it cannot be deleted. Set its status to maintenance instead.';
    }
}

$vehicles = $pdo->query(
    "SELECT v.*, c.categories_name
     FROM vehicles v
     JOIN categories c ON v.categories_id = c.categories_id
     ORDER BY v.vehicle_id"
)->fetchAll(PDO::FETCH_ASSOC);
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
            <li><a href="vehicles.php">Vehicles</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <div class="top-bar">
            <h1>Manage Vehicles</h1>
            <a href="vehicle-form.php" class="btn">+ Add Car</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
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
                <td><?php echo $v['vehicle_id']; ?></td>
                <td><?php echo htmlspecialchars($v['brand'] . ' ' . $v['model']); ?></td>
                <td><?php echo htmlspecialchars($v['categories_name']); ?></td>
                <td>RM<?php echo number_format($v['price_per_day']); ?></td>
                <td><span class="status <?php echo $v['status']; ?>"><?php echo $v['status']; ?></span></td>
                <td>
                    <form method="post" onsubmit="return confirm('Delete this car?');">
                        <a href="vehicle-form.php?id=<?php echo $v['vehicle_id']; ?>" class="btn grey">Edit</a>
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