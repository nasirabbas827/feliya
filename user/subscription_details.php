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

// Get the subscription ID from the URL
if (!isset($_GET["subscription_id"])) {
    header("location: user_dashboard.php");
    exit;
}
$subscription_id = $_GET["subscription_id"];

// Fetch subscription details from the database
$sql_subscription = "SELECT s.*, p.title AS package_title, p.daily_profit
                    FROM subscriptions s
                    INNER JOIN packages p ON s.package_id = p.package_id
                    WHERE s.subscription_id = ? AND s.user_id = ?";
$stmt_subscription = mysqli_prepare($conn, $sql_subscription);
mysqli_stmt_bind_param($stmt_subscription, "ii", $subscription_id, $user_id);
mysqli_stmt_execute($stmt_subscription);
$result_subscription = mysqli_stmt_get_result($stmt_subscription);
$row_subscription = mysqli_fetch_assoc($result_subscription);
mysqli_stmt_close($stmt_subscription);

// Fetch user balance from the database
$sql_balance = "SELECT balance FROM users WHERE id = ?";
$stmt_balance = mysqli_prepare($conn, $sql_balance);
mysqli_stmt_bind_param($stmt_balance, "i", $user_id);
mysqli_stmt_execute($stmt_balance);
mysqli_stmt_bind_result($stmt_balance, $balance);
mysqli_stmt_fetch($stmt_balance);
mysqli_stmt_close($stmt_balance);
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
    <h2>Subscription Details</h2>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Package: <?php echo $row_subscription['package_title']; ?></h5>
            <p class="card-text">Daily Profit: <?php echo $row_subscription['daily_profit']; ?>$</p>
            <p class="card-text">Your Balance: <?php echo $balance; ?>$</p>
            <?php 
            // Calculate time since last profit was earned
            $last_profit_earned_time = strtotime($row_subscription['last_profit_earned']);
            $current_time = time();
            $time_diff = $current_time - $last_profit_earned_time;
            $remaining_time_seconds = 24 * 60 * 60 - $time_diff; // Remaining time in seconds
            
            // Convert remaining time to hours, minutes, and seconds
            $remaining_hours = floor($remaining_time_seconds / 3600);
            $remaining_minutes = floor(($remaining_time_seconds % 3600) / 60);
            $remaining_seconds = $remaining_time_seconds % 60;
            
            if ($remaining_time_seconds <= 0) {
                // Allow the user to earn profit
                echo '<form action="earn_profit.php" method="post">';
                echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
                echo '<input type="hidden" name="subscription_id" value="' . $subscription_id . '">';
                echo '<button type="submit" class="btn btn-primary" id="earn-profit-btn">Earn Profit</button>';
                echo '</form>';
            } else {
                // Show timer
                echo '<p class="card-text">Next profit can be earned in <span id="timer">' . $remaining_hours . ' hours, ' . $remaining_minutes . ' minutes, ' . $remaining_seconds . ' seconds</span>.</p>';
                echo '<button class="btn btn-primary" disabled>Earn Profit</button>';
            }
            ?>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
// Update timer every second
setInterval(updateTimer, 1000);

function updateTimer() {
    var timerElement = document.getElementById('timer');
    var remainingTime = timerElement.innerText.split(', '); // Split hours, minutes, and seconds
    var hours = parseInt(remainingTime[0].split(' ')[0]);
    var minutes = parseInt(remainingTime[1].split(' ')[0]);
    var seconds = parseInt(remainingTime[2].split(' ')[0]);
    
    // Decrement remaining time
    if (seconds > 0) {
        seconds--;
    } else {
        if (minutes > 0) {
            minutes--;
            seconds = 59;
        } else {
            if (hours > 0) {
                hours--;
                minutes = 59;
                seconds = 59;
            }
        }
    }
    
    // Update timer display
    timerElement.innerText = hours + ' hours, ' + minutes + ' minutes, ' + seconds + ' seconds';
}
</script>
</body>
</html>
