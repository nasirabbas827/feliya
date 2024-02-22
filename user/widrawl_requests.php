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

// Fetch user details from the database including wallet address and balance
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($result_user);
mysqli_stmt_close($stmt_user);

// Fetch total withdrawal amount for the user with status 'Completed'
$sql_total_withdrawal = "SELECT SUM(withdrawal_amount_after_charge) AS total_withdrawal FROM withdrawals WHERE user_id = ? AND status = 'Completed'";
$stmt_total_withdrawal = mysqli_prepare($conn, $sql_total_withdrawal);
mysqli_stmt_bind_param($stmt_total_withdrawal, "i", $user_id);
mysqli_stmt_execute($stmt_total_withdrawal);
$result_total_withdrawal = mysqli_stmt_get_result($stmt_total_withdrawal);
$total_withdrawal_row = mysqli_fetch_assoc($result_total_withdrawal);
$total_withdrawal = $total_withdrawal_row['total_withdrawal'];
mysqli_stmt_close($stmt_total_withdrawal);

// Calculate total balance (balance - total completed withdrawals)
$total_balance = $user['balance'] - $total_withdrawal;

// Fetch withdrawal requests for the user
$sql_withdrawal_requests = "SELECT * FROM withdrawals WHERE user_id = ?";
$stmt_withdrawal_requests = mysqli_prepare($conn, $sql_withdrawal_requests);
mysqli_stmt_bind_param($stmt_withdrawal_requests, "i", $user_id);
mysqli_stmt_execute($stmt_withdrawal_requests);
$result_withdrawal_requests = mysqli_stmt_get_result($stmt_withdrawal_requests);
mysqli_stmt_close($stmt_withdrawal_requests);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdrawal Requests</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Withdrawal Requests</h2>
    <p>Total Balance: <?php echo $user['balance']; ?>$</p>
    <p>Total Withdrawals: <?php echo $total_withdrawal; ?>$</p>
    <div class="card mt-4">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Withdrawal Amount</th>
                        <th>Charge</th>
                        <th>Amount After Deduction</th>
                        <th>Wallet Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_withdrawal_requests)) : ?>
                        <tr>
                            <td><?php echo $row['withdrawal_amount']; ?>$</td>
                            <td><?php echo $row['charge']; ?>$</td>
                            <td><?php echo $row['withdrawal_amount_after_charge']; ?>$</td>
                            <td><?php echo $row['wallet_address']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
