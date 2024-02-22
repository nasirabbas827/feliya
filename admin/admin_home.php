<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = 0;
$totalPackages = 0;
$totalCompletedPackages = 0;
$totalOffers = 0;
$totalParticipants = 0;
$totalWithdrawalRequests = 0;
$totalQueriesForReply = 0;
$totalBalance = 0; // New variable for total balance

// Fetch total balance of all users
$totalBalanceResult = mysqli_query($conn, "SELECT SUM(balance) AS total_balance FROM users");
if ($totalBalanceResult) {
    $totalBalanceRow = mysqli_fetch_assoc($totalBalanceResult);
    $totalBalance = $totalBalanceRow['total_balance'];
}

// Fetch total users
$totalUsersResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
if ($totalUsersResult) {
    $totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];
}

// Fetch total packages
$totalPackagesResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM packages");
if ($totalPackagesResult) {
    $totalPackages = mysqli_fetch_assoc($totalPackagesResult)['total'];
}

// Fetch total completed packages
$totalCompletedPackagesResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM subscriptions WHERE status = 'completed'");
if ($totalCompletedPackagesResult) {
    $totalCompletedPackages = mysqli_fetch_assoc($totalCompletedPackagesResult)['total'];
}

// Fetch total offers
$totalOffersResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM offers");
if ($totalOffersResult) {
    $totalOffers = mysqli_fetch_assoc($totalOffersResult)['total'];
}

// Fetch total participants
$totalParticipantsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM participants");
if ($totalParticipantsResult) {
    $totalParticipants = mysqli_fetch_assoc($totalParticipantsResult)['total'];
}

// Fetch total withdrawal requests
$totalWithdrawalRequestsResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM withdrawals");
if ($totalWithdrawalRequestsResult) {
    $totalWithdrawalRequests = mysqli_fetch_assoc($totalWithdrawalRequestsResult)['total'];
}

// Fetch total queries for reply
$totalQueriesForReply = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM messages WHERE reply_text IS NULL"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Balance Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Balance of Users</h5>
                    <p class="card-text">$<?php echo number_format($totalBalance, 2); ?></p>
                </div>
            </div>
        </div>
        <!-- Total Users Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Packages Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Packages</h5>
                    <p class="card-text"><?php echo $totalPackages; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <!-- Total Completed Packages Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Completed Packages</h5>
                    <p class="card-text"><?php echo $totalCompletedPackages; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Offers Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Offers</h5>
                    <p class="card-text"><?php echo $totalOffers; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Participants Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Participants</h5>
                    <p class="card-text"><?php echo $totalParticipants; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Withdrawal Requests Card -->
        <div class="col-md-4">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Total Withdrawal Requests</h5>
                    <p class="card-text"><?php echo $totalWithdrawalRequests; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Queries for Reply Card -->
        <div class="col-md-4">
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Total Queries for Reply</h5>
                    <p class="card-text"><?php echo $totalQueriesForReply; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
