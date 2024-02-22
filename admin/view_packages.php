<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to delete a package
function deletePackage($conn, $package_id) {
    $sql = "DELETE FROM packages WHERE package_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $package_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Check if the delete button is clicked
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    deletePackage($conn, $delete_id);
    // Redirect back to the same page to avoid re-deletion on page refresh
    header("Location: view_packages.php");
    exit;
}

// Fetch all packages from the database
$sql = "SELECT * FROM packages";
$result = mysqli_query($conn, $sql);

// Display packages in a table
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Packages</h2>
    <a href="add_package.php" class="btn btn-primary mb-3 float-right">Add New Package</a>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Package ID</th>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Daily Profile</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['package_id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>" . $row['daily_profit'] . "</td>";
                    echo "<td>";
                    echo "<a href='edit_package.php?id=" . $row['package_id'] . "' class='btn btn-sm btn-info mr-2'>Edit</a>";
                    echo "<a href='view_packages.php?delete_id=" . $row['package_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this package?\")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
