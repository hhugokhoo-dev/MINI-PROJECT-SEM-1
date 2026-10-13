<?php
// Enforce staff authentication guard
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['staff']);

// Include database connection configuration ($pdo)
require_once __DIR__ . '/../config/db.php';

// Determine operation mode: Add new vehicle ($id = 0) or Edit existing vehicle ($id > 0)
$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Fetch all categories for the category selection dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Initialize default empty vehicle array for form fields so that wont occur error in html
$car = [
    'categories_id' => '',
    'brand' => '',
    'model' => '',
    'engine' => '',
    'transmission_drive' => '',
    'horsepower' => '',
    'acceleration_0_100' => '',
    'feature_tag' => '',
    'slogan' => '',
    'price_per_day' => '',
    'status' => 'available',
    'image' => '',
];

// Load existing vehicle details from database if in Edit mode
if ($id != 0) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $car = $row;
    } else {
        die('Car not found. <a href="vehicles.php">Back</a>');
    }
}

// Handle vehicle form submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categories_id = $_POST['categories_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $engine = $_POST['engine'];
    $transmission_drive = $_POST['transmission_drive'];
    $horsepower = $_POST['horsepower'];
    $acceleration = $_POST['acceleration_0_100'];
    $feature_tag = $_POST['feature_tag'];
    $slogan = $_POST['slogan'];
    $price = $_POST['price_per_day'];
    $status = $_POST['status'];
    $image = $_POST['image'];

    // Update existing vehicle record
    if ($id != 0) {
        $stmt = $pdo->prepare("UPDATE vehicles SET categories_id = ?, brand = ?, model = ?, engine = ?, transmission_drive = ?, horsepower = ?, acceleration_0_100 = ?, feature_tag = ?, slogan = ?, price_per_day = ?, status = ?, image = ? WHERE vehicle_id = ?");
        $stmt->execute([$categories_id, $brand, $model, $engine, $transmission_drive, $horsepower, $acceleration, $feature_tag, $slogan, $price, $status, $image, $id]);
    
    // Insert new vehicle record
    } else {
        $stmt = $pdo->prepare("INSERT INTO vehicles (categories_id, brand, model, engine, transmission_drive, horsepower, acceleration_0_100, feature_tag, slogan, price_per_day, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$categories_id, $brand, $model, $engine, $transmission_drive, $horsepower, $acceleration, $feature_tag, $slogan, $price, $status, $image]);
    }

    // Redirect back to vehicle list page
    header('Location: vehicles.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Form - DRIVORA</title>
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

        <?php if ($id != 0): ?>
            <h1>Edit Car</h1>
        <?php else: ?>
            <h1>Add Car</h1>
        <?php endif; ?>

        <form method="post" class="form-card">

            <label>Category</label>
            <select name="categories_id" required>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['categories_id']; ?>" <?php if ($c['categories_id'] == $car['categories_id']) { echo 'selected'; } ?>>
                        <?php echo htmlspecialchars($c['categories_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Brand</label>
            <input type="text" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>

            <label>Model</label>
            <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>

            <label>Engine</label>
            <input type="text" name="engine" value="<?php echo htmlspecialchars($car['engine']); ?>">

            <label>Transmission / Drive</label>
            <input type="text" name="transmission_drive" value="<?php echo htmlspecialchars($car['transmission_drive']); ?>">

            <label>Horsepower (hp)</label>
            <input type="number" name="horsepower" value="<?php echo htmlspecialchars($car['horsepower']); ?>" required>

            <label>0–100 km/h (seconds)</label>
            <input type="number" step="0.1" name="acceleration_0_100" value="<?php echo htmlspecialchars($car['acceleration_0_100']); ?>" required>

            <label>Feature tag</label>
            <input type="text" name="feature_tag" value="<?php echo htmlspecialchars($car['feature_tag']); ?>">

            <label>Slogan</label>
            <input type="text" name="slogan" value="<?php echo htmlspecialchars($car['slogan']); ?>">

            <label>Price per day (RM)</label>
            <input type="number" step="0.01" name="price_per_day" value="<?php echo htmlspecialchars($car['price_per_day']); ?>" required>

            <label>Status</label>
            <select name="status">
                <?php foreach (['available', 'rented', 'maintenance'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php if ($car['status'] === $s) { echo 'selected'; } ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Image URL</label>
            <input type="text" name="image" value="<?php echo htmlspecialchars($car['image']); ?>">

            <div class="buttons">
                <button type="submit">Save</button>
                <a href="vehicles.php" class="btn grey">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>