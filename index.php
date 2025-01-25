<?php
// Sample data (in a real application, this would be fetched from a database)
require_once 'config.php';

$products = [];

$sql = "SELECT inv.InventoryID, inv.InventoryType, inv.ItemQuantity, inv.ItemStatus, inv.Description, inv.Price, inv.InventoryImage,
                          COALESCE(bd.ItemName, dm.ItemName, pb.ItemName) AS ItemName
                   FROM inventory inv 
                   LEFT JOIN bridaldais bd ON inv.InventoryID = bd.InventoryID
                   LEFT JOIN diymaterial dm ON inv.InventoryID = dm.InventoryID
                   LEFT JOIN photobooth pb ON inv.InventoryID = pb.InventoryID";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo "No products found.";
}

// Filter logic
$filteredProducts = $products;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $typeFilter = isset($_GET['InventoryType']) ? $_GET['InventoryType'] : null;

    $filteredProducts = array_filter($products, function ($product) use ($typeFilter) {
        if ($typeFilter !== null && $typeFilter !== '' && $product['InventoryType'] !== $typeFilter) {
            return false;
        }
        return true;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
	<link rel="stylesheet" href="Catalog.css">
	<style>
        .filter-form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            padding: 10px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .filter-form label {
            font-size: 1.2rem;
            margin-right: 10px;
            color: #333;
        }
        .filter-form select {
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }
        .filter-form button {
            padding: 8px 15px;
            font-size: 1rem;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }
    </style>
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
        <section class="call-to-action" style="background-color:#F5F5F5; color:black;">
            <h2>Our Services</h2>
            <p>We provide various products to be booked on your big day!</p>
        </section>
		
		<section class="why-choose-us">
            <h2>Pelamin Qaseh 2 Jiwa Rental Essentials</h2>
            <div class="features">
                <div class="feature">
                    <h3>Various Range of Bridal Dais</h3>
					
                    <p>
                        From modern to traditional designs, find the perfect items for your wedding theme.
                    </p>
                </div>
                <div class="feature">
                    <h3>DIY Material</h3>
                    <p>
                        DIY your dream wedding!
                    </p>
                </div>
                <div class="feature">
                    <h3>Photobooth</h3>
                    <p>
                        Capture precious moments with your guests with our photobooth package.
                    </p>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="call-to-action">
            <h2>Plan Your Perfect Day with Us</h2>
            <p>Explore our catalog or contact us to discuss your needs!</p>
            <button onclick="location.href='Catalog.php'">View Catalog</button>
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
		
		<!-- Call to Action Catalog -->
        <section class="call-to-action">
            <h2>Catalog</h2>
            <p>Explore our catalog or contact us to discuss your needs!</p>
        </section>
		
		<!-- Catalog -->
		<div class="container">
			<div class="product-container">
				<?php if (count($filteredProducts) > 0): ?>
					<?php foreach ($filteredProducts as $product): ?>
						<div class="product-card">
							<img src="img/<?php echo htmlspecialchars($product['InventoryImage'] . '.png') ?: 'default.png'; ?>" alt="<?php echo htmlspecialchars($product['Description']); ?>" class="product-image">
							<div class="product-info">
								<h5><?php echo htmlspecialchars($product['ItemName']); ?></h5>
								<p><?php echo nl2br(htmlspecialchars($product['Description'])); ?></p>
								<p class="price">RM<?php echo number_format($product['Price'], 2); ?></p>
								<p class="availability">
									<?php echo $product['ItemStatus'] ? "Available" : "Unavailable"; ?>
								</p>
								<?php if ($product['ItemStatus'] == 'Available'): ?>
									<a href="bookingformfinal.php?InventoryID=<?php echo $product['InventoryID']; ?>" class="btn-rent">Rent Now</a>
								<?php else: ?>
									<button class="btn-rent" disabled>Unavailable</button>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<p>No products match your filters.</p>
				<?php endif; ?>
			</div>
		</div>

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
	
</body>
</html>
