<?php
    session_start();
    require "includes/database_connect.php";

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $city_name = mysqli_real_escape_string($conn, $_GET["city"]);

    // Get city details
    $sql_1 = "SELECT * FROM cities WHERE name = '$city_name'";
    $result_1 = mysqli_query($conn, $sql_1);
    if (!$result_1) {
        echo "Something went wrong!";
        return;
    }

    $city = mysqli_fetch_assoc($result_1);
    if (!$city) {
        echo "Sorry! We do not have any PG listed in this city.";
        return;
    }

    $city_id = $city['id'];

    // Get properties for the city
    $sql_2 = "SELECT * FROM properties WHERE city_id = $city_id";
    $result_2 = mysqli_query($conn, $sql_2);
    if (!$result_2) {
        echo "Something went wrong!";
        return;
    }

    $properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

    // Get interested users for each property
    $sql_3 = "SELECT * 
                FROM interested_users_properties iup
                INNER JOIN properties p ON iup.property_id = p.id
                WHERE p.city_id = $city_id";
    $result_3 = mysqli_query($conn, $sql_3);
    if (!$result_3) {
        echo "Something went wrong!";
        return;
    }

    $interested_users_properties = mysqli_fetch_all($result_3, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in <?= htmlspecialchars($city['name']); ?> | PG Tak Safar</title>

    <?php 
        include "includes/head_links.php";
    ?>

    <link href="css/property_list.css" rel="stylesheet" />
</head>
<body>
    <?php
        include "includes/header.php";
    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="home.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($city_name); ?>
            </li>
        </ol>
    </nav>

    <div class="page-container">
        <?php
            foreach ($properties as $property) {
            $property_images = glob("img/properties/" . $property['id'] . "/*");
            if (!$property_images) {
            echo "<p>Image not found for property ID: " . $property['id'] . "</p>";
            }
        ?>
            <div class="property-card property-id-<?= $property['id'] ?> row">
                <div class="image-container col-md-4">
                    <img src="<?= $property_images[0] ?>" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <?php
                            $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                            $total_rating = round($total_rating, 1);
                        ?>
                        <div class="star-container" title="<?= $total_rating ?>">
                            <?php
                                $rating = $total_rating;
                                for ($i = 0; $i < 5; $i++) {
                                    if ($rating >= $i + 0.8) {
                            ?>
                                    <i class="fas fa-star"></i>
                            <?php
                                } elseif ($rating >= $i + 0.3) {
                            ?>
                                    <i class="fas fa-star-half-alt"></i>
                            <?php
                                } else {
                            ?>
                                    <i class="far fa-star"></i>
                            <?php
                                    }
                                }
                            ?>
                        </div>
                        <div class="interested-container">
                            <?php
                                $interested_users_count = 0;
                                $is_interested = false;
                                foreach ($interested_users_properties as $interested_user_property) {
                                    if ($interested_user_property['property_id'] == $property['id']) {
                                        $interested_users_count++;

                                        if ($interested_user_property['user_id'] == $user_id) {
                                            $is_interested = true;
                                        }
                                    }
                                }

                                if ($is_interested) {
                            ?>
                                <i class="is-interested-image property-id-<?= $property['id'] ?> fas fa-heart" property_id="<?= $property['id'] ?>"></i>
                            <?php
                                } else {
                            ?>
                                <i class="is-interested-image property-id-<?= $property['id'] ?> far fa-heart" property_id="<?= $property['id'] ?>"></i>
                            <?php
                                }
                            ?>
                            <div class="interested-text">
                                <span class="interested-user-count property-id-<?= $property['id'] ?> "><?= $interested_users_count ?></span> interested
                            </div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= htmlspecialchars($property['name']) ?></div>
                        <div class="property-address"><?= htmlspecialchars($property['address']) ?></div>
                        <div class="property-gender">
                            <?php
                                if ($property['gender'] == "male") {
                            ?>
                                <img src="img/male.png" />
                            <?php
                                } elseif ($property['gender'] == "female") {
                            ?>
                                <img src="img/female.png" />
                            <?php
                                } else {
                            ?>
                                <img src="img/unisex.png" />
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            }

            if (count($properties) == 0) {
        ?>
            <div class="no-property-container">
                <p>No PG to list</p>
            </div>
        <?php
            }
        ?>
    </div>

    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="filter-heading">Filters</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Gender</h5>
                    <hr />
                    <div>
                        <button class="btn btn-outline-dark btn-active">
                            No Filter
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-venus-mars"></i>Unisex
                        </button>
                        <button class="btn btn-outline-dark" >
                            <i class="fas fa-mars"></i>Male
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-venus"></i>Female
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-success">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <?php
        include "includes/signup_modal.php";
        include "includes/login_modal.php";
        include "includes/footer.php";
    ?>

    <script type="text/javascript" src="js/property_list.js"></script>
</body>
</html>
