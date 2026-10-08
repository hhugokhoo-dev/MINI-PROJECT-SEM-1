<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// only admin can open this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /system/login.php');
    exit;
}

$my_id   = $_SESSION['user']['id'];
$message = '';

// an admin cannot change or delete their own account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['user_id'] != $my_id) {

    if ($_POST['action'] === 'change_role') {
        if (in_array($_POST['role'], ['customer', 'staff', 'admin'])) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            $stmt->execute([$_POST['role'], $_POST['user_id']]);
        }

    } elseif ($_POST['action'] === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$_POST['user_id']]);
        } catch (PDOException $e) {
            // a user who already has rentals cannot be deleted
            $message = 'This user already has rentals, so the account cannot be deleted.';
        }
    }

    if ($message === '') {
        header('Location: users.php');
        exit;
    }
}

$users = $pdo->query("SELECT user_id, name, email, phone, role, created_at FROM users ORDER BY user_id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - DRIVORA</title>
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
        <h1>Manage Users</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Action</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['user_id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars((string)$u['phone']); ?></td>

                <?php if ($u['user_id'] == $my_id): ?>
                    <td><?php echo $u['role']; ?> <small>(you)</small></td>
                    <td>-</td>
                <?php else: ?>
                    <td>
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <select name="role">
                                <?php foreach (['customer', 'staff', 'admin'] as $role): ?>
                                    <option value="<?php echo $role; ?>" <?php if ($u['role'] === $role) echo 'selected'; ?>><?php echo $role; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button name="action" value="change_role">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" onsubmit="return confirm('Delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <button name="action" value="delete" class="no">Delete</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>