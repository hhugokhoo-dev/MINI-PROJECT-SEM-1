<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 只有 Customer 可以访问
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: /system/login.php');
    exit;
}

// 处理取消订单的请求 (Cancel Button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_rental_id'])) {
    // 只能取消自己的，且状态必须是 pending 的订单
    $stmt = $pdo->prepare("UPDATE rentals SET status = 'cancelled' WHERE rental_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$_POST['cancel_rental_id'], $_SESSION['user_id']]);

    header('Location: my-rentals.php');
    exit;
}

// 只查询当前登录顾客的订单
$stmt = $pdo->prepare(
    "SELECT r.*, v.brand, v.model
     FROM rentals r
     JOIN vehicles v ON r.vehicle_id = v.vehicle_id
     WHERE r.user_id = ?
     ORDER BY r.created_at DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$my_rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/panel.css"> <!-- 假设借用你现有的后台样式 -->
</head>
<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span>O</span>RA</a>
        <ul>
            <li><a href="browse.php">Browse Cars</a></li>
            <li><a href="my-rentals.php" class="active">My Rentals</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <h1>My Rentals</h1>

        <table>
            <tr>
                <th>Order ID</th>
                <th>Car</th>
                <th>Pick-up & Return</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($my_rentals as $r): ?>
            <tr>
                <td>#<?php echo $r['rental_id']; ?></td>
                <td><?php echo htmlspecialchars($r['brand'] . ' ' . $r['model']); ?></td>
                <td><?php echo date('d M Y', strtotime($r['start_date'])); ?> – <?php echo date('d M Y', strtotime($r['end_date'])); ?></td>
                <td>RM<?php echo number_format($r['total_price'], 2); ?></td>
                <td><span class="status <?php echo $r['status']; ?>"><?php echo strtoupper($r['status']); ?></span></td>
                <td>
                    <!-- 如果是 pending 状态，允许取消 -->
                    <?php if ($r['status'] === 'pending'): ?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to cancel this rental?');">
                            <input type="hidden" name="cancel_rental_id" value="<?php echo $r['rental_id']; ?>">
                            <button type="submit" class="btn" style="background-color: #dc3545; padding: 5px 10px; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if (count($my_rentals) === 0): ?>
            <p class="empty">You have no rental records yet. <a href="browse.php">Go book a car!</a></p>
        <?php endif; ?>
    </div>

</body>
</html>