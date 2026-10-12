<?php
// PHP code starts here
session_start();
// open the session (the storage box for who is logged in)
require_once __DIR__ . '/../config/db.php';
// load db.php (one folder up) to get the $pdo database connection

$error = '';
// an empty box for the error message
$email = '';
// an empty box for the email (used to refill the form)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// if the page was opened by pressing the Log in button
    $email = $_POST['email'];
    // take the email the user typed
    $password = $_POST['password'];
    // take the password the user typed

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    // prepare a question: find the user with this email (? is an empty slot)
    $stmt->execute([$email]);
    // fill the ? with the email and ask the database
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // get the user as one row (or false if nobody has this email)

    if ($user && password_verify($password, $user['password'])) {
    // if a user was found AND the typed password matches the scrambled one in the database
        $_SESSION['user_id'] = $user['user_id'];
        // remember the user's id
        $_SESSION['name'] = $user['name'];
        // remember the user's name
        $_SESSION['role'] = $user['role'];
        // remember the user's role (customer / staff / admin)

        if ($user['role'] === 'staff') {
        // if the user is staff
            header('Location: /staff/rentals.php');
            // go to the staff page
        } elseif ($user['role'] === 'admin') {
        // otherwise, if the user is admin
            header('Location: /admin/categories.php');
            // go to the admin page
        } else {
        // otherwise the user is a customer
            header('Location: /customer/browse.php');
            // go to the browse page
        }

        exit;
    } else {

    $error = 'Email or password is incorrect. Check your details and try again.';
        // put this message into the error box
    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Log in - DRIVORA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/auth.css">

</head>


<body class="auth-body">

    <main class="auth-layout">


        <section class="auth-visual" aria-hidden="true">

        <div class="auth-visual-copy">

        <h2>The best part of the trip is the drive.</h2>

        <p>Log in to see which cars are free this weekend.</p>

    </div>

</section>

        <section class="auth-panel">

        <div class="auth-panel-top">

        <a href="/mainpage/index.php" class="auth-logo">DRIV<span>O</span>RA</a>

        <a href="/mainpage/index.php" class="auth-back">Back to home</a>

    </div>


            <div class="auth-panel-main">

            <div class="auth-form-wrap">


                    <h1>Log in</h1>

                    <p class="auth-subtitle">Welcome back. Enter your details to continue.</p>

                    
                    <?php if ($error != ''): ?>
                    <!-- PHP: if the error box is not empty, show what is below -->
                        <div class="auth-error" role="alert"><?php echo htmlspecialchars($error); ?></div>
                        <!-- show the error message in a red box -->
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                    <!-- the form starts; it sends the data to this same page with POST -->

                        <div class="auth-field">

                        <label for="email">Email</label>

                        <div class="auth-input-wrap">

                        <input type="email" id="email" name="email"
                                       value="<?php echo htmlspecialchars($email); ?>"
                                       placeholder="you@example.com"
                                       autocomplete="email" required>
                                <!-- an email box sent as "email"; refilled with what was typed; it cannot be empty -->
                            </div>
                        </div>

                        <div class="auth-field">
                        <!-- one form row: the password -->
                            <label for="password">Password</label>
                            <!-- the text above the box -->
                            <div class="auth-input-wrap">
                            <!-- a wrapper around the input and its button -->
                                <input type="password" id="password" name="password"
                                       class="has-toggle"
                                       autocomplete="current-password" required>
                                <!-- a password box (shows dots) sent as "password"; it cannot be empty -->
                                <button type="button" class="auth-toggle" id="togglePassword"
                                        aria-label="Show password">Show</button>
                                <!-- the Show/Hide button; type="button" means it does not send the form -->
                            </div>
                        </div>

                        <button type="submit" class="auth-submit">Log in</button>

                    </form>

                    <p class="auth-footer">
                        New to DRIVORA? <a href="register.php">Create an account</a>
                    </p>

                </div>
            </div>
        </section>

    </main>

    <script>
        const passwordInput = document.getElementById('password');
        // find the password box by its id and store it
        const toggleBtn = document.getElementById('togglePassword');
        // find the Show/Hide button by its id and store it

        toggleBtn.addEventListener('click', () => {
        // when the button is clicked, run the code below
            const showing = passwordInput.type === 'text';
            // true if the password is currently visible
            passwordInput.type = showing ? 'password' : 'text';
            // if visible, hide it; otherwise show it
            toggleBtn.textContent = showing ? 'Show' : 'Hide';
            // change the button text to match the new state
            toggleBtn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            // update the hidden label read by screen readers
        });
    </script>

</body>
</html>
