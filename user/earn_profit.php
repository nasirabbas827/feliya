<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"]) && isset($_POST["subscription_id"])) {
    $user_id = $_POST["user_id"];
    $subscription_id = $_POST["subscription_id"];

    // Fetch subscription details from the database
    $sql_subscription = "SELECT p.daily_profit, s.last_profit_earned FROM subscriptions s
                        INNER JOIN packages p ON s.package_id = p.package_id
                        WHERE s.subscription_id = ? AND s.user_id = ?";
    $stmt_subscription = mysqli_prepare($conn, $sql_subscription);
    mysqli_stmt_bind_param($stmt_subscription, "ii", $subscription_id, $user_id);
    mysqli_stmt_execute($stmt_subscription);
    $result_subscription = mysqli_stmt_get_result($stmt_subscription);
    $row_subscription = mysqli_fetch_assoc($result_subscription);
    mysqli_stmt_close($stmt_subscription);

    // Check if enough time has passed since the last profit was earned
    $last_profit_earned = strtotime($row_subscription['last_profit_earned']);
    $current_time = time();
    $time_diff = $current_time - $last_profit_earned;
    $hours_passed = floor($time_diff / (60 * 60)); // Calculate hours passed

    // If less than 24 hours have passed since the last profit was earned, redirect back with an error message
    if ($hours_passed < 24) {
        $_SESSION['error'] = "You can only earn profit once every 24 hours.";
        header("Location: subscription_details.php?subscription_id=$subscription_id");
        exit;
    }

    // Calculate the profit to be earned
    $daily_profit = $row_subscription['daily_profit'];
    $profit_earned = $daily_profit;

    // Update the user's balance
    $sql_update_balance = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmt_update_balance = mysqli_prepare($conn, $sql_update_balance);
    mysqli_stmt_bind_param($stmt_update_balance, "di", $profit_earned, $user_id);
    mysqli_stmt_execute($stmt_update_balance);
    mysqli_stmt_close($stmt_update_balance);

    // Update the last profit earned time
    $current_datetime = date("Y-m-d H:i:s");
    $sql_update_time = "UPDATE subscriptions SET last_profit_earned = ? WHERE subscription_id = ?";
    $stmt_update_time = mysqli_prepare($conn, $sql_update_time);
    mysqli_stmt_bind_param($stmt_update_time, "si", $current_datetime, $subscription_id);
    mysqli_stmt_execute($stmt_update_time);
    mysqli_stmt_close($stmt_update_time);

    // Redirect back to subscription details page with success message
    $_SESSION['success'] = "You have earned $profit_earned$ profit successfully.";
    header("Location: subscription_details.php?subscription_id=$subscription_id");
    exit;
} else {
    // If the form is not submitted, redirect back to subscription details page
    header("Location: subscription_details.php");
    exit;
}
?>
