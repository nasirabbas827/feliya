<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete request if request ID is provided
if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM forgot_password WHERE id = $delete_id";
    if(mysqli_query($conn, $delete_sql)) {
        header("Location: forgot_password_requests.php");
        exit;
    } else {
        echo "Error deleting request: " . mysqli_error($conn);
    }
}

// Fetch all forgot password requests along with user passwords
$sql = "SELECT fp.id, fp.email,fp.request_time, u.password 
        FROM forgot_password fp 
        INNER JOIN users u ON fp.email = u.email";
$result = mysqli_query($conn, $sql);
$requests = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $requests[] = $row;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password Requests</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2 class="text-center">Forgot Password Requests</h2>
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Date</th>
                    <th>Action</th> <!-- Added Action column -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $key => $request): ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo $request['email']; ?></td>
                        <td><?php echo $request['password']; ?></td>
                        <td><?php echo $request['request_time']; ?></td>
                        <td>
                            <a href="?delete_id=<?php echo $request['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
