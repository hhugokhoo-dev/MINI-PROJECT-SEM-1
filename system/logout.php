<?php
session_start();

if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true){
    header('Location: index.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charqset="UTF-8">
    <title>Home</title>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></h2>
    <a href="05_logout_exercise.php">Logout</a>

</body>
</html>