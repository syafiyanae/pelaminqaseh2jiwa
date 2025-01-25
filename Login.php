<?php
session_start(); // Start the session
require 'config.php'; // Include the database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form input values
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize the inputs
    $username = trim($username);
    $password = trim($password);

    // Query to fetch user from the database
    $query = "SELECT * FROM customer WHERE CustUsername = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashed_password = $row['CustPassword']; // Assuming password is hashed in the DB

            // Verify the entered password against the stored hash
            if (password_verify($password, $hashed_password)) {
                // Set session variables
                $_SESSION['CustomerID'] = $row['CustomerID'];

                // Redirect to the intended page (e.g., 'about.php')
                header("Location: index.php");
                exit;
            } else {
                echo "Invalid login credentials.";
                exit;
            }
        } else {
            echo "Invalid login credentials.";
            exit;
        }

        mysqli_stmt_close($stmt);
    }
}

// Close the database connection
mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
	<link rel="stylesheet" href="styles.css">
    <style>
        body 
		{
		font-family: Arial, sans-serif;
		background-image: url('img/about-bridal.webp');
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
        <h1>BRIDAL DAIS</h1>
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <div class="register-account">
                <a href="Registration.php">New User? Register account</a>
            </div>
			<div class="admin-login">
				<a href="adminlogin.php">Login as Admin</a>
            </div>
        </form>
    </div>
</body>
</html>
