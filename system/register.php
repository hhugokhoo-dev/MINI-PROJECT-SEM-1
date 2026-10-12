<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';
// an empty box for the error message
$name = '';
// an empty box for the name (used to refill the form)
$email = '';
// an empty box for the email (used to refill the form)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// if the page was opened by pressing the Sign up button
    $name = trim($_POST['name']);
    // take the name; trim() removes spaces at the start and end
    $email = trim($_POST['email']);
    // take the email and remove extra spaces
    $password = $_POST['password'];
    // take the password
    $confirm = $_POST['confirm_password'];
    // take the second password (the "type it again" box)

    if ($name === '' || $email === '') {
    // if the name is empty OR the email is empty
        $error = 'Name and email are required.';
        // put this message into the error box
    } elseif (strlen($password) < 8) {
    // otherwise, if the password is shorter than 8 characters (strlen = string length)
        $error = 'Password must be at least 8 characters.';
        // put this message into the error box
    } elseif ($password !== $confirm) {
    // otherwise, if the two passwords are NOT the same
        $error = 'Passwords do not match.';
        // put this message into the error box
    } else {
    // otherwise, everything looks fine so far
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        // prepare a question: is there already a user with this email? (? is an empty slot)
        $stmt->execute([$email]);
        // fill the ? with the email and ask the database

        if ($stmt->fetch()) {
        // if the database found a row, the email is already used
            $error = 'This email is already registered. Try logging in instead.';
            // put this message into the error box
        } else {
        // otherwise the email is new
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            // turn the password into scrambled text; we never store the real password
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            // prepare a command to add a new user; 3 empty slots, and the role is fixed to 'customer'
            $stmt->execute([$name, $email, $hashed]);
            // fill the 3 slots in order and run the command

            header('Location: login.php');
            // send the browser to the login page
            exit;
            // stop the script
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign up - DRIVORA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/auth.css">
</head>

<body class="auth-body">
    <main class="auth-layout">

        <section class="auth-visual" aria-hidden="true">

            <div class="auth-visual-copy">

                <h2>Your next drive starts here.</h2>

                <p>Create an account and book a car in a few minutes.</p>
            </div>
        </section>

        <section class="auth-panel">
        <!-- the right side with the form -->
            <div class="auth-panel-top">
            <!-- the top row of the right side -->
                <a href="/mainpage/index.php" class="auth-logo">DRIV<span>O</span>RA</a>
                <!-- the logo; clicking it goes to the home page -->
                <a href="/mainpage/index.php" class="auth-back">Back to home</a>
                <!-- a link back to the home page -->
            </div>

            <div class="auth-panel-main">

                <div class="auth-form-wrap">

                    <h1>Create an account</h1>

                    <p class="auth-subtitle">It only takes a minute. Fill in your details to get started.</p>

                    <?php if ($error != ''): ?>
                    <!-- PHP: if the error box is not empty, show what is below -->
                        <div class="auth-error" role="alert"><?php echo htmlspecialchars($error); ?></div>
                        <!-- show the error message in a red box -->
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                    <!-- the form starts; it sends the data to this same page with POST -->

                        <div class="auth-field">
                            <label for="name">Full name</label>
                            <div class="auth-input-wrap">
                                <input type="text" id="name" name="name"
                                       value="<?php echo htmlspecialchars($name); ?>"
                                       placeholder="Your name"
                                       autocomplete="name" required>
                                <!-- a text box sent as "name"; refilled with what was typed; it cannot be empty -->
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="email">Email</label>
                            <div class="auth-input-wrap">
                                <input type="email" id="email" name="email"
                                       value="<?php echo htmlspecialchars($email); ?>"
                                       placeholder="you@example.com"
                                       autocomplete="email" required>
                                <!-- an email box sent as "email"; the browser checks it looks like an email; it cannot be empty -->
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="password">Password</label>
                            <div class="auth-input-wrap">
                                <input type="password" id="password" name="password"
                                       class="has-toggle"
                                       placeholder="At least 8 characters"
                                       autocomplete="new-password" minlength="8" required>
                                <button type="button" class="auth-toggle" data-target="password"
                                        aria-label="Show password">Show</button>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="confirm_password">Confirm password</label>
                            <div class="auth-input-wrap">
                                <input type="password" id="confirm_password" name="confirm_password"
                                       class="has-toggle"
                                       autocomplete="new-password" required>
                                <!-- a second password box sent as "confirm_password"; it cannot be empty -->
                                <button type="button" class="auth-toggle" data-target="confirm_password"
                                        aria-label="Show password">Show</button>
                            </div>
                        </div>

                        <button type="submit" class="auth-submit">Sign up</button>

                    </form>

                    <p class="auth-footer">
                        Already have an account? <a href="login.php">Log in</a>
                    </p>

                </div>
            </div>
        </section>

    </main>

    <script>
        document.querySelectorAll('.auth-toggle').forEach((btn) => {
        // find every Show/Hide button and loop through them one by one
            btn.addEventListener('click', () => {
            // when this button is clicked, run the code below
                const input = document.getElementById(btn.dataset.target);
                // find the password box whose id is written in the button's data-target
                const showing = input.type === 'text';
                // true if that password is currently visible
                input.type = showing ? 'password' : 'text';
                // if visible, hide it; otherwise show it
                btn.textContent = showing ? 'Show' : 'Hide';
                // change the button text to match the new state
                btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
                // update the hidden label read by screen readers
            });
        });
    </script>

</body>
</html>
