<?php
include('config.php');
?>


<!DOCTYPE html>
<html>
<head>
    <title>Feliya - A Mining Profit Website</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        
        .jumbotron {
            height: 550px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }

        /* Style for package cards */
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<div class="jumbotron text-center">
    <h1>Welcome to Feliya - A Mining Profit Website</h1>
    <p>Discover Your Perfect Mining Package and Offers</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Explore</a>
</div>

<div class="container">
    <h2 class="text-center mb-4">Mining Packages</h2>
    <div class="row">
        <?php
        // Fetch packages from the database and display as cards
        $sql_packages = "SELECT * FROM packages";
        $result_packages = mysqli_query($conn, $sql_packages);

        if (mysqli_num_rows($result_packages) > 0) {
            while ($row_package = mysqli_fetch_assoc($result_packages)) {
                echo "<div class='col-md-4'>";
                echo "<div class='card package-card'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . $row_package['title'] . "</h5>";
                echo "<p class='card-text'>Amount: $" . $row_package['amount'] . "</p>";
                echo "<p class='card-text'>" . $row_package['description'] . "</p>";
                echo "<p class='card-text'>Daily Profit: $" . $row_package['daily_profit'] . "</p>";
                // Add subscribe button
                echo "<a href='login' class='btn btn-primary'>Subscribe</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-center'>No packages available.</p>";
        }
        ?>
    </div>
</div>

<div class="container mt-5">
    <h2 class="text-center mb-4">Offers</h2>
    <div class="row">
        <?php
        // Fetch offers from the database and display as cards
        $sql_offers = "SELECT * FROM offers";
        $result_offers = mysqli_query($conn, $sql_offers);

        if (mysqli_num_rows($result_offers) > 0) {
            while ($row_offer = mysqli_fetch_assoc($result_offers)) {
                echo "<div class='col-md-4'>";
                echo "<div class='card'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . $row_offer['offer_name'] . "</h5>";
                echo "<p class='card-text'>" . $row_offer['details'] . "</p>";
                echo "<p class='card-text'>Amount: $" . $row_offer['amount'] . "</p>";
                echo "<p class='card-text'>End Date: " . $row_offer['end_date'] . "</p>";
                // Add participate button
                echo "<a href='login' class='btn btn-primary'>Participate</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-center'>No offers available.</p>";
        }
        ?>
    </div>
</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; <span id="currentYear"></span> Feliya. All rights reserved.</p>
    </div>
</footer>

<script>
    // Get current year and update the footer
    document.getElementById("currentYear").innerHTML = new Date().getFullYear();
</script>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
