<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// only staff can open this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header('Location: /system/login.php');
    exit;
}

function e($text) {
    return htmlspecialchars((string)$text);
}

// vehicle-form.php = add a car, vehicle-form.php?id=5 = edit car 5
$id = $_GET['id'] ?? 0;

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// empty car for the "add" form
$car = [
    'categories_id' => '', 'brand' => '', 'model' => '', 'engine' => '',
    'transmission_drive' => '', 'horsepower' => '', 'acceleration_0_100' => '',
    'feature_tag' => '', 'slogan' => '', 'price_per_day' => '',
    'status' => 'available', 'image' => '',
];

// editing: load the existing car
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();

    if (!$car) {
        die('Car not found. <a href="vehicles.php">Back</a>');
    }
}

// form submitted: save the car
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        $_POST['categories_id'],
        $_POST['brand'],
        $_POST['model'],
        $_POST['engine'],
        $_POST['transmission_drive'],
        $_POST['horsepower'],
        $_POST['acceleration_0_100'],
        $_POST['feature_tag'],
        $_POST['slogan'],
        $_POST['price_per_day'],
        $_POST['status'],
        $_POST['image'],
    ];

    if ($id) {
        $values[] = $id;
        $stmt = $pdo->prepare(
            "UPDATE vehicles SET categories_id = ?, brand = ?, model = ?, engine = ?,
             transmission_drive = ?, horsepower = ?, acceleration_0_100 = ?, feature_tag = ?,
             slogan = ?, price_per_day = ?, status = ?, image = ?
             WHERE vehicle_id = ?"
        );
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO vehicles (categories_id, brand, model, engine, transmission_drive,
             horsepower, acceleration_0_100, feature_tag, slogan, price_per_day, status, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
    }
    $stmt->execute($values);

    header('Location: vehicles.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit Car' : 'Add Car'; ?> - DRIVORA</title>
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
        <h1><?php echo $id ? 'Edit Car' : 'Add Car'; ?></h1>

        <form method="post" class="form-card">

            <label>Category</label>
            <select name="categories_id" required>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['categories_id']; ?>" <?php if ($c['categories_id'] == $car['categories_id']) echo 'selected'; ?>>
                        <?php echo e($c['categories_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Brand</label>
            <input type="text" name="brand" value="<?php echo e($car['brand']); ?>" required>

            <label>Model</label>
            <input type="text" name="model" value="<?php echo e($car['model']); ?>" required>

            <label>Engine</label>
            <input type="text" name="engine" value="<?php echo e($car['engine']); ?>">

            <label>Transmission / Drive</label>
            <input type="text" name="transmission_drive" value="<?php echo e($car['transmission_drive']); ?>">

            <label>Horsepower (hp)</label>
            <input type="number" name="horsepower" value="<?php echo e($car['horsepower']); ?>">

            <label>0–100 km/h (seconds)</label>
            <input type="number" step="0.1" name="acceleration_0_100" value="<?php echo e($car['acceleration_0_100']); ?>">

            <label>Feature tag</label>
            <input type="text" name="feature_tag" value="<?php echo e($car['feature_tag']); ?>">

            <label>Slogan</label>
            <input type="text" name="slogan" value="<?php echo e($car['slogan']); ?>">

            <label>Price per day (RM)</label>
            <input type="number" step="0.01" name="price_per_day" value="<?php echo e($car['price_per_day']); ?>" required>

            <label>Status</label>
            <select name="status">
                <?php foreach (['available', 'rented', 'maintenance'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php if ($car['status'] === $s) echo 'selected'; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>

            <label>Image URL</label>
            <input type="text" name="image" value="<?php echo e($car['image']); ?>">

            <div class="buttons">
                <button type="submit">Save</button>
                <a href="vehicles.php" class="btn grey">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>