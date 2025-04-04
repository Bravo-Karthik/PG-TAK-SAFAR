<?php
session_start();
require("includes/database_connect.php");

// Ensure only admin can access this page
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header("Location: login.php");
//     exit;
// }

// Fetch all bookings
$sql = "SELECT id, user_id, property_id, check_in, check_out, guests, guest_name, guest_id, booking_date, status 
        FROM bookings
        ORDER BY booking_date DESC";
$result = mysqli_query($conn, $sql);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Update booking status if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $sql_update = "UPDATE bookings SET status = '$new_status' WHERE id = $booking_id";
    mysqli_query($conn, $sql_update);
    header("Location: admin.php"); // Refresh the page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Bookings</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <h2>Admin Dashboard - Manage Bookings</h2>

    <div class="booking-table">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User ID</th>
                    <th>Property ID</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Guests</th>
                    <th>Guest Name</th>
                    <th>Guest ID</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['id']) ?></td>
                        <td><?= htmlspecialchars($booking['user_id']) ?></td>
                        <td><?= htmlspecialchars($booking['property_id']) ?></td>
                        <td><?= htmlspecialchars($booking['check_in']) ?></td>
                        <td><?= htmlspecialchars($booking['check_out']) ?></td>
                        <td><?= htmlspecialchars($booking['guests']) ?></td>
                        <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                        <td><?= htmlspecialchars($booking['guest_id']) ?></td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars($booking['status']) ?></td>
                        <td>
                            <form action="admin.php" method="POST">
                                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="Pending" <?= $booking['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="booked" <?= $booking['status'] === 'booked' ? 'selected' : '' ?>>booked</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
