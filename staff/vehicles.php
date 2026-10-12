<?php
// PHP code starts here
require_once __DIR__ . '/../includes/auth_check.php';
// load the guard file (it opens the session and defines require_role)
require_role(['staff']);
// only staff may stay on this page; everyone else is sent to the login page
require_once __DIR__ . '/../config/db.php';
// load db.php to get the $pdo database connection

$message = '';
// an empty box for a message (used when a car cannot be deleted)

// When the Delete button is clicked
// (a comment: only for humans to read)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// if the page was opened by pressing the Delete button
    $vehicle_id = $_POST['vehicle_id'];
    // take the car id from the form and store it

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE vehicle_id = ?");
    // prepare a question: how many rentals use this car? (? is an empty slot)
    $stmt->execute([$vehicle_id]);
    // fill the ? with the car id and ask the database
    $count = $stmt->fetchColumn();
    // get the answer (a number) and store it

    if ($count > 0) {
    // if this car has at least 1 rental
        $message = 'This car already has rentals, so it cannot be deleted. Set its status to maintenance instead.';
        // put this message into the box so the page can show it
    } else {
    // otherwise the car has no rentals, so it is safe to delete
        $stmt = $pdo->prepare("DELETE FROM vehicles WHERE vehicle_id = ?");
        // prepare a command: delete this car
        $stmt->execute([$vehicle_id]);
        // fill the ? with the car id and run the command
        header('Location: vehicles.php');
        // reload this page so the deleted car disappears from the list
        exit;
        // stop the script
    }
    // end of the if / else
}
// end of the POST if

$sql = "SELECT vehicles.*, categories.categories_name
        FROM vehicles
        JOIN categories ON vehicles.categories_id = categories.categories_id
        ORDER BY vehicles.vehicle_id";
// write the question for the database and store it in $sql
// SELECT vehicles.*, categories.categories_name = all car columns plus the category name
// FROM vehicles = start from the vehicles table
// JOIN categories ON ... = connect each car to its category using categories_id
// ORDER BY vehicles.vehicle_id = sort by car id

$vehicles = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
// ask the database the question above and store all the rows in $vehicles
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
    <title>Manage Vehicles - DRIVORA</title>
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
            <li><a href="vehicles.php" class="active">Vehicles</a></li>
            <!-- link to this page; "active" highlights it -->
            <li><a href="/system/logout.php">Logout</a></li>
            <!-- link to log out -->
        </ul>
        <!-- end of the menu list -->
    </nav>
    <!-- end of the navigation bar -->

    <div class="wrap">
    <!-- a box that holds the main content -->
        <div class="top-bar">
        <!-- a row containing the title and the Add button -->
            <h1>Manage Vehicles</h1>
            <!-- the big page title -->
            <a href="vehicle-form.php" class="btn">+ Add Car</a>
            <!-- a link styled as a button; it opens the add-car form -->
        </div>
        <!-- end of the row -->

        <?php if ($message != ''): ?>
        <!-- PHP: if the message box is not empty, show what is below -->
            <div class="message"><?php echo $message; ?></div>
            <!-- show the message in a styled box -->
        <?php endif; ?>
        <!-- PHP: end of the if -->

        <table>
        <!-- the table starts -->
            <tr>
            <!-- the header row starts -->
                <th>ID</th>
                <!-- column title: car id -->
                <th>Car</th>
                <!-- column title: brand and model -->
                <th>Category</th>
                <!-- column title: category name -->
                <th>Price / day</th>
                <!-- column title: price per day -->
                <th>Status</th>
                <!-- column title: available / rented / maintenance -->
                <th>Action</th>
                <!-- column title: Edit and Delete buttons -->
            </tr>
            <!-- end of the header row -->

            <?php foreach ($vehicles as $v): ?>
            <!-- PHP: repeat everything below once for every car; the current car is called $v -->
            <tr>
            <!-- one table row for one car -->
                <td><?php echo $v['vehicle_id']; ?></td>
                <!-- show the car id -->
                <td><?php echo $v['brand']; ?> <?php echo $v['model']; ?></td>
                <!-- show the brand, a space, then the model -->
                <td><?php echo $v['categories_name']; ?></td>
                <!-- show the category name -->
                <td>RM<?php echo $v['price_per_day']; ?></td>
                <!-- show RM and the price per day -->
                <td><span class="status <?php echo $v['status']; ?>"><?php echo $v['status']; ?></span></td>
                <!-- show the status; the same word is also used as a CSS class to give it a colour -->
                <td>
                <!-- the action cell starts -->
                    <a href="vehicle-form.php?id=<?php echo $v['vehicle_id']; ?>" class="btn grey">Edit</a>
                    <!-- a link to the form with ?id=car id, so the form knows which car to edit -->
                    <form method="post" onsubmit="return confirm('Delete this car?');">
                    <!-- a form sent with POST; the browser first asks "Delete this car?" and stops if you press Cancel -->
                        <input type="hidden" name="vehicle_id" value="<?php echo $v['vehicle_id']; ?>">
                        <!-- an invisible field carrying this car's id -->
                        <button class="no">Delete</button>
                        <!-- the Delete button, which sends the form -->
                    </form>
                    <!-- end of the form -->
                </td>
                <!-- end of the action cell -->
            </tr>
            <!-- end of this car's row -->
            <?php endforeach; ?>
            <!-- PHP: end of the repeat; go to the next car -->
        </table>
        <!-- end of the table -->
    </div>
    <!-- end of the content box -->

</body>
<!-- end of the visible part -->
</html>
<!-- end of the page -->