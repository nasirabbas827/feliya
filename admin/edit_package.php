<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if package ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_packages.php");
    exit;
}

// Initialize variables
$package_id = $_GET['id'];
$title = $amount = $description = $daily_profit = '';
$title_err = $amount_err = $description_err = $daily_profit_err = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate amount
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter an amount.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate daily profit
    if (empty(trim($_POST["daily_profit"]))) {
        $daily_profit_err = "Please enter the daily profit.";
    } else {
        $daily_profit = trim($_POST["daily_profit"]);
    }

    // Check input errors before updating the database
    if (empty($title_err) && empty($amount_err) && empty($description_err) && empty($daily_profit_err)) {
        // Prepare an update statement
        $sql = "UPDATE packages SET title = ?, amount = ?, description = ?, daily_profit = ? WHERE package_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sissi", $param_title, $param_amount, $param_description, $param_daily_profit, $param_package_id);

            // Set parameters
            $param_title = $title;
            $param_amount = $amount;
            $param_description = $description;
            $param_daily_profit = $daily_profit;
            $param_package_id = $package_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to manage_packages.php after successful update
                header("Location: view_packages.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Query to retrieve the current package details
$sql = "SELECT * FROM packages WHERE package_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_package_id);
    $param_package_id = $package_id;
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            // Retrieve values from database and store them in variables
            $title = $row["title"];
            $amount = $row["amount"];
            $description = $row["description"];
            $daily_profit = $row["daily_profit"];
        } else {
            // Package ID not found, redirect to manage_packages.php
            header("Location: view_packages.php");
            exit;
        }
    }
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2>Edit Package</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $package_id; ?>" method="post">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
            <span class="invalid-feedback"><?php echo $title_err; ?></span>
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control <?php echo (!empty($amount_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $amount; ?>">
            <span class="invalid-feedback"><?php echo $amount_err; ?></span>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
            <span class="invalid-feedback"><?php echo $description_err; ?></span>
        </div>
        <div class="form-group">
            <label>Daily Profit</label>
            <input type="number" name="daily_profit" class="form-control <?php echo (!empty($daily_profit_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $daily_profit; ?>">
            <span class="invalid-feedback"><?php echo $daily_profit_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="view_packages.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
