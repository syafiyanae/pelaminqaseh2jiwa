<?php
// Start the session
session_start();

// Database connection
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminID = $_POST['adminID'];
    $adminPassword = $_POST['adminPassword'];

    // Query to check admin credentials
    $sql = "SELECT * FROM admin WHERE AdminID = ? AND AdminPassword = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $adminID, $adminPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Successful login
        $_SESSION['adminID'] = $adminID; // Store admin ID in session
        header("Location: inventory_list.php?tab=dais");
        exit();
    } else {
        // Invalid credentials
        echo "Invalid Admin ID or Password.";
    }
    
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
	<style>
        body 
		{
		font-family: Arial, sans-serif;
		background-image: url('img/header-banner.jpg');
		background-size: cover; /* Ensures the image covers the entire background */
		background-position: center; /* Centers the image */
		background-repeat: no-repeat; /* Prevents the image from repeating */
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
		margin: 0;
		}
        
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Dashboard Login</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="adminID">Admin ID:</label>
                <input type="text" id="adminID" name="adminID" required>
            </div>
            <div class="form-group">
                <label for="adminPassword">Password:</label>
                <input type="password" id="adminPassword" name="adminPassword" required>
            </div>
            <button type="submit" class="btn">Login</button>
			<div class="customer-login">
				<a href="Login.php">Login as Customer</a>
            </div>
        </form>
    </div>
</body>
</html>
