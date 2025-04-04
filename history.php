<?php
session_start();
require("includes/database_connect.php");

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to fetch booking history for the logged-in user
$sql = "SELECT b.id, b.check_in, b.check_out, b.guests, b.guest_name, b.guest_id, b.status, 
               p.name AS property_name, p.address AS property_address
        FROM bookings b
        INNER JOIN properties p ON b.property_id = p.id
        WHERE b.user_id = $user_id
        ORDER BY b.check_in DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error fetching booking history.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History | PG tak safar</title>
    <?php include "includes/head_links.php"; ?>
    <link rel="stylesheet" href="css/history.css">
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="page-container">
        <h1>Your Booking History</h1>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Address</th>
                        <th>Check-in Date</th>
                        <th>Check-out Date</th>
                        <th>Guests</th>
                        <th>Guest Name</th>
                        <th>Guest ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['property_name']) ?></td>
                            <td><?= htmlspecialchars($row['property_address']) ?></td>
                            <td><?= htmlspecialchars($row['check_in']) ?></td>
                            <td><?= htmlspecialchars($row['check_out']) ?></td>
                            <td><?= htmlspecialchars($row['guests']) ?></td>
                            <td><?= htmlspecialchars($row['guest_name']) ?></td>
                            <td><?= htmlspecialchars($row['guest_id']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>
