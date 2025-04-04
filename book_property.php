<?php
session_start();
require("includes/database_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    if (!$user_id) {
        echo "Please log in to book a property.";
        exit;
    }

    $property_id = $_POST['property_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guests = $_POST['guests'];
    $guest_name = $_POST['guest_name'];
    $guest_id = $_POST['guest_id'];

    // Insert the booking into the database
    $sql = "INSERT INTO bookings (user_id, property_id, check_in, check_out, guests, guest_name, guest_id) 
            VALUES ('$user_id', '$property_id', '$check_in', '$check_out', '$guests', '$guest_name', '$guest_id')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Redirect to payment page
        header("Location: payment_gateway.php?booking_id=" . mysqli_insert_id($conn));
        exit;
    } else {
        echo "Booking failed. Please try again.";
    }
}
?>
