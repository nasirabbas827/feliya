<?php
include('config.php');

session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all subscriptions with user details from the database
$sql = "SELECT s.*, u.username, u.email, u.wallet_address, u.balance, p.title AS package_title, p.amount, p.daily_profit 
        FROM subscriptions s 
        INNER JOIN users u ON s.user_id = u.id 
        INNER JOIN packages p ON s.package_id = p.package_id";
$result = mysqli_query($conn, $sql);

// Function to update subscription status and user balance
function updateStatus($conn, $subscription_id, $status) {
    $sql = "UPDATE subscriptions SET status = ? WHERE subscription_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $subscription_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // If status is completed, add package amount to user balance
    if ($status == 'completed') {
        $sql_balance = "UPDATE users u
                        INNER JOIN subscriptions s ON u.id = s.user_id
                        INNER JOIN packages p ON s.package_id = p.package_id
                        SET u.balance = u.balance + p.amount
                        WHERE s.subscription_id = ?";
        $stmt_balance = mysqli_prepare($conn, $sql_balance);
        mysqli_stmt_bind_param($stmt_balance, "i", $subscription_id);
        mysqli_stmt_execute($stmt_balance);
        mysqli_stmt_close($stmt_balance);
    } else {
        // If status is not completed, subtract package amount from user balance
        $sql_subtract = "UPDATE users u
                        INNER JOIN subscriptions s ON u.id = s.user_id
                        INNER JOIN packages p ON s.package_id = p.package_id
                        SET u.balance = u.balance - p.amount
                        WHERE s.subscription_id = ?";
        $stmt_subtract = mysqli_prepare($conn, $sql_subtract);
        mysqli_stmt_bind_param($stmt_subtract, "i", $subscription_id);
        mysqli_stmt_execute($stmt_subtract);
        mysqli_stmt_close($stmt_subtract);
    }
}

// Check if the status update form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscription_id"]) && isset($_POST["status"])) {
    $subscription_id = $_POST["subscription_id"];
    $status = $_POST["status"];
    updateStatus($conn, $subscription_id, $status);
    // Redirect to refresh the page
    header("Location: view_subscriptions.php");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Subscriptions</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Adjust table column widths */
        .table th, .table td {
            white-space: nowrap;
        }

        /* Style for status dropdown */
        .status-dropdown {
            width: 100%;
        }
    </style>
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Subscriptions</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Subscription ID</th>
                    <th>Username</th>
                    <th>Wallet Address</th>
                    <th>Package Name</th>
                    <th>Amount</th>
                    <th>Daily Profit</th>
                    <th>Status</th>
                    <th>User Balance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['subscription_id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo substr($row['wallet_address'], 0, 20); ?></td> <!-- Display only first 20 characters of the wallet address -->
                        <td><?php echo $row['package_title']; ?></td>
                        <td>$<?php echo $row['amount']; ?></td>
                        <td><?php echo $row['daily_profit']; ?>$</td>
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="subscription_id" value="<?php echo $row['subscription_id']; ?>">
                                <select name="status" class="form-control status-dropdown">
                                    <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="process" <?php echo ($row['status'] == 'process') ? 'selected' : ''; ?>>Process</option>
                                    <option value="completed" <?php echo ($row['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                </select>
                        </td>
                        <td>$<?php echo $row['balance']; ?></td>
                        <td>
                            <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
