<?php
session_start();
require("includes/database_connect.php");

// Redirect to admin dashboard if already logged in and logout if requested
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    echo "<h1>Welcome to the Admin Dashboard</h1>";
    echo "<p><a href='admin.php?action=logout'>Logout</a></p>";
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

    // Query to check if admin exists
    $sql = "SELECT id, password FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Store admin ID in session
            $_SESSION['admin_id'] = $admin['id'];

            // Redirect to admin dashboard
            header("Location: admin.php");
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
    <title>Admin Login | PG Tak Safar</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/login.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="page-container">
        <h1>Admin Login</h1>

        <?php if ($login_error): ?>
            <p class="error"><?= htmlspecialchars($login_error) ?></p>
        <?php endif; ?>

        <form action="admin.php" method="POST">
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

        <p><a href="home.php">Back to Home</a></p>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>
