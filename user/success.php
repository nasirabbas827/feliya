<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user details from the database
$sql_user = "SELECT username, email, wallet_address FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
mysqli_stmt_bind_result($stmt_user, $username, $email, $wallet_address);
mysqli_stmt_fetch($stmt_user);
mysqli_stmt_close($stmt_user);

?>


<!DOCTYPE html>
<html>
<head>
    <title>Subscription Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Subscription Successful!</h4>
            <p>Your subscription request has been successfully submitted. It will be reviewed by our team shortly.</p>
            <hr>
            <p class="mb-0">Thank you for choosing our service. You will receive further updates via email.</p>
        </div>
    </div>
</body>
</html>
