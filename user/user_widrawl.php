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

// Fetch user details from the database including wallet address
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($result_user);
mysqli_stmt_close($stmt_user);

// Check if form is submitted for withdrawal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["withdrawal_amount"])) {
    $withdrawal_amount = $_POST["withdrawal_amount"];
    
    // Check if withdrawal amount exceeds user balance
    if ($withdrawal_amount > $user['balance']) {
        // Redirect to withdrawal page with error message
        header("location: withdrawal.php?error=amount_exceeds_balance");
        exit;
    }

    // Apply 10% charge
    $charge = 0.10 * $withdrawal_amount;
    $withdrawal_amount_after_charge = $withdrawal_amount - $charge;

    // Insert withdrawal request into database
    $sql_withdrawal = "INSERT INTO withdrawals (user_id, withdrawal_amount, charge, withdrawal_amount_after_charge, wallet_address, status) VALUES (?, ?, ?, ?, ?, 'Requested')";
    $stmt_withdrawal = mysqli_prepare($conn, $sql_withdrawal);
    mysqli_stmt_bind_param($stmt_withdrawal, "iddss", $user_id, $withdrawal_amount, $charge, $withdrawal_amount_after_charge, $user["wallet_address"]);
    mysqli_stmt_execute($stmt_withdrawal);
    mysqli_stmt_close($stmt_withdrawal);

    // Redirect to dashboard with success message
    header("location: user_widrawl.php?withdrawal_success=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdrawal</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Withdrawal</h2>
    <?php if (isset($_GET["withdrawal_success"]) && $_GET["withdrawal_success"] == 1) : ?>
        <div class="alert alert-success" role="alert">
            Withdrawal request submitted successfully.
        </div>
    <?php endif; ?>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Withdrawal Form</h5>
            <p class="card-text">Your Balance: <?php echo $user['balance']; ?>$</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="withdrawal_amount">Withdrawal Amount</label>
                    <input type="number" class="form-control" id="withdrawal_amount" name="withdrawal_amount" required min="1" max="<?php echo $user['balance']; ?>" step="0.01" oninput="calculateAmount()">
                </div>
                <div class="form-group">
                    <label for="wallet_address">Wallet Address</label>
                    <input type="text" class="form-control" id="wallet_address" name="wallet_address" value="<?php echo $user['wallet_address']; ?>" disabled>
                </div>
                <p>Amount after 10% deduction: <span id="after_deduction">0</span></p>
                <button type="submit" class="btn btn-primary">Withdraw</button>
                <a class="btn btn-outline-dark"  href="widrawl_requests.php">My Widrawl Request</a>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function calculateAmount() {
    var withdrawalAmount = parseFloat(document.getElementById('withdrawal_amount').value);
    var charge = 0.10 * withdrawalAmount;
    var amountAfterDeduction = withdrawalAmount - charge;
    document.getElementById('after_deduction').innerText = amountAfterDeduction.toFixed(2);
}
</script>
</body>
</html>
