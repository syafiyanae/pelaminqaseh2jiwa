<?php
require 'config.php';
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
	$address = $conn->real_escape_string($_POST['address']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Validate input
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Passwords do not match. Please try again.');
                window.history.back(); // Sends the user back to the registration form
              </script>";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert data into the database
        $sql = "INSERT INTO customer (CustName, CustEmail, CustUsername, CustAddress, CustPhoneNum, CustPassword) 
                VALUES ('$full_name', '$email', '$username', '$address', '$phone_number', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            // Get the auto-generated CustomerID
            $customerID = $conn->insert_id;

            // Successfully registered
            echo "<script>
                    alert('Successfully registered! Your CustomerID is $customerID.');
                    window.location.href = 'Login.php'; // Redirect to the login page
                  </script>";
        } else {
            // Show an error message
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account</title>
    <style>
        body {
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

        .container {
            width: 500px;
            background-color: #fff;
            padding: 36px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
		.container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #218838;
        }
        .container a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
	
    <div class="container">
		<h1>BRIDAL DAIS</h1>
        <h2>Register</h2>
        <form action="Registration.php" method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
			<input type="username" name="username" placeholder="Username" required>
			<input type="address" name="address" placeholder="Address" required>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <a href="Login.php">Already have an account? Login here</a>
		
    </div>
</body>
</html>
