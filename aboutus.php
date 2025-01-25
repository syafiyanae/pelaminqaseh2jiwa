<?php
require 'config.php';
/*session_start();

// Check if the session variable is set
if (!isset($_SESSION['CustomerID'])) {
    // If not set, redirect the user to the login page
    header("Location: Login.php");
    exit;
}

// Fetch customer details or perform other actions
$customerID = $_SESSION['CustomerID']; // Now you can use the session variable


// Fetch the customer details from the database (optional, for further use)
$query = "SELECT * FROM customer WHERE CustomerID = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, 'i', $customerID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        die("No customer found for the logged-in ID.");
    }

    mysqli_stmt_close($stmt);
} else {
    die("Error preparing the query.");
}

// Close the database connection
mysqli_close($conn);*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Bridal Items Rental System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
	<header class="header">
        <div class="logo" ><a style="color:white" href="index.php">Pelamin Qaseh 2 Jiwa</a></div>
        <nav class="header-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="Catalog.php">Catalog</a></li>
                <li><a href="aboutus.php" class="active">About Us</a></li>
                <li><a href="profilepage.php">Profile</a></li>
					<!--<div class="account-dropdown">
						<button class="account-btn">My Account<i class="fa fa-caret-down"></i></button>
						<div id="accountMenu" class="dropdown-content">
							<a href="profilepage.php">View Profile</a>
							<a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
						</div>
					</div>
                </li>-->
            </ul>
        </nav>
    </header>
	

    <main>
        <!-- About Us Content -->
        <section class="about-content">
            <div class="text-content">
                <h2>Welcome to Pelamin Qaseh 2 Jiwa</h2>
                <p>
                    At Pelamin Qaseh 2 Jiwa, we specialize in providing high-quality bridal items for rent, 
                    ensuring your special day is as magical as you've always imagined. From stunning 
                    bridal dais and photobooths to DIY materials, we offer a wide range of rental 
                    options tailored to meet your needs.
                </p>
                <p>
                    With years of experience in the industry, our mission is to make your wedding 
                    planning process smooth, stress-free, and budget-friendly. Whether you're looking 
                    for elegant designs, creative DIY options, or premium services, we've got you covered.
                </p>
            </div>
            <div class="image-content">
                <img src="img/about-bridal.webp" alt="Pelamin Qaseh 2 Jiwa">
            </div>
        </section>

        <!-- Call to Action -->
        <section class="call-to-action">
            <h2>Plan Your Perfect Day with Us</h2>
            <p>Explore our catalog or contact us to discuss your needs!</p>
            <button onclick="location.href='index.html'">View Catalog</button>
        </section>

        <!-- Why Choose Us -->
        <section class="why-choose-us">
            <h2>Why Choose Pelamin Qaseh 2 Jiwa?</h2>
            <div class="features">
                <div class="feature">
                    <h3>Wide Selection</h3>
                    <p>
                        From modern to traditional designs, find the perfect items for your wedding theme.
                    </p>
                </div>
                <div class="feature">
                    <h3>Affordable Prices</h3>
                    <p>
                        Enjoy competitive rental prices without compromising on quality or elegance.
                    </p>
                </div>
                <div class="feature">
                    <h3>Easy Booking</h3>
                    <p>
                        Use our seamless booking system to reserve your items and track your orders with ease.
                    </p>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="testimonials">
            <h2>What Our Clients Say</h2>
            <div class="testimonial-cards">
                <div class="card">
                    <img src="img/client1.jpg" alt="Client">
                    <p>
                        "Pelamin Qaseh 2 Jiwa made our wedding day unforgettable! The bridal dais was stunning, and the service was exceptional."
                    </p>
                    <h3>Amira & Rizal</h3>
                </div>
                <div class="card">
                    <img src="img/client2.jpg" alt="Client">
                    <p>
                        "We loved the photobooth! Our guests had so much fun, and the pictures turned out amazing!"
                    </p>
                    <h3>Sarah & Daniel</h3>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="footer-content">
                <p>Copyright &copy; 2025 Pelamin Qaseh 2 Jiwa. All rights reserved.</p>
            </div>
        </footer>
    </main>
	
	<script>
		function toggleDropdown() {
			const menu = document.getElementById("accountMenu");
			menu.classList.toggle("show");
		}

		// Close the dropdown if the user clicks outside of it
		window.onclick = function(event) {
			if (!event.target.matches('.account-btn')) {
				const dropdowns = document.getElementsByClassName("dropdown-content");
				for (let i = 0; i < dropdowns.length; i++) {
					const openDropdown = dropdowns[i];
					if (openDropdown.classList.contains('show')) {
						openDropdown.classList.remove('show');
					}
				}
			}
		};


	</script>
</body>
</html>
