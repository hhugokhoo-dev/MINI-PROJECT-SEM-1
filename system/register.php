<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error   = '';
$name    = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name            = trim($_POST['name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Check if the email is already registered
        $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'This email is already registered. Try logging in instead.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // New sign-ups are always customers
            $insert = $pdo->prepare(
                'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
            );
            $insert->execute([$name, $email, $hashed, 'customer']);

            header('Location: login.php');
            exit;
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

      <!-- left photo -->
        <section class="auth-visual" aria-hidden="true">
            <div class="auth-visual-copy">
                <h2>Your next drive starts here.</h2>
                <p>Create an account and book a car in a few minutes.</p>
            </div>
        </section>

       <!-- rigth form -->
        <section class="auth-panel">

            <div class="auth-panel-top">
                <a href="index.html" class="auth-logo">DRIV<span>O</span>RA</a>
                <a href="index.html" class="auth-back">Back to home</a>
            </div>

            <div class="auth-panel-main">
                <div class="auth-form-wrap">

                    <h1>Create an account</h1>
                    <p class="auth-subtitle">It only takes a minute. Fill in your details to get started.</p>

                    <?php if ($error): ?>
                        <div class="auth-error" role="alert"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">

                        <div class="auth-field">
                            <label for="name">Full name</label>
                            <div class="auth-input-wrap">
                                <input type="text" id="name" name="name"
                                       value="<?= htmlspecialchars($name) ?>"
                                       placeholder="Your name"
                                       autocomplete="name" required>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="email">Email</label>
                            <div class="auth-input-wrap">
                                <input type="email" id="email" name="email"
                                       value="<?= htmlspecialchars($email) ?>"
                                       placeholder="you@example.com"
                                       autocomplete="email" required>
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
        // Show / hide password (works for both password fields)
        document.querySelectorAll('.auth-toggle').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                btn.textContent = showing ? 'Show' : 'Hide';
                btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            });
        });
    </script>

</body>
</html>
