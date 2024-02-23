<?php
include('config.php');

$email = "";
$email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);

        // Check if email exists in the database
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Insert forgot password request
            $sql = "INSERT INTO forgot_password (email) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            mysqli_stmt_execute($stmt);

            echo "<script>alert('Forgot password request submitted successfully.Recovered Password Will Be Sent to You in 48 Hours.');</script>";
        } else {
            echo "<script>alert('Email not found in the database.Please Enter Correct Email and Try Again');</script>";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php
    include('navbar.php');
    ?>
    <div class="container mt-5">
        <h2 class="text-center">Forgot Password</h2>
        <p class="text-center">Please enter your email to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" placeholder="Enter Your Email">
                <span><?php echo $email_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" value="Submit" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
