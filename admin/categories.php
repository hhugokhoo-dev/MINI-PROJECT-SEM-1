<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// only admin can open this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /system/login.php');
    exit;
}

function e($text) {
    return htmlspecialchars((string)$text);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO categories (categories_name, seats_label, description) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['categories_name'], $_POST['seats_label'], $_POST['description']]);

    } elseif ($action === 'edit') {
        $stmt = $pdo->prepare("UPDATE categories SET categories_name = ?, seats_label = ?, description = ? WHERE categories_id = ?");
        $stmt->execute([$_POST['categories_name'], $_POST['seats_label'], $_POST['description'], $_POST['categories_id']]);

    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE categories_id = ?");
            $stmt->execute([$_POST['categories_id']]);
        } catch (PDOException $e) {
            // a category that still has cars cannot be deleted
            $message = 'This category still has cars, so it cannot be deleted.';
        }
    }

    if ($message === '') {
        header('Location: categories.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY categories_id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/panel.css">
</head>
<body>

    <nav>
        <a href="/mainpage/index.php" class="logo">DRIV<span>O</span>RA <small>ADMIN</small></a>
        <ul>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <h1>Manage Categories</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- add a new category -->
        <form method="post" class="row">
            <input type="hidden" name="action" value="add">
            <input type="text" name="categories_name" placeholder="Category name" required>
            <input type="text" name="seats_label" placeholder="Seats (e.g. 4-5 seaters)">
            <input type="text" name="description" placeholder="Description">
            <button class="ok">+ Add</button>
        </form>

        <br>

        <!-- existing categories: edit or delete -->
        <?php foreach ($categories as $c): ?>
        <form method="post" class="row">
            <span class="id">#<?php echo $c['categories_id']; ?></span>
            <input type="hidden" name="categories_id" value="<?php echo $c['categories_id']; ?>">
            <input type="text" name="categories_name" value="<?php echo e($c['categories_name']); ?>" required>
            <input type="text" name="seats_label" value="<?php echo e($c['seats_label']); ?>">
            <input type="text" name="description" value="<?php echo e($c['description']); ?>">
            <button name="action" value="edit">Save</button>
            <button name="action" value="delete" class="no" onclick="return confirm('Delete this category?');">Delete</button>
        </form>
        <?php endforeach; ?>
    </div>

</body>
</html>