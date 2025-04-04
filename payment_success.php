<?php
session_start();
require("includes/database_connect.php");

// Check if required booking details are received
if (!isset($_POST['property_id']) || !isset($_POST['check_in']) || !isset($_POST['check_out']) || !isset($_POST['guests']) || !isset($_POST['guest_name']) || !isset($_POST['guest_id'])) {
    echo "Booking details are missing.";
    exit;
}

$property_id = $_POST['property_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$guests = $_POST['guests'];
$guest_name = $_POST['guest_name'];
$guest_id = $_POST['guest_id'];

// Retrieve property and booking information
$sql = "SELECT p.name AS property_name, p.address, p.city_id, c.name AS city_name 
        FROM properties p
        INNER JOIN cities c ON p.city_id = c.id
        WHERE p.id = $property_id";
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
    <title>Payment Success | PG tak safar</title>

    <?php 
        include "includes/head_links.php";
    ?>
    
    <link href="css/payment.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="page-container">
        <!--<h1>Payment Successful!</h1>-->
        <img src="img\paymentsucess.png" alt="Successfully Done">
        <h3>Thank you for your booking. Here are your booking details:</h3>
        <ul>
            <li>Property Name: <?= htmlspecialchars($property['property_name']) ?></li>
            <li>Property Address: <?= htmlspecialchars($property['address']) ?></li>
            <li>City: <?= htmlspecialchars($property['city_name']) ?></li>
            <li>Check-in Date: <?= htmlspecialchars($check_in) ?></li>
            <li>Check-out Date: <?= htmlspecialchars($check_out) ?></li>
            <li>Number of Guests: <?= htmlspecialchars($guests) ?></li>
            <li>Name of Guests: <?= htmlspecialchars($guest_name) ?></li>
            <li>ID of Guests: <?= htmlspecialchars($guest_id) ?></li>
        </ul>

        <!-- <h2>Contact Us</h2>
        <p>If you have any questions, please fill out the form below:</p>
        
        <form action="contact_submit.php" method="POST" class="contact-form">
            <div class="form-group">
                <label for="contact_name">Your Name:</label>
                <input type="text" id="contact_name" name="contact_name" required class="form-control" placeholder="Enter your name">
            </div>

            <div class="form-group">
                <label for="contact_email">Your Email:</label>
                <input type="email" id="contact_email" name="contact_email" required class="form-control" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="contact_message">Message:</label>
                <textarea id="contact_message" name="contact_message" required class="form-control" rows="4" placeholder="Enter your message"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button> -->
        </form>

        <p>If you prefer, you can also reach us at <strong>support@pgtaksafar.com</strong>.</p>
    </div>

    <?php include "includes/signup_modal.php"; ?>
    <?php include "includes/login_modal.php"; ?>
    <?php include "includes/footer.php"; ?>

</body>
</html>
