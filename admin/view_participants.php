<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to fetch participants for a specific offer
function fetchParticipants($conn, $offer_id) {
    $participants = array();
    $sql = "SELECT * FROM participants WHERE offer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $offer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $participants[] = $row;
    }
    return $participants;
}

// Handle winner selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["winner"])) {
    $participant_id = $_POST["winner"];
    $offer_id = $_POST["offer_id"];
    $sql = "UPDATE participants SET is_winner = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $participant_id);
    mysqli_stmt_execute($stmt);
    // Update other participants of the same offer to mark them as non-winners
    $sql_update_others = "UPDATE participants SET is_winner = 0 WHERE offer_id = ? AND id != ?";
    $stmt_update_others = mysqli_prepare($conn, $sql_update_others);
    mysqli_stmt_bind_param($stmt_update_others, "ii", $offer_id, $participant_id);
    mysqli_stmt_execute($stmt_update_others);
    echo "<script>alert('Winner selected successfully.');</script>";
}

// Fetch offers from the database
$sql_offers = "SELECT * FROM offers";
$result_offers = mysqli_query($conn, $sql_offers);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Participants and Choose Winner</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-3">View Participants and Choose Winner</h2>
    <?php
    if (mysqli_num_rows($result_offers) > 0) {
        while ($row_offer = mysqli_fetch_assoc($result_offers)) {
            $offer_id = $row_offer['id'];
            $offer_name = $row_offer['offer_name'];
            echo "<div class='card mb-4'>";
            echo "<h5 class='card-header'>$offer_name</h5>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>Participants</h5>";
            echo "<ul class='list-group'>";
            // Fetch participants for this offer
            $participants = fetchParticipants($conn, $offer_id);
            if (!empty($participants)) {
                foreach ($participants as $participant) {
                    $participant_id = $participant['id'];
                    $user_id = $participant['user_id'];
                    // Fetch user details
                    $sql_user = "SELECT username FROM users WHERE id = ?";
                    $stmt_user = mysqli_prepare($conn, $sql_user);
                    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
                    mysqli_stmt_execute($stmt_user);
                    mysqli_stmt_bind_result($stmt_user, $username);
                    mysqli_stmt_fetch($stmt_user);
                    mysqli_stmt_close($stmt_user);
                    echo "<li class='list-group-item'>$username</li>";
                }
            } else {
                echo "<li class='list-group-item'>No participants yet</li>";
            }
            echo "</ul>";
            // Winner selection form
            echo "<form method='post' action=''>";
            echo "<div class='form-group mt-3'>";
            echo "<label for='winner'>Select Winner:</label>";
            echo "<select class='form-control' id='winner' name='winner'>";
            if (!empty($participants)) {
                foreach ($participants as $participant) {
                    $participant_id = $participant['id'];
                    $user_id = $participant['user_id'];
                    // Fetch user details
                    $sql_user = "SELECT username FROM users WHERE id = ?";
                    $stmt_user = mysqli_prepare($conn, $sql_user);
                    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
                    mysqli_stmt_execute($stmt_user);
                    mysqli_stmt_bind_result($stmt_user, $username);
                    mysqli_stmt_fetch($stmt_user);
                    mysqli_stmt_close($stmt_user);
                    echo "<option value='$participant_id'>$username</option>";
                }
            }
            echo "</select>";
            echo "<input type='hidden' name='offer_id' value='$offer_id'>";
            echo "</div>";
            echo "<button type='submit' class='btn btn-primary'>Choose Winner</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No offers found</p>";
    }
    ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
