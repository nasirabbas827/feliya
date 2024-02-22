<?php
include('config.php');

session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all withdrawal requests from the database
$sql = "SELECT * FROM withdrawals";
$result = mysqli_query($conn, $sql);

// Function to update withdrawal status
function updateStatus($conn, $withdrawal_id, $status) {
    $sql = "UPDATE withdrawals SET status = ? WHERE withdrawal_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $withdrawal_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // If status is completed, subtract withdrawal amount from user balance
    if ($status == 'Completed') {
        $sql_withdrawal = "SELECT user_id, withdrawal_amount FROM withdrawals WHERE withdrawal_id = ?";
        $stmt_withdrawal = mysqli_prepare($conn, $sql_withdrawal);
        mysqli_stmt_bind_param($stmt_withdrawal, "i", $withdrawal_id);
        mysqli_stmt_execute($stmt_withdrawal);
        mysqli_stmt_bind_result($stmt_withdrawal, $user_id, $withdrawal_amount);
        mysqli_stmt_fetch($stmt_withdrawal);
        mysqli_stmt_close($stmt_withdrawal);

        // Update user balance
        $sql_balance = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $stmt_balance = mysqli_prepare($conn, $sql_balance);
        mysqli_stmt_bind_param($stmt_balance, "di", $withdrawal_amount, $user_id);
        mysqli_stmt_execute($stmt_balance);
        mysqli_stmt_close($stmt_balance);
    }
}

// Check if the status update form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["withdrawal_id"]) && isset($_POST["status"])) {
    $withdrawal_id = $_POST["withdrawal_id"];
    $status = $_POST["status"];
    updateStatus($conn, $withdrawal_id, $status);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Withdrawals</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Withdrawals</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Withdrawal ID</th>
                    <th>User</th> 
                    <th>Withdrawal Amount</th>
                    <th>Charge</th>
                    <th>Amount After Charge</th>
                    <th>Wallet Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while ($row = mysqli_fetch_assoc($result)) : 
                    // Fetch username based on user ID
                    $user_id = $row['user_id'];
                    $user_query = mysqli_query($conn, "SELECT username FROM users WHERE id = $user_id");
                    $user_row = mysqli_fetch_assoc($user_query);
                    $username = $user_row['username'];
                ?>
                    <tr>
                        <td><?php echo $row['withdrawal_id']; ?></td>
                        <td><?php echo $username; ?></td> 
                        <td><?php echo $row['withdrawal_amount']; ?></td>
                        <td><?php echo $row['charge']; ?></td>
                        <td><?php echo $row['withdrawal_amount_after_charge']; ?></td>
                        <td><?php echo $row['wallet_address']; ?></td>
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="withdrawal_id" value="<?php echo $row['withdrawal_id']; ?>">
                                <select name="status" class="form-control">
                                    <option value="Requested" <?php echo ($row['status'] == 'Requested') ? 'selected' : ''; ?>>Requested</option>
                                    <option value="Processing" <?php echo ($row['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                </select>
                        </td>
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
