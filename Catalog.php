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

// Filter and search logic
$filteredProducts = $products;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $typeFilter = isset($_GET['InventoryType']) ? $_GET['InventoryType'] : null;
    $sortOption = isset($_GET['SortBy']) ? $_GET['SortBy'] : null;
    $searchQuery = isset($_GET['Search']) ? strtolower(trim($_GET['Search'])) : null;

    $filteredProducts = array_filter($products, function ($product) use ($typeFilter, $searchQuery) {
        if ($typeFilter !== null && $typeFilter !== '' && $product['InventoryType'] !== $typeFilter) {
            return false;
        }
        if ($searchQuery !== null && $searchQuery !== '') {
            return strpos(strtolower($product['ItemName']), $searchQuery) !== false;
        }
        return true;
    });

    // Sorting logic
    usort($filteredProducts, function ($a, $b) use ($sortOption) {
        if ($sortOption === 'price_asc') {
            return $a['Price'] <=> $b['Price'];
        } elseif ($sortOption === 'price_desc') {
            return $b['Price'] <=> $a['Price'];
        } elseif ($sortOption === 'availability') {
            return strcmp($b['ItemStatus'], $a['ItemStatus']); // Available first
        }
        return 0;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bridal Rental Catalog</title>
    <link rel="stylesheet" href="Catalog.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>

<header class="header">
    <div class="logo" ><a style="color:white" href="index.php">Pelamin Qaseh 2 Jiwa</a></div>
    <nav class="header-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="Catalog.php">Catalog</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="profilepage.php">Profile</a></li>
        </ul>
    </nav>
</header>

<h1 style="font-family: 'Dancing Script', cursive; color: #333;">Pelamin Qaseh 2 Jiwa</h1>

<!-- Filter, Sort & Search Form -->
<div class="filter-form">
    <form method="GET" action="">
        <label>
            Product Type:
            <select name="InventoryType">
                <option value="">All</option>
                <option value="Bridal Dais">Dais</option>
                <option value="Photobooth">Photobooth</option>
                <option value="DIY material">DIY material</option>
            </select>
        </label>
        
        <label>
            Search:
            <input type="text" name="Search" placeholder="Enter product name..." value="<?php echo isset($_GET['Search']) ? htmlspecialchars($_GET['Search']) : ''; ?>">
        </label>

        <label>
            Sort By:
            <select name="SortBy">
                <option value="">None</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="availability">Availability</option>
            </select>
        </label>
        
        <button type="submit">Apply</button>
    </form>
</div>

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

</body>
</html>
