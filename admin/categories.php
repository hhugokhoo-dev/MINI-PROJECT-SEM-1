<?php
// Enforce admin authentication guard
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['admin']);

// Include database connection configuration ($pdo)
require_once __DIR__ . '/../config/db.php';

$message = '';

// Handle category management form requests (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') //read the action and proceed three root
 
{
    $action = $_POST['action'];

    // Action 1: Add a new vehicle category
    if ($action === 'add') {

        $stmt = $pdo->prepare("INSERT INTO categories (categories_name, seats_label, description) VALUES (?, ?, ?)");
        //  prepare to insert point and use ???to prevent sql injection

        $stmt->execute([$_POST['categories_name'], $_POST['seats_label'], $_POST['description']]);

        header('Location: categories.php');
        
        exit;
// force to stop php to prevent still continue loading when makesure

    // Action 2: Update existing category details
    } elseif ($action === 'edit') {

        $stmt = $pdo->prepare("UPDATE categories SET categories_name = ?, seats_label = ?, description = ? WHERE categories_id = ?");
      // able to update back to the database

        $stmt->execute([$_POST['categories_name'], $_POST['seats_label'], $_POST['description'], $_POST['categories_id']]);
        header('Location: categories.php');
        exit;

    // Action 3: Delete category (if no vehicles are attached)
    } elseif ($action === 'delete') {
        $categories_id = $_POST['categories_id'];

        // Foreign key integrity check: count vehicles currently linked to this category
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE categories_id = ?");
        $stmt->execute([$categories_id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Prevent deletion if category still contains  vehicles
            $message = 'This category still has cars, so it cannot be deleted.';
        } else {
            // Proceed with deletion if no linked vehicles exist

            $stmt = $pdo->prepare("DELETE FROM categories WHERE categories_id = ?");
            // program go database to check the latest data then arrange follow order and save to $categories 

            $stmt->execute([$categories_id]);
            header('Location: categories.php');
            exit;
        }
    }
}

// Fetch all categories ordered by ID for backend display
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
            <li><a href="categories.php" class="active">Categories</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="/system/logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="wrap">
        <h1>Manage Categories</h1>

        <?php if ($message != ''): ?>
            <!-- when the delete been block bc of still got vehicles will show errorrrr -->
            <div class="message"> <?php echo htmlspecialchars($message); ?> </div>
            <!-- use htmlblabla to pretent XSS -->

        <?php endif; ?>

        <!-- Form for adding a new category -->
        <form method="post" class="row">
            <input type="hidden" name="action" value="add">
            <input type="text" name="categories_name" placeholder="Category name" required>
            <input type="text" name="seats_label" placeholder="Seats (e.g. 4-5 seaters)">
            <input type="text" name="description" placeholder="Description">
            <button class="ok">+ Add</button>
        </form>






        <br>

        <?php foreach ($categories as $c): ?>
            <!-- use foreach see all the data been classification and wrap into <form> -->

        <!-- Form loop for editing or deleting existing categories -->
        <form method="post" class="row">
            <span class="id">#<?php echo $c['categories_id']; ?></span>
            <input type="hidden" name="categories_id" value="<?php echo $c['categories_id']; ?>">
            <input type="text" name="categories_name" value="<?php echo htmlspecialchars($c['categories_name']); ?>" required>
            <input type="text" name="seats_label" value="<?php echo htmlspecialchars($c['seats_label']); ?>">
            <input type="text" name="description" value="<?php echo htmlspecialchars($c['description']); ?>">
            <button name="action" value="edit">Save</button>
            <button name="action" value="delete" class="no" onclick="return confirm('Delete this category?');">Delete</button>
        <!-- add onclick js at button to prevent admin accidentally delete -->
        </form>
        <?php endforeach; ?>
    </div>

</body>
</html>