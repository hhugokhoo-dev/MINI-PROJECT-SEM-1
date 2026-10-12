<?php
// PHP code starts here
require_once __DIR__ . '/../includes/auth_check.php';
// load the guard file (it opens the session and defines require_role)
require_role(['staff']);
// only staff may stay on this page; everyone else is sent to the login page
require_once __DIR__ . '/../config/db.php';
// load db.php to get the $pdo database connection

// vehicle-form.php = add a car, vehicle-form.php?id=5 = edit car 5
// (a comment: only for humans to read)
$id = 0;
// start with 0, which means "adding a new car"
if (isset($_GET['id'])) {
// if the web address has ?id=something
    $id = $_GET['id'];
    // use that id, which means "editing this car"
}
// end of the if

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
// get every category from the database (used for the dropdown)

// an empty car, used when adding a new car
// (a comment: only for humans to read)
$car = [
// make a list of empty values, so the form has something to show
    'categories_id' => '',
    // no category chosen yet
    'brand' => '',
    // empty brand
    'model' => '',
    // empty model
    'engine' => '',
    // empty engine
    'transmission_drive' => '',
    // empty transmission
    'horsepower' => '',
    // empty horsepower
    'acceleration_0_100' => '',
    // empty 0-100 time
    'feature_tag' => '',
    // empty feature tag
    'slogan' => '',
    // empty slogan
    'price_per_day' => '',
    // empty price
    'status' => 'available',
    // a new car starts as "available"
    'image' => '',
    // empty image link
];
// end of the list

// when editing, load the real car from the database
// (a comment: only for humans to read)
if ($id != 0) {
// if $id is not 0, we are editing
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
    // prepare a question: get this car (? is an empty slot)
    $stmt->execute([$id]);
    // fill the ? with the id and ask the database
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // get the car as one row (or false if nothing was found)

    if ($row) {
    // if the car was found
        $car = $row;
        // replace the empty list with the real car's data
    } else {
    // otherwise no car has this id
        die('Car not found. <a href="vehicles.php">Back</a>');
        // stop and show a message with a Back link
    }
    // end of the if / else
}
// end of the if

// when the Save button is clicked
// (a comment: only for humans to read)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// if the page was opened by pressing the Save button
    $categories_id = $_POST['categories_id'];
    // take the chosen category from the form
    $brand = $_POST['brand'];
    // take the brand
    $model = $_POST['model'];
    // take the model
    $engine = $_POST['engine'];
    // take the engine
    $transmission_drive = $_POST['transmission_drive'];
    // take the transmission
    $horsepower = $_POST['horsepower'];
    // take the horsepower
    $acceleration = $_POST['acceleration_0_100'];
    // take the 0-100 time
    $feature_tag = $_POST['feature_tag'];
    // take the feature tag
    $slogan = $_POST['slogan'];
    // take the slogan
    $price = $_POST['price_per_day'];
    // take the price per day
    $status = $_POST['status'];
    // take the status
    $image = $_POST['image'];
    // take the image link

    if ($id != 0) {
    // if we are editing a car
        $stmt = $pdo->prepare("UPDATE vehicles SET categories_id = ?, brand = ?, model = ?, engine = ?, transmission_drive = ?, horsepower = ?, acceleration_0_100 = ?, feature_tag = ?, slogan = ?, price_per_day = ?, status = ?, image = ? WHERE vehicle_id = ?");
        // prepare a command: change all 12 columns of this car (each ? is an empty slot; the last ? is the car id)
        $stmt->execute([$categories_id, $brand, $model, $engine, $transmission_drive, $horsepower, $acceleration, $feature_tag, $slogan, $price, $status, $image, $id]);
        // fill the 13 slots in order and run the command
    } else {
    // otherwise we are adding a new car
        $stmt = $pdo->prepare("INSERT INTO vehicles (categories_id, brand, model, engine, transmission_drive, horsepower, acceleration_0_100, feature_tag, slogan, price_per_day, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // prepare a command: add a new car with 12 columns (each ? is an empty slot)
        $stmt->execute([$categories_id, $brand, $model, $engine, $transmission_drive, $horsepower, $acceleration, $feature_tag, $slogan, $price, $status, $image]);
        // fill the 12 slots in order and run the command
    }
    // end of the if / else

    header('Location: vehicles.php');
    // go back to the vehicle list
    exit;
    // stop the script
}
// end of the POST if
?>
<!-- PHP ends here; plain HTML starts below -->
<!DOCTYPE html>
<!-- tells the browser this is an HTML5 page -->
<html lang="en">
<!-- the page starts; its language is English -->
<head>
<!-- the invisible information starts -->
    <meta charset="UTF-8">
    <!-- use UTF-8 so all characters display correctly -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- make the page fit phone screens -->
    <title>Car Form - DRIVORA</title>
    <!-- the text shown on the browser tab -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- load the Playfair Display and Quicksand fonts from Google -->
    <link rel="stylesheet" href="../assets/panel.css">
    <!-- load our own style file -->
</head>
<!-- end of the invisible information -->
<body>
<!-- the visible part starts -->

    <nav>
    <!-- the top navigation bar starts -->
        <a href="/mainpage/index.php" class="logo">DRIV<span>O</span>RA <small>STAFF</small></a>
        <!-- the logo; clicking it goes to the home page -->
        <ul>
        <!-- a list of menu links starts -->
            <li><a href="rentals.php">Rentals</a></li>
            <!-- link to the rentals page -->
            <li><a href="vehicles.php">Vehicles</a></li>
            <!-- link to the vehicles page -->
            <li><a href="/system/logout.php">Logout</a></li>
            <!-- link to log out -->
        </ul>
        <!-- end of the menu list -->
    </nav>
    <!-- end of the navigation bar -->

    <div class="wrap">
    <!-- a box that holds the main content -->

        <?php if ($id != 0): ?>
        <!-- PHP: if we are editing, show the line below -->
            <h1>Edit Car</h1>
            <!-- the title when editing -->
        <?php else: ?>
        <!-- PHP: otherwise (adding), show the line below -->
            <h1>Add Car</h1>
            <!-- the title when adding -->
        <?php endif; ?>
        <!-- PHP: end of the if -->

        <form method="post" class="form-card">
        <!-- the form starts; it is sent with POST and styled like a card -->

            <label>Category</label>
            <!-- the text above the category dropdown -->
            <select name="categories_id" required>
            <!-- a dropdown menu sent as categories_id; one must be chosen -->
                <?php foreach ($categories as $c): ?>
                <!-- PHP: repeat for every category; the current one is called $c -->
                    <option value="<?php echo $c['categories_id']; ?>" <?php if ($c['categories_id'] == $car['categories_id']) { echo 'selected'; } ?>>
                    <!-- one choice; its value is the category id; add "selected" if it is this car's current category -->
                        <?php echo $c['categories_name']; ?>
                        <!-- show the category name -->
                    </option>
                    <!-- end of this choice -->
                <?php endforeach; ?>
                <!-- PHP: end of the repeat -->
            </select>
            <!-- end of the dropdown -->

            <label>Brand</label>
            <!-- the text above the brand box -->
            <input type="text" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
            <!-- a text box sent as brand; filled with the current value; it cannot be empty -->

            <label>Model</label>
            <!-- the text above the model box -->
            <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
            <!-- a text box sent as model; filled with the current value; it cannot be empty -->

            <label>Engine</label>
            <!-- the text above the engine box -->
            <input type="text" name="engine" value="<?php echo htmlspecialchars($car['engine']); ?>">
            <!-- a text box sent as engine; filled with the current value -->

            <label>Transmission / Drive</label>
            <!-- the text above the transmission box -->
            <input type="text" name="transmission_drive" value="<?php echo htmlspecialchars($car['transmission_drive']); ?>">
            <!-- a text box sent as transmission_drive; filled with the current value -->

            <label>Horsepower (hp)</label>
            <!-- the text above the horsepower box -->
            <input type="number" name="horsepower" value="<?php echo $car['horsepower']; ?>" required>
            <!-- a number-only box sent as horsepower; it cannot be empty -->

            <label>0–100 km/h (seconds)</label>
            <!-- the text above the acceleration box -->
            <input type="number" step="0.1" name="acceleration_0_100" value="<?php echo $car['acceleration_0_100']; ?>" required>
            <!-- a number box allowing 1 decimal (step 0.1), sent as acceleration_0_100; it cannot be empty -->

            <label>Feature tag</label>
            <!-- the text above the feature tag box -->
            <input type="text" name="feature_tag" value="<?php echo htmlspecialchars($car['feature_tag']); ?>">
            <!-- a text box sent as feature_tag; filled with the current value -->

            <label>Slogan</label>
            <!-- the text above the slogan box -->
            <input type="text" name="slogan" value="<?php echo htmlspecialchars($car['slogan']); ?>">
            <!-- a text box sent as slogan; filled with the current value -->

            <label>Price per day (RM)</label>
            <!-- the text above the price box -->
            <input type="number" step="0.01" name="price_per_day" value="<?php echo $car['price_per_day']; ?>" required>
            <!-- a number box allowing 2 decimals, sent as price_per_day; it cannot be empty -->

            <label>Status</label>
            <!-- the text above the status dropdown -->
            <select name="status">
            <!-- a dropdown menu sent as status -->
                <?php foreach (['available', 'rented', 'maintenance'] as $s): ?>
                <!-- PHP: repeat for each of the 3 statuses; the current one is called $s -->
                    <option value="<?php echo $s; ?>" <?php if ($car['status'] === $s) { echo 'selected'; } ?>><?php echo $s; ?></option>
                    <!-- one choice; value and text are the status word; "selected" if it is this car's current status -->
                <?php endforeach; ?>
                <!-- PHP: end of the repeat -->
            </select>
            <!-- end of the dropdown -->

            <label>Image URL</label>
            <!-- the text above the image link box -->
            <input type="text" name="image" value="<?php echo htmlspecialchars($car['image']); ?>">
            <!-- a text box sent as image; filled with the current image link -->

            <div class="buttons">
            <!-- a row that holds the two buttons -->
                <button type="submit">Save</button>
                <!-- the Save button, which sends the form -->
                <a href="vehicles.php" class="btn grey">Cancel</a>
                <!-- a grey link that goes back to the list without saving -->
            </div>
            <!-- end of the button row -->
        </form>
        <!-- end of the form -->
    </div>
    <!-- end of the content box -->

</body>
<!-- end of the visible part -->
</html>
<!-- end of the page -->