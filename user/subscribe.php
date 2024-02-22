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

// Fetch package details from the URL
if(isset($_GET['package_id'])) {
    $package_id = $_GET['package_id'];
    $sql_package = "SELECT * FROM packages WHERE package_id = ?";
    $stmt_package = mysqli_prepare($conn, $sql_package);
    mysqli_stmt_bind_param($stmt_package, "i", $package_id);
    mysqli_stmt_execute($stmt_package);
    mysqli_stmt_bind_result($stmt_package, $package_id, $title, $amount, $description, $daily_profit);
    mysqli_stmt_fetch($stmt_package);
    mysqli_stmt_close($stmt_package);
}

// Handle subscription form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deposit'])) {
    // Insert subscription details into the database
    $status = "pending";
    $sql_insert_subscription = "INSERT INTO subscriptions (user_id, package_id, status) VALUES (?, ?, ?)";
    $stmt_insert_subscription = mysqli_prepare($conn, $sql_insert_subscription);
    mysqli_stmt_bind_param($stmt_insert_subscription, "iis", $user_id, $package_id, $status);
    mysqli_stmt_execute($stmt_insert_subscription);
    mysqli_stmt_close($stmt_insert_subscription);

    // Redirect user to success page or perform other actions
    header("Location: success.php");
    exit;
}
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
    <h2 class="mb-4">Subscription Details</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">User Details</h5>
                    <form>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" class="form-control" value="<?php echo $username; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" class="form-control" value="<?php echo $email; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="wallet_address">Wallet Address:</label>
                            <input type="text" id="wallet_address" class="form-control" value="<?php echo $wallet_address; ?>" disabled>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Package Details</h5>
                    <form>
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" class="form-control" value="<?php echo $title; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="text" id="amount" class="form-control" value="<?php echo $amount; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" class="form-control" disabled><?php echo $description; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="daily_profit">Daily Profit:</label>
                            <input type="text" id="daily_profit" class="form-control" value="<?php echo $daily_profit; ?>" disabled>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <form method="post">
            <button type="submit" name="deposit" class="btn btn-primary mb-5">Deposit</button>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
