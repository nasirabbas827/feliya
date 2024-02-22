<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("Location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user details from the database
$sql_user = "SELECT username, email, wallet_address, balance FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
mysqli_stmt_bind_result($stmt_user, $username, $email, $wallet_address, $balance);
mysqli_stmt_fetch($stmt_user);
mysqli_stmt_close($stmt_user);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Offers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-3">View Offers</h2>
    <div class="row">
        <div class="col-md-12 mb-3">
            <p><strong>Balance:</strong> $<?php echo $balance; ?></p>
        </div>
        <?php
        // Fetch offers from the database
        $sql = "SELECT * FROM offers";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Calculate the remaining time for the offer
                $current_date = new DateTime();
                $end_date = new DateTime($row['end_date']);
                $interval = $current_date->diff($end_date);

                // Format remaining time as days, hours, minutes, seconds
                $remaining_time = $interval->format('%a days %h hours %i minutes %s seconds');

                // Check if the user has already participated in this offer
                $offer_id = $row['id'];
                $sql_participation = "SELECT * FROM participants WHERE user_id = ? AND offer_id = ?";
                $stmt_participation = mysqli_prepare($conn, $sql_participation);
                mysqli_stmt_bind_param($stmt_participation, "ii", $user_id, $offer_id);
                mysqli_stmt_execute($stmt_participation);
                $participation_result = mysqli_stmt_get_result($stmt_participation);

                if (mysqli_num_rows($participation_result) > 0) {
                    // User has already participated in this offer
                    $participation_row = mysqli_fetch_assoc($participation_result);
                    echo "<div class='col-md-4'>";
                    echo "<div class='card mb-4'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['offer_name'] . "</h5>";
                    echo "<p class='card-text'>" . $row['details'] . "</p>";
                    echo "<p class='card-text'>Amount: $" . $row['amount'] . "</p>";
                    echo "<p class='card-text'>End Date: " . $row['end_date'] . "</p>";
                    
                    // Fetch winner username
                    $winner_id = $participation_row['user_id'];
                    $sql_winner = "SELECT username FROM users WHERE id = ?";
                    $stmt_winner = mysqli_prepare($conn, $sql_winner);
                    mysqli_stmt_bind_param($stmt_winner, "i", $winner_id);
                    mysqli_stmt_execute($stmt_winner);
                    mysqli_stmt_bind_result($stmt_winner, $winner_username);
                    mysqli_stmt_fetch($stmt_winner);
                    mysqli_stmt_close($stmt_winner);
                    
                    if ($participation_row['is_winner']) {
                        echo "<p class='card-text'>Winner: " . $winner_username . "</p>";
                    } else {
                        echo "<p class='card-text'>Status: Wait for results</p>";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    // User has not participated in this offer
                    echo "<div class='col-md-4'>";
                    echo "<div class='card mb-4'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $row['offer_name'] . "</h5>";
                    echo "<p class='card-text'>" . $row['details'] . "</p>";
                    echo "<p class='card-text'>Amount: $" . $row['amount'] . "</p>";
                    echo "<p class='card-text'>End Date: " . $row['end_date'] . "</p>";
                    echo "<p class='card-text'>Remaining Time: <span id='timer_" . $offer_id . "'>" . $remaining_time . "</span></p>";
                    echo "<form method='post' action='participate.php'>";
                    echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
                    echo "<input type='hidden' name='offer_id' value='" . $offer_id . "'>";
                    echo "<button type='submit' class='btn btn-primary'>Participate</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                
                
            }
        } else {
            echo "<p>No offers found</p>";
        }
        ?>
    </div>
</div>

<script>
// Update countdown timer for each offer every second
setInterval(function() {
    <?php
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $offer_id = $row['id'];
            $end_date = new DateTime($row['end_date']);
            $current_date = new DateTime();
            $interval = $end_date->diff($current_date);
            ?>
            var timer_<?php echo $offer_id; ?> = document.getElementById('timer_<?php echo $offer_id; ?>');
            timer_<?php echo $offer_id; ?>.innerText = '<?php echo $interval->format('%a days %h hours %i minutes %s seconds'); ?>';
        <?php
        }
    }
    ?>
}, 1000);
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
