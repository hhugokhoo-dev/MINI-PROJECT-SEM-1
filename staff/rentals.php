<?php
// PHP code starts here
require_once __DIR__ . '/../includes/auth_check.php';
// load the guard file (it opens the session and defines require_role)
require_role(['staff']);
// only staff may stay on this page; everyone else is sent to the login page
require_once __DIR__ . '/../config/db.php';
// load db.php to get the $pdo database connection

// When the Confirm or Cancel button is clicked
// (a comment: only for humans to read)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// if the page was opened by pressing one of the buttons
    $rental_id = $_POST['rental_id'];
    // take the rental id from the form and store it
    $action = $_POST['action'];
    // take which button was pressed: "confirm" or "cancel"

    $stmt = $pdo->prepare("SELECT vehicle_id, status FROM rentals WHERE rental_id = ?");
    // prepare a question: which car and which status does this rental have? (? is an empty slot)
    $stmt->execute([$rental_id]);
    // fill the ? with the rental id and ask the database
    $rental = $stmt->fetch(PDO::FETCH_ASSOC);
    // get the answer as one row (or false if this rental does not exist)

    if ($rental) {
    // if the rental was found

        if ($action === 'confirm' && $rental['status'] === 'pending') {
        // if the Confirm button was pressed AND the rental is still pending
            $stmt = $pdo->prepare("UPDATE rentals SET status = 'confirmed' WHERE rental_id = ?");
            // prepare a command: change this rental's status to confirmed
            $stmt->execute([$rental_id]);
            // fill the ? with the rental id and run the command

            $stmt = $pdo->prepare("UPDATE vehicles SET status = 'rented' WHERE vehicle_id = ?");
            // prepare a command: change this car's status to rented
            $stmt->execute([$rental['vehicle_id']]);
            // fill the ? with the car id and run the command

        } elseif ($action === 'cancel') {
        // otherwise, if the Cancel button was pressed
            $stmt = $pdo->prepare("UPDATE rentals SET status = 'cancelled' WHERE rental_id = ?");
            // prepare a command: change this rental's status to cancelled
            $stmt->execute([$rental_id]);
            // fill the ? with the rental id and run the command

            if ($rental['status'] === 'confirmed') {
            // only if the rental was confirmed before (so the car was marked as rented)
                $stmt = $pdo->prepare("UPDATE vehicles SET status = 'available' WHERE vehicle_id = ?");
                // prepare a command: make this car available again
                $stmt->execute([$rental['vehicle_id']]);
                // fill the ? with the car id and run the command
            }
            // end of the inner if
        }
        // end of the confirm / cancel check
    }
    // end of the if (rental found)

    header('Location: rentals.php');
    // reload this page so the list shows the new status
    exit;
    // stop the script
}
// end of the POST if

$sql = "SELECT rentals.*, users.name, users.email, vehicles.brand, vehicles.model
        FROM rentals
        JOIN users ON rentals.user_id = users.user_id
        JOIN vehicles ON rentals.vehicle_id = vehicles.vehicle_id
        ORDER BY rentals.rental_id DESC";
// write the question for the database and store it in $sql
// SELECT ... = all rental columns, plus the customer's name and email, plus the car's brand and model
// FROM rentals = start from the rentals table
// JOIN users ON ... = connect each rental to its customer
// JOIN vehicles ON ... = connect each rental to its car
// ORDER BY rentals.rental_id DESC = newest rental first

$rentals = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
// ask the database the question above and store all the rows in $rentals
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
    <title>Rental Applications - DRIVORA</title>
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
            <li><a href="rentals.php" class="active">Rentals</a></li>
            <!-- link to this page; "active" highlights it -->
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
        <h1>Rental Applications</h1>
        <!-- the big page title -->

        <table>
        <!-- the table starts -->
            <tr>
            <!-- the header row starts -->
                <th>No.</th>
                <!-- column title: rental number -->
                <th>Customer</th>
                <!-- column title: who rented -->
                <th>Car</th>
                <!-- column title: which car -->
                <th>Start</th>
                <!-- column title: start date -->
                <th>End</th>
                <!-- column title: end date -->
                <th>Total</th>
                <!-- column title: total price -->
                <th>Status</th>
                <!-- column title: pending / confirmed / cancelled -->
                <th>Action</th>
                <!-- column title: the Confirm / Cancel button -->
            </tr>
            <!-- end of the header row -->

            <?php foreach ($rentals as $r): ?>
            <!-- PHP: repeat everything below once for every rental; the current one is called $r -->
            <tr>
            <!-- one table row for one rental -->
                <td><?php echo $r['rental_id']; ?></td>
                <!-- show the rental id -->
                <td><?php echo htmlspecialchars($r['name']); ?> (<?php echo htmlspecialchars($r['email']); ?>)</td>
                <!-- show the customer's name and email; htmlspecialchars makes what the user typed safe -->
                <td><?php echo $r['brand']; ?> <?php echo $r['model']; ?></td>
                <!-- show the car's brand and model -->
                <td><?php echo $r['start_date']; ?></td>
                <!-- show the start date -->
                <td><?php echo $r['end_date']; ?></td>
                <!-- show the end date -->
                <td>RM<?php echo $r['total_price']; ?></td>
                <!-- show RM and the total price -->
                <td><span class="status <?php echo $r['status']; ?>"><?php echo $r['status']; ?></span></td>
                <!-- show the status; the same word is also used as a CSS class to give it a colour -->
                <td>
                <!-- the action cell starts -->
                    <?php if ($r['status'] === 'pending'): ?>
                    <!-- PHP: if this rental is still pending, show the Confirm button -->
                        <form method="post">
                        <!-- a form sent with POST -->
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <!-- an invisible field carrying this rental's id -->
                            <button class="ok" name="action" value="confirm">Confirm</button>
                            <!-- the Confirm button; it sends action = confirm -->
                        </form>
                        <!-- end of the form -->
                    <?php elseif ($r['status'] === 'confirmed'): ?>
                    <!-- PHP: otherwise, if this rental is confirmed, show the Cancel button -->
                        <form method="post" onsubmit="return confirm('Cancel this rental and make the car available again?');">
                        <!-- a form sent with POST; the browser first asks for a confirmation -->
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <!-- an invisible field carrying this rental's id -->
                            <button class="no" name="action" value="cancel">Cancel</button>
                            <!-- the Cancel button; it sends action = cancel -->
                        </form>
                        <!-- end of the form -->
                    <?php endif; ?>
                    <!-- PHP: end of the if (a cancelled rental shows no button) -->
                </td>
                <!-- end of the action cell -->
            </tr>
            <!-- end of this rental's row -->
            <?php endforeach; ?>
            <!-- PHP: end of the repeat; go to the next rental -->
        </table>
        <!-- end of the table -->

        <?php if (count($rentals) == 0): ?>
        <!-- PHP: if the number of rentals is 0, show what is below -->
            <p class="empty">No rental applications yet.</p>
            <!-- a message saying there is nothing to show -->
        <?php endif; ?>
        <!-- PHP: end of the if -->
    </div>
    <!-- end of the content box -->

</body>
<!-- end of the visible part -->
</html>
<!-- end of the page -->