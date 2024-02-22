<?php
// Connect to the database
// $conn = new mysqli("localhost", "u630586235_feliya_db", "Ys]R#*nM8", "u630586235_feliya_db");


// // Connect to the database
$conn = new mysqli("localhost", "root", "", "feliya_db");




if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>