<?php
session_start();
require("includes/database_connect.php");

// Redirect to the payment page if already logged in and trying to access login
if (isset($_SESSION['user_id']) && isset($_GET['redirect']) && $_GET['redirect'] === 'payment') {
    header("Location: payment.php");
    exit;
}

// Initialize variables
$email = $password = "";
$login_error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if user exists
    $sql = "SELECT id, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user ID in session
            $_SESSION['user_id'] = $user['id'];

            // Redirect based on `redirect` parameter
            if (isset($_GET['redirect']) && $_GET['redirect'] === 'payment') {
                header("Location: payment.php");
            } else {
                header("Location: index.php"); // Default landing page after login
            }
            exit;
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | PG tak safar</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/login.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="page-container">
        <h1>Login to Your Account</h1>
        
        <?php if ($login_error): ?>
            <p class="error"><?= htmlspecialchars($login_error) ?></p>
        <?php endif; ?>

        <form action="login.php<?= isset($_GET['redirect']) ? '?redirect=' . htmlspecialchars($_GET['redirect']) : '' ?>" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required class="form-control" value="<?= htmlspecialchars($email) ?>" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required class="form-control" placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>
