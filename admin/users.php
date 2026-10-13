<?php
// ensure only admin can enter this page
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['admin']);

// Include database connection configuration ($pdo)
require_once __DIR__ . '/../config/db.php';

// Store currently logged-in admin user ID and initialize message variable to work in self protect
$my_id = $_SESSION['user_id'];

$message = '';
// to save the prevent accident or not allowed control mess

// Handle user management actions (Role Change or Account Deletion)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    // Guard constraint: Prevent admins from altering or deleting their own active account to customer 
    if ($user_id == $my_id) {
        $message = 'You cannot change or delete your own account.';

    // Action 1: Change user role
    } else if ($action === 'change_role') {
        $role = $_POST['role'];

        // check the data been transform for front by cus,sta,ad to prevent hacker pass unvalid role by changing table
        if (in_array($role, ['customer', 'staff', 'admin'])) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            $stmt->execute([$role, $user_id]);
        }
        header('Location: users.php');
        exit;

    // Action 2: Delete user account (with foreign key relationship check)
    } else if ($action === 'delete') {
        // Check if user has associated rental records before deleting
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Prevent deletion if rental history exists
            $message = 'This user already has rentals, so the account cannot be deleted.';
        } else {
            // Proceed with user account deletion
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            header('Location: users.php');
            exit;
        }
    }
}

// sql checking only take userid,name,email,role and skip hashpsw to prevent spread user psw even though got problem
$users = $pdo->query("SELECT user_id, name, email,role FROM users ORDER BY user_id")->fetchAll(PDO::FETCH_ASSOC);
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
            <li><a href="users.php" class="active">Users</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <h1>Manage Users</h1>

        <?php if ($message != ''): ?>
            <!-- Display action restriction or error message -->
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <table>
            <tr> <!--tablerow-->
                <th>ID</th> <!-- tablehead -->
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?php echo $u['user_id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>

                <?php if ($u['user_id'] == $my_id): ?>
                    <!-- Disable role modification and deletion for current logged-in admin -->
                    <td><?php echo htmlspecialchars($u['role']); ?> <small>(you)</small></td>
                    <td>-</td>
                <?php else: ?>
                    <!-- Form for updating user role -->
                    <td>
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <select name="role">
                                <?php foreach (['customer', 'staff', 'admin'] as $role): ?>
                                    <option value="<?php echo $role; ?>" <?php if ($u['role'] === $role) { echo 'selected'; } ?>><?php echo $role; ?></option>
                                <?php endforeach; ?>
                                <!-- use foreach see three role  -->
                                 <!-- use select and compare the current role and the role in database is same anot  -->
                                  <!-- if same then add selected to make sure is the real role of current character -->
                            </select>

                            <button name="action" value="change_role">Save</button>
                            <!-- act as butt and action ,when pressed backend can get changerole then can jump to root change role -->

                        </form>
                    </td>

                    <!-- Form for deleting user account -->
                    <td>
                        <form method="post" onsubmit="return confirm('Delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                            <button name="action" value="delete" class="no">Delete</button>
                        </form>
                    </td>

                <?php endif; ?>
                <!-- use : replace {,use endif replace } -->
                 <!-- used to act as a mark at the end of if stat -->

            </tr>
            <?php endforeach; ?>
            <!-- endwhile,endfor -->

        </table>
    </div>

</body>
</html>