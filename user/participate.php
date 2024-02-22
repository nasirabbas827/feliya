<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("Location: index.php");
    exit;
}

// Get user ID and offer ID from the form submission
$user_id = $_POST['user_id'];
$offer_id = $_POST['offer_id'];

// Fetch offer details from the database
$sql_offer = "SELECT * FROM offers WHERE id = ?";
$stmt_offer = mysqli_prepare($conn, $sql_offer);
mysqli_stmt_bind_param($stmt_offer, "i", $offer_id);
mysqli_stmt_execute($stmt_offer);
$result_offer = mysqli_stmt_get_result($stmt_offer);

if (mysqli_num_rows($result_offer) == 1) {
    // Offer found, fetch offer data
    $offer = mysqli_fetch_assoc($result_offer);
    $offer_amount = $offer['amount'];

    // Fetch user balance from the database
    $sql_balance = "SELECT balance FROM users WHERE id = ?";
    $stmt_balance = mysqli_prepare($conn, $sql_balance);
    mysqli_stmt_bind_param($stmt_balance, "i", $user_id);
    mysqli_stmt_execute($stmt_balance);
    $result_balance = mysqli_stmt_get_result($stmt_balance);

    if (mysqli_num_rows($result_balance) == 1) {
        // User found, fetch user balance
        $user = mysqli_fetch_assoc($result_balance);
        $user_balance = $user['balance'];

        // Check if the user has sufficient balance to participate in the offer
        if ($user_balance >= $offer_amount) {
            // Deduct offer amount from user balance
            $new_balance = $user_balance - $offer_amount;

            // Update user balance in the database
            $update_balance_sql = "UPDATE users SET balance = ? WHERE id = ?";
            $stmt_update_balance = mysqli_prepare($conn, $update_balance_sql);
            mysqli_stmt_bind_param($stmt_update_balance, "di", $new_balance, $user_id);
            mysqli_stmt_execute($stmt_update_balance);

            // Insert participation record into the participants table
            $insert_participation_sql = "INSERT INTO participants (user_id, offer_id) VALUES (?, ?)";
            $stmt_insert_participation = mysqli_prepare($conn, $insert_participation_sql);
            mysqli_stmt_bind_param($stmt_insert_participation, "ii", $user_id, $offer_id);
            mysqli_stmt_execute($stmt_insert_participation);

            echo "<script>alert('You have successfully participated in the offer.');</script>";
            header("Location: view_offers.php");
            exit;
        } else {
            echo "<script>alert('Insufficient balance.');</script>";
            header("Location: view_offers.php");
            exit;
        }
    } else {
        echo "<script>alert('User not found.');</script>";
        header("Location: view_offers.php");
        exit;
    }
} else {
    echo "<script>alert('Offer not found.');</script>";
    header("Location: view_offers.php");
    exit;
}
?>
