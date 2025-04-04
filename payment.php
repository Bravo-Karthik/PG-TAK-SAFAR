<?php
session_start();
require("includes/database_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php?redirect=payment");
    exit;
}

// Check if required POST data is received
if (!isset($_POST['property_id']) || !isset($_POST['check_in']) || !isset($_POST['check_out']) || !isset($_POST['guests']) || !isset($_POST['guest_name']) || !isset($_POST['guest_id'])) {
    echo "Booking details are missing. Please go back and fill in the booking form.";
    exit;
}

// Retrieve booking details from POST data
$user_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$guests = $_POST['guests'];
$guest_name = $_POST['guest_name'];
$guest_id = $_POST['guest_id'];

// Insert booking details into the bookings table
$sql_insert_booking = "INSERT INTO bookings (user_id, property_id, check_in, check_out, guests, guest_name, guest_id, booking_date, status)
                       VALUES ('$user_id', '$property_id', '$check_in', '$check_out', '$guests', '$guest_name', '$guest_id', NOW(), 'Pending')";

if (!mysqli_query($conn, $sql_insert_booking)) {
    echo "Error: Could not save booking. " . mysqli_error($conn);
    exit;
}

// Retrieve property details for display on payment page
$sql = "SELECT name FROM properties WHERE id = $property_id";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Property not found.";
    exit;
}
$property = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment for Booking | PG tak safar</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/payment.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="page-container">
        <h1>Payment for Booking</h1>
        <p>Property: <?= htmlspecialchars($property['name']) ?></p>
        <p>Check-in Date: <?= htmlspecialchars($check_in) ?></p>
        <p>Check-out Date: <?= htmlspecialchars($check_out) ?></p>
        <p>Guests: <?= htmlspecialchars($guests) ?></p>
        <p>Name of Guest: <?= htmlspecialchars($guest_name) ?></p>
        <p>ID of Guest: <?= htmlspecialchars($guest_id) ?></p>

        <!-- Payment Form -->
        <form action="payment_success.php" method="POST">
            <input type="hidden" name="property_id" value="<?= htmlspecialchars($property_id) ?>">
            <input type="hidden" name="check_in" value="<?= htmlspecialchars($check_in) ?>">
            <input type="hidden" name="check_out" value="<?= htmlspecialchars($check_out) ?>">
            <input type="hidden" name="guests" value="<?= htmlspecialchars($guests) ?>">
            <input type="hidden" name="guest_name" value="<?= htmlspecialchars($guest_name) ?>">
            <input type="hidden" name="guest_id" value="<?= htmlspecialchars($guest_id) ?>">

            <div class="form-group">
                <label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number" required class="form-control" pattern="\d{16}" maxlength="16" placeholder="Enter 16-digit card number">
            </div>

            <div class="form-group">
                <label for="card_holder">Card Holder Name:</label>
                <input type="text" id="card_holder" name="card_holder" required class="form-control" placeholder="Enter name as on card">
            </div>

            <div class="form-group">
                <label for="expiry_month">Expiration Date:</label>
                <input type="text" id="expiry_month" name="expiry_month" required class="form-control" placeholder="MM" pattern="\d{2}" maxlength="2" style="width: 50px; display: inline;">
                <input type="text" id="expiry_year" name="expiry_year" required class="form-control" placeholder="YYYY" pattern="\d{4}" maxlength="4" style="width: 80px; display: inline;">
            </div>

            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required class="form-control" pattern="\d{3}" maxlength="3" placeholder="Enter 3-digit CVV" style="width: 60px;">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit Payment</button>
        </form>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>
