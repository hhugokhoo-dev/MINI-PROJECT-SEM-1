<?php
session_start();
require_once __DIR__ . '/config/db.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // STEP 1: Query the database to find the user by email
    $stmt = $pdo->prepare('SELECT user_id, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // STEP 2: Verify the password using password_verify()
    if ($user && password_verify($password, $user['password'])) {

        // STEP 3: Store id, email, and role in $_SESSION['user']
        session_regenerate_id(true); // prevent session fixation
        $_SESSION['user'] = [
            'id'    => $user['user_id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        // STEP 4: Redirect based on role
        if ($user['role'] === 'customer') {
            header('Location: /customer/browse.php');
        } elseif ($user['role'] === 'staff') {
            header('Location: /staff/manage_vehicles.php');
        } elseif ($user['role'] === 'admin') {
            header('Location: /admin/users.php');
        }
        exit;

    } else {
        // STEP 5: If login failed, set an error message
        $error = 'Email or password is incorrect. Check your details and try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Log in - DRIVORA</title>

    <!-- Google Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Auth pages CSS (login, register) -->
    <link rel="stylesheet" href="assets/auth.css">
</head>

<body class="auth-body">

    <main class="auth-layout">


      <!-- left photo -->
            <section class="auth-visual" aria-hidden="true">
            <div class="auth-visual-copy">
                <h2>The best part of the trip is the drive.</h2>
                <p>Log in to see which cars are free this weekend.</p>
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

                    <h1>Log in</h1>
                    <p class="auth-subtitle">Welcome back. Enter your details to continue.</p>

                    <?php if ($error): ?>
                        <div class="auth-error" role="alert"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">

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
                                       autocomplete="current-password" required>
                                <button type="button" class="auth-toggle" id="togglePassword"
                                        aria-label="Show password">Show</button>
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
        // Show / hide password
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePassword');

        toggleBtn.addEventListener('click', () => {
            const showing = passwordInput.type === 'text';
            passwordInput.type = showing ? 'password' : 'text';
            toggleBtn.textContent = showing ? 'Show' : 'Hide';
            toggleBtn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        });
    </script>

</body>
</html>