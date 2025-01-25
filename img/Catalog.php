<?php
// Sample data (in a real application, this would be fetched from a database)
$products = [
    [
        "id" => 111,
        "name" => "Mini Dais Neutral-White - 8ft",
        "description" => "- Shaggy carpet
		- Flower decoration
		- Backdrop
		- Lighting spotlight
		- Dais Chair 4ft",
        "price" => 550.00,
        "image" => '<img src="imej/Big Dais Carpet - White.jpg" alt="">',
		"available" => true
    ],
    [
        "id" => 112,
        "name" => "Mini Dais Rotan Chair - 8ft",
        "description" => "- Shaggy carpet
		- Flower decoration
		- Backdrop
		- Lighting spotlight
		- Dais chair 4ft.",
        "price" => 550.00,
        "image" => '<img src="diy_materials.jpg" alt="DIY Materials">',
        "available" => true
    ],
    [
        "id" => 113,
        "name" => "Bridal Dais Neutral-Beige - 30ft",
        "description" => "- Dais chair
		- Backdrop
		- Flower decoration
		- Stage and carpet
		- Lighting spotlight
		- Walkway decoration
		- Entrance decoration
		- Red carpet 100ft",
        "price" => 4800.00,
        "image" => '<img src="photobooth.jpg" alt="Photobooth">',
        "available" => false
    ],
    [
        "id" => 114,
        "name" => "Bridal Dais White-Gold - 30ft",
        "description" => "- Dais chair
		- Backdrop
		- Flower decoration
		- Stage and carpet
		- Lighting spotlight
		- Walkway decoration
		- Entrance decoration
		- Red carpet 100ft",
        "price" => 4800.00,
        "image" => "wedding_arch.jpg",
        "available" => true
    ],
    [
        "id" => 115,
        "name" => "Bridal Dais White-Cream 30ft:",
        "description" => "- Dais chair
		- Backdrop
		- Flower decoration
		- Stage and carpet
		- Lighting spotlight
		- Walkway decoration
		- Entrance decoration
		- Red carpet 100ft.",
        "price" => 4800.00,
        "image" => "table_decorations.jpg",
        "available" => true
    ],
    [
        "id" => 116,
        "name" => "Bridal Dais Neutral-Beige 20ft",
        "description" => "- Dais chair
		- Backdrop
		- Flower decoration
		- Stage and carpet
		- Lighting spotlight
		- Red carpet 20ft.",
        "price" => 3600.00,
        "image" => "lighting_setup.jpg",
        "available" => false
    ],
    [
        "id" => 117,
        "name" => "Bridal Dais White-Cream 20ft",
        "description" => "- Dais chair
		- Backdrop
		- Flower decoration
		- Stage and carpet
		- Lighting spotlight
		- Red carpet 20ft",
        "price" => 3600.00,
        "image" => "wedding_chairs.jpg",
        "available" => true
    ],
    [
        "id" => 118,
        "name" => "Bridal Dais White-Gold 20ft",
        "description" => "- Dais chair
		- Backdrop
		- Flower decoration
		- Stage and carpet
		- Lighting spotlight
		- Red carpet 20ft",
        "price" => 3600.00,
        "image" => "centerpiece_vases.jpg",
        "available" => true
    ],
    [
        "id" => 310,
        "name" => "Set Of Photobooth",
        "description" => "Camera, Backdop, Photo printer, Monitor, Photobooth operator",
        "price" => 200.00,
        "image" => "floral_arrangements.jpg",
        "available" => true
    ],
    [
        "id" => 2101,
        "name" => "Flower decoration Pink - 8ft",
        "description" => "- Comes with default white flowers.",
        "price" => 250.00,
        "image" => "bridal_bouquet.jpg",
        "available" => true
    ],
    [
        "id" => 2102,
        "name" => "Flower decoration Blue - 8ft",
        "description" => "Comes with default white flowers.",
        "price" => 250.00,
        "image" => "reception_table.jpg",
        "available" => true
    ],
    [
        "id" => 2103,
        "name" => "Flower decoration Purple - 8ft",
        "description" => "Comes with default white flowers.",
        "price" => 250.00,
        "image" => "backdrop_setup.jpg",
        "available" => false
    ],
    [
        "id" => 2104,
        "name" => "Flower decoration Yellow - 8ft",
        "description" => "Comes with default white flowers.",
        "price" => 250.00,
        "image" => "sound_system.jpg",
        "available" => true
    ],
	[
        "id" => 2105,
        "name" => "Flower decoration Green - 8ft",
        "description" => "Comes with default white flowers.",
        "price" => 250.00,
        "image" => "wedding_cake.jpg",
        "available" => false
    ],
    [
        "id" => 2106,
        "name" => "Flower decoration Pink - 17ft",
        "description" => "Comes with default white flowers.",
        "price" => 400.00,
        "image" => "wedding_cake.jpg",
        "available" => false
    ],
    [
        "id" => 2107,
        "name" => "Flower decoration Blue - 17ft",
        "description" => "Comes with default white flowers.",
        "price" => 1000.00,
        "image" => "photographer_service.jpg",
        "available" => true
    ],
    [
        "id" => 2108,
        "name" => "Flower decoration Purple - 17ft",
        "description" => "Comes with default white flowers.",
        "price" => 500.00,
        "image" => "event_planner.jpg",
        "available" => true
    ],
    [
        "id" => 2109,
        "name" => "Flower decoration Yellow - 17ft",
        "description" => "Comes with default white flowers.",
        "price" => 15.00,
        "image" => "party_favors.jpg",
        "available" => true
    ],
    [
        "id" => 2110,
        "name" => "Flower decoration Green - 17ft",
        "description" => "Comes with default white flowers.",
        "price" => 40.00,
        "image" => "guest_book.jpg",
        "available" => true
    ],
    [
        "id" => 2201,
        "name" => "Mini Dais Carpet - White",
        "description" => "",
        "price" => 25.00,
        "image" => "wedding_sparklers.jpg",
        "available" => true
    ],
    [
        "id" => 2202,
        "name" => "Mini Dais Carpet - Cream",
        "description" => "",
        "price" => 100.00,
        "image" => "invitation_cards.jpg",
        "available" => true
    ],
    [
        "id" => 2203,
        "name" => "Big Dais Carpet - White",
        "description" => "",
        "price" => 200.00,
        "image" => "photo_album.jpg",
        "available" => true
    ],
    [
        "id" => 2204,
        "name" => "Big Dais Carpet - White",
        "description" => "",
        "price" => 180.00,
        "image" => "tuxedo_rental.jpg",
        "available" => true
    ],
    [
        "id" => 2301,
        "name" => "Mini Rotan Dais Chair",
        "description" => "",
        "price" => 150.00,
        "image" => "bridal_party_dresses.jpg",
        "available" => true
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bridal Rental Catalog</title>
    <style>
        /* General Page Styling */
        body {
            margin: 0;
            padding: 0;
            background-color: white;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ff99cc; /* Dark pink */
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .header-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .header-nav ul li {
            margin-left: 20px;
        }

        .header-nav ul li a {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            transition: background-color 0.3s;
        }

        .header-nav ul li a:hover {
            background-color: #ff66b3;
            border-radius: 5px;
        }

        /* Product Grid Styling */
        h1 {
            font-size: 4rem;
            font-weight: bold;
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .product-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 columns */
            gap: 20px;
            padding: 40px;
        }

        .product-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-align: center;
        }

        .product-card:hover {
            transform: translateY(-10px);
        }

        .product-image {
            width: 100%;
            height: 250px; /* Adjust height */
            object-fit: cover;
        }

        .product-info {
            padding: 20px;
        }

        .product-info h5 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .product-info p {
            font-size: 1rem;
            color: #666;
        }

        .product-info .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00b300;
            margin-top: 10px;
        }

        .product-info .btn-rent {
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
        }

        .product-info .btn-rent:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">Bridal Rental</div>
    <nav class="header-nav">
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Catalog</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
</header>

<h1>Bridal Rental Catalog</h1>

<div class="container">
    <div class="product-container">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <?php
                // Check if the image exists in the 'images' folder
                $imagePath = "images/" . $product['image'];
                $imageSrc = file_exists($imagePath) ? $imagePath : "images/default.jpg"; // Fallback to default image
                ?>
                <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                <div class="product-info">
                    <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <?php if ($product['available']): ?>
                        <a href="Calender.php?product_id=<?php echo $product['id']; ?>" class="btn-rent">Rent Now</a>
                    <?php else: ?>
                        <button class="btn-rent" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

