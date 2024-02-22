<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the offer ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_offers.php");
    exit;
}

// Get the offer ID from the URL
$id = $_GET['id'];

// Fetch offer details from the database based on the provided ID
$sql = "SELECT * FROM offers WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    // Offer found, fetch offer data
    $row = mysqli_fetch_assoc($result);

    // Handle form submission to update offer details
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $offer_name = $_POST['offer_name'];
        $details = $_POST['details'];
        $amount = $_POST['amount'];
        $end_date = $_POST['end_date'];

        // Update offer details in the database
        $update_sql = "UPDATE offers SET offer_name = '$offer_name', details = '$details', amount = '$amount', end_date = '$end_date' WHERE id = $id";
        if (mysqli_query($conn, $update_sql)) {
            echo "<script>alert('Offer details updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating offer details.');</script>";
        }
    }
} else {
    // Offer not found
    echo "<script>alert('Offer not found.');</script>";
    header("Location: view_offers.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Offer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-3">Edit Offer</h2>
    <form method="post">
        <div class="form-group">
            <label for="offer_name">Offer Name:</label>
            <input type="text" class="form-control" id="offer_name" name="offer_name" value="<?php echo $row['offer_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="details">Details:</label>
            <textarea class="form-control" id="details" name="details" rows="3" required><?php echo $row['details']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $row['amount']; ?>" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $row['end_date']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Offer</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
