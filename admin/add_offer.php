<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission to add offers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $offer_name = $_POST['offer_name'];
    $details = $_POST['details'];
    $amount = $_POST['amount'];
    $end_date = $_POST['end_date'];

    // Insert offer details into the database
    $insert_sql = "INSERT INTO offers (offer_name, details, amount, end_date) VALUES ('$offer_name', '$details', '$amount', '$end_date')";
    if (mysqli_query($conn, $insert_sql)) {
        echo "<script>alert('Offer added successfully.');</script>";
    } else {
        echo "<script>alert('Error adding offer.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Offer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-3">Add Offer</h2>
    <form method="post">
        <div class="form-group">
            <label for="offer_name">Offer Name:</label>
            <input type="text" class="form-control" id="offer_name" name="offer_name" required>
        </div>
        <div class="form-group">
            <label for="details">Details:</label>
            <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Offer</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
