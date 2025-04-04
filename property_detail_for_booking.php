<?php
    session_start();
    require("includes/database_connect.php");

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : NULL;

    if (!$property_id) {
        echo "Property not found.";
        exit;
    }

    $sql_1 = "SELECT *, p.id AS property_id, p.name AS property_name, c.name AS city_name 
                FROM properties p
                INNER JOIN cities c
                ON p.city_id = c.id
                WHERE p.id = $property_id";
    $result_1 = mysqli_query($conn, $sql_1);
    if(!$result_1 || mysqli_num_rows($result_1) == 0){
        echo "Property not found.";
        exit;
    }

    $property = mysqli_fetch_assoc($result_1);

    $sql_2 = "SELECT * FROM testimonials WHERE property_id = $property_id";
    $result_2 = mysqli_query($conn, $sql_2);
    $testimonials = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

    $sql_3 = "SELECT a.*
                FROM amenities a
                INNER JOIN properties_amenities pa ON a.id = pa.amenity_id
                WHERE pa.property_id = $property_id";
    $result_3 = mysqli_query($conn, $sql_3);
    $amenities = mysqli_fetch_all($result_3, MYSQLI_ASSOC);

    $sql_4 = "SELECT * FROM interested_users_properties WHERE property_id = $property_id";
    $result_4 = mysqli_query($conn, $sql_4);
    $interested_users_count = mysqli_num_rows($result_4);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($property['property_name']) ?> | PG Life</title>

    <?php 
        include "includes/head_links.php";
    ?>
    <link href="css/property_detail.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="home.php">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="property_list.php?city=<?= htmlspecialchars($property['city_name']); ?>"><?= htmlspecialchars($property['city_name']); ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($property['property_name']); ?>
            </li>
        </ol>
    </nav>

    <!-- Property Summary Section -->
    <div class="property-summary page-container">
        <div class="row no-gutters justify-content-between">
            <div class="star-container" title="<?= $total_rating ?>">
                <!-- Star Rating Logic Here -->
            </div>
            <div class="interested-container">
                <i class="is-interested-image <?= $is_interested ? 'fas fa-heart' : 'far fa-heart' ?>"></i>
                <div class="interested-text">
                    <span class="interested-user-count"><?= $interested_users_count; ?></span> interested
                </div>
            </div>
        </div>
        
        <div class="detail-container">
            <div class="property-name"><?= htmlspecialchars($property['property_name']) ?></div>
            <div class="property-address"><?= htmlspecialchars($property['address']) ?></div>
            <div class="property-gender">
                <img src="img/<?= htmlspecialchars($property['gender']) ?>.png" alt="gender icon" />
            </div>
        </div>
        
        <!-- Booking Form -->
        <div class="booking-form mt-4">
            <h4>Book this Property</h4>
            <form action="payment.php" method="POST">
                <input type="hidden" name="property_id" value="<?= htmlspecialchars($property_id); ?>">
                
                <div class="form-group">
                    <label for="check_in">Check-in Date:</label>
                    <input type="date" id="check_in" name="check_in" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="check_out">Check-out Date:</label>
                    <input type="date" id="check_out" name="check_out" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="guests">Number of Guests:</label>
                    <input type="number" id="guests" name="guests" min="1" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="guest_name">Name of Guest:</label>
                    <input type="text" id="guest_name" name="guest_name" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="guest_id">ID of Guest:</label>
                    <input type="text" id="guest_id" name="guest_id" required class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary mt-3">Book Now</button>
            </form>
        </div>
    </div>

    <?php include "includes/signup_modal.php"; ?>
    <?php include "includes/login_modal.php"; ?>
    <?php include "includes/footer.php"; ?>

    <script type="text/javascript" src="js/property_detail.js"></script>
</body>
</html>
