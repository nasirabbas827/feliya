<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Define variables and initialize with empty values
$title = $amount = $description = $daily_profit = "";
$title_err = $amount_err = $description_err = $daily_profit_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title for the package.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate amount
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter the amount for the package.";
    } elseif (!is_numeric(trim($_POST["amount"]))) {
        $amount_err = "Amount must be a number.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description for the package.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate daily profit
    if (empty(trim($_POST["daily_profit"]))) {
        $daily_profit_err = "Please enter the daily profit for the package.";
    } elseif (!is_numeric(trim($_POST["daily_profit"]))) {
        $daily_profit_err = "Daily profit must be a number.";
    } else {
        $daily_profit = trim($_POST["daily_profit"]);
    }

    // Check input errors before inserting into database
    if (empty($title_err) && empty($amount_err) && empty($description_err) && empty($daily_profit_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO packages (title, amount, description, daily_profit) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "siss", $param_title, $param_amount, $param_description, $param_daily_profit);

            // Set parameters
            $param_title = $title;
            $param_amount = $amount;
            $param_description = $description;
            $param_daily_profit = $daily_profit;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to admin dashboard
                header("location: view_packages.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Add Package</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
            <input type="submit" class="btn btn-primary" value="Add Package">
            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
