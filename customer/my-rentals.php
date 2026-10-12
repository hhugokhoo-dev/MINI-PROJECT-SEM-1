<?php
// Start session to access session variables
session_start();

// Check if user is logged in and has the customer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../system/login.php");
    exit();
}

// Include database connection configuration
include '../config/db.php';

// Store logged-in user ID
$user_id = $_SESSION['user_id'];

// Check if cancel form was submitted via POST
if (isset($_POST['rental_id'])) {
    $rental_id = $_POST['rental_id'];

    // Update rental status to cancelled for pending orders
    $sql = "UPDATE rentals SET status = 'cancelled' WHERE rental_id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rental_id, $user_id]);

    // Refresh page to show updated status
    header("Location: my-rentals.php");
    exit();
}

// Query all rental records for the current customer joined with vehicle details
$sql = "SELECT rentals.*, vehicles.brand, vehicles.model 
        FROM rentals 
        JOIN vehicles ON rentals.vehicle_id = vehicles.vehicle_id 
        WHERE rentals.user_id = ? 
        ORDER BY rentals.rental_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$my_rentals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - DRIVORA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/panel.css">
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
                <th>Start</th>
                <th>End</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php 
            // Loop through each rental record stored in $my_rentals array
            foreach ($my_rentals as $r) { 
            ?>
            <tr>
                <!-- Display the rental ID number -->
                <td>#<?php echo $r['rental_id']; ?></td>

                <!-- Combine car brand and model with a space -->
                <td><?php echo $r['brand'] . ' ' . $r['model']; ?></td>

                <!-- Display start and end dates -->
                <td><?php echo $r['start_date']; ?></td>
                <td><?php echo $r['end_date']; ?></td>

                <!-- Display price with RM currency tag -->
                <td>RM<?php echo $r['total_price']; ?></td>

                <!-- Output status name as a CSS class name for styling -->
                <td><span class="status <?php echo $r['status']; ?>"><?php echo $r['status']; ?></span></td>

                <td>
                    <?php 
                    // Show cancel button only if the rental status is 'pending'
                    if ($r['status'] == 'pending') { 
                    ?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to cancel this rental?');">
                            <!-- Hidden field to send the rental ID to PHP post request -->
                            <input type="hidden" name="rental_id" value="<?php echo $r['rental_id']; ?>">
                            <button class="no">Cancel</button>
                        </form>
                    <?php 
                    // Display dash if status is confirmed or cancelled
                    } else { 
                    ?>
                        -
                    <?php } ?>
                </td>
            </tr>
            <?php 
            } // End of foreach loop
            ?>
        </table>

        <?php 
        // Check if user has no rental history (array count is 0)
        if (count($my_rentals) == 0) { 
        ?>
            <p class="empty">You have no rental records yet. <a href="browse.php">Go book a car!</a></p>
        <?php 
        } // End of count check
        ?>
    </div>

</body>
</html>