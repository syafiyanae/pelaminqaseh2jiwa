<?php
// Start the session
session_start();

// Database connection
require 'config.php';


// Check if the session variable is set
if (!isset($_SESSION['adminID'])) {
    // If not set, redirect the user to the login page
    header("Location: adminlogin.php");
    exit;
}

// Fetch admin details or perform other actions
$adminID = $_SESSION['adminID']; // Now you can use the session variable

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add-bridal-dais') {
        $ItemName = $_POST['ItemName'];
		$InventoryType = $_POST['InventoryType'];
		$DaisType = $_POST['DaisType'];
        $ItemQuantity = $_POST['ItemQty'];
        $ItemStatus = $_POST['ItemStatus'];
		$ItemDescription = $_POST['ItemDesc'];
		$ItemPrice = $_POST['ItemPrice'];
			
        // Validate inputs
        if (!empty($ItemName) && !empty($InventoryType) && !empty($DaisType) && !empty($ItemQuantity) 
			&& !empty($ItemStatus) && !empty($ItemDescription) && !empty($ItemPrice)) {
				// Insert data into the database
				// Insert into the inventory table
				$sql_inventory = "INSERT INTO inventory (InventoryType, ItemQuantity, ItemStatus, Description, Price) 
								  VALUES (?, ?, ?, ?, ?)";
				$stmt_inventory = $conn->prepare($sql_inventory);
				$stmt_inventory->bind_param("sissd", $InventoryType, $ItemQuantity, $ItemStatus, $ItemDescription, $ItemPrice);
				$stmt_inventory->execute();

				// Get the auto-incremented InventoryID
				$inventoryID = $conn->insert_id;

				// Insert into the bridal_dais table
				$sql_bridal_dais = "INSERT INTO bridaldais (InventoryID, ItemName) 
									VALUES (?, ?)";
				$stmt_bridal_dais = $conn->prepare($sql_bridal_dais);
				$stmt_bridal_dais->bind_param("is", $inventoryID, $ItemName);
				$stmt_bridal_dais->execute();

				// Commit transaction
				$conn->commit();

				echo "<script>alert('Item successfully added!'); window.location.href = 'inventory_list.php?tab=dais';</script>";

				// Close the prepared statements
				$stmt_inventory->close();
				$stmt_bridal_dais->close();
            
        } else {
            echo "Error: All fields are required.";
        }
    } elseif ($action === 'add-diymaterial') {
        $ItemName = $_POST['ItemName'];
		$InventoryType = $_POST['InventoryType'];
        $ItemQuantity = $_POST['ItemQty'];
        $ItemStatus = $_POST['ItemStatus'];
		$ItemDescription = $_POST['ItemDesc'];
		$ItemPrice = $_POST['ItemPrice'];
			
        // Validate inputs
        if (!empty($ItemName) && !empty($InventoryType) && !empty($ItemQuantity) 
			&& !empty($ItemStatus) && !empty($ItemDescription) && !empty($ItemPrice)) {
				// Insert data into the database
				// Insert into the inventory table
				$sql_inventory = "INSERT INTO inventory (InventoryType, ItemQuantity, ItemStatus, Description, Price) 
								  VALUES (?, ?, ?, ?, ?)";
				$stmt_inventory = $conn->prepare($sql_inventory);
				$stmt_inventory->bind_param("sissd", $InventoryType, $ItemQuantity, $ItemStatus, $ItemDescription, $ItemPrice);
				$stmt_inventory->execute();

				// Get the auto-incremented InventoryID
				$inventoryID = $conn->insert_id;

				// Insert into the diymaterial table
				$sql_diy_material = "INSERT INTO diymaterial (InventoryID, ItemName) 
									VALUES (?, ?)";
				$stmt_diy_material = $conn->prepare($sql_diy_material);
				$stmt_diy_material->bind_param("is", $inventoryID, $ItemName);
				$stmt_diy_material->execute();

				// Commit transaction
				$conn->commit();

			echo "<script>alert('Item successfully added!'); window.location.href = 'inventory_list.php?tab=diymaterial';</script>";

				// Close the prepared statements
				$stmt_inventory->close();
				$stmt_diy_material->close();
            
        } else {
            echo "Error: All fields are required.";
        }
	}
		elseif ($action === 'add-photobooth') {
		$InventoryType = $_POST['InventoryType'];
        $ItemQuantity = $_POST['ItemQty'];
        $ItemStatus = $_POST['ItemStatus'];
		$ItemDescription = $_POST['ItemDesc'];
		$ItemPrice = $_POST['ItemPrice'];
			
        // Validate inputs
        if (!empty($InventoryType) && !empty($ItemQuantity) && !empty($ItemStatus) && !empty($ItemDescription) && !empty($ItemPrice)) {
				// Insert data into the database
				// Insert into the inventory table
				$sql_inventory = "INSERT INTO inventory (InventoryType, ItemQuantity, ItemStatus, Description, Price) 
								  VALUES (?, ?, ?, ?, ?)";
				$stmt_inventory = $conn->prepare($sql_inventory);
				$stmt_inventory->bind_param("sissd", $InventoryType, $ItemQuantity, $ItemStatus, $ItemDescription, $ItemPrice);
				$stmt_inventory->execute();

				// Get the auto-incremented InventoryID
				$inventoryID = $conn->insert_id;

				// Insert into the photobooth table
				$sql_photobooth = "INSERT INTO photobooth (InventoryID) 
									VALUES (?)";
				$stmt_photobooth = $conn->prepare($sql_photobooth);
				$stmt_photobooth->bind_param("i", $inventoryID);
				$stmt_photobooth->execute();

				// Commit transaction
				$conn->commit();

				echo "<script>alert('Item successfully added!'); window.location.href = 'inventory_list.php?tab=photobooth';</script>";

				// Close the prepared statements
				$stmt_inventory->close();
				$stmt_photobooth->close();
            
        } else {
            echo "Error: All fields are required.";
        }
	}elseif ($action === 'deletebridaldais') {
		$inventoryID = intval($_POST['iddeletedais']); // Ensure the ID is sanitized

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Delete from bridal_dais table
            $sql_delete_bridal_dais = "DELETE FROM bridaldais WHERE InventoryID = ?";
            $stmt_bridal_dais = $conn->prepare($sql_delete_bridal_dais);
            $stmt_bridal_dais->bind_param("i", $inventoryID);
            $stmt_bridal_dais->execute();

            // Delete from inventory table
            $sql_delete_inventory = "DELETE FROM inventory WHERE InventoryID = ?";
            $stmt_inventory = $conn->prepare($sql_delete_inventory);
            $stmt_inventory->bind_param("i", $inventoryID);
            $stmt_inventory->execute();

            // Commit the transaction
            $conn->commit();

            // Provide success feedback
			echo "<script>alert('Item successfully deleted!'); window.location.href = 'inventory_list.php?tab=dais';</script>";

        } catch (Exception $e) {
            // Rollback on failure
            $conn->rollback();
            echo "<script>alert('Error: Unable to delete the item. Please try again.');</script>";
        }
	}elseif ($action === 'deletediymaterial') {
		$inventoryID = intval($_POST['iddeletematerial']); // Ensure the ID is sanitized

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Delete from diymaterial table
            $sql_delete_diy_material = "DELETE FROM diymaterial WHERE InventoryID = ?";
            $stmt_delete_diy_material = $conn->prepare($sql_delete_diy_material);
            $stmt_delete_diy_material->bind_param("i", $inventoryID);
            $stmt_delete_diy_material->execute();

            // Delete from inventory table
            $sql_delete_inventory = "DELETE FROM inventory WHERE InventoryID = ?";
            $stmt_inventory = $conn->prepare($sql_delete_inventory);
            $stmt_inventory->bind_param("i", $inventoryID);
            $stmt_inventory->execute();

            // Commit the transaction
            $conn->commit();

            // Provide success feedback
			echo "<script>alert('Item successfully deleted!'); window.location.href = 'inventory_list.php?tab=diymaterial';</script>";

        } catch (Exception $e) {
            // Rollback on failure
            $conn->rollback();
            echo "<script>alert('Error: Unable to delete the item. Please try again.');</script>";
        }
	}elseif ($action === 'deletephotobooth') {
		$inventoryID = intval($_POST['iddeletephotobooth']); // Ensure the ID is sanitized

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Delete from bridal_dais table
            $sql_delete_photobooth = "DELETE FROM photobooth WHERE InventoryID = ?";
            $stmt_delete_photobooth = $conn->prepare($sql_delete_photobooth);
            $stmt_delete_photobooth->bind_param("i", $inventoryID);
            $stmt_delete_photobooth->execute();

            // Delete from inventory table
            $sql_delete_inventory = "DELETE FROM inventory WHERE InventoryID = ?";
            $stmt_inventory = $conn->prepare($sql_delete_inventory);
            $stmt_inventory->bind_param("i", $inventoryID);
            $stmt_inventory->execute();

            // Commit the transaction
            $conn->commit();

            // Provide success feedback
			echo "<script>alert('Item successfully deleted!'); window.location.href = 'inventory_list.php?tab=photobooth';</script>";

        } catch (Exception $e) {
            // Rollback on failure
            $conn->rollback();
            echo "<script>alert('Error: Unable to delete the item. Please try again.');</script>";
        }
	}
	
	
	if ($action === 'edit') {
        $id = $_POST['id'];
        $ItemName = $_POST['ItemName'];
        $ItemQuantity = $_POST['ItemQty'];
        $ItemStatus = $_POST['ItemStatus'];

        // Validate inputs
        if (!empty($ItemName) && !empty($ItemQuantity) && !empty($ItemStatus)) {
            $stmt = $conn->prepare("UPDATE inventory SET ItemName=?, ItemQuantity=?, ItemStatus=? WHERE InventoryID=?");
            $stmt->bind_param("sisi", $ItemName, $ItemQuantity, $ItemStatus, $id);
            $stmt->execute();
        } else {
            echo "Error: All fields are required.";
        }
    } 
}


// Fetch joined data for bridal dais from inventory and bridaldais tables
$bridaldaisQuery = "
    SELECT 
        inventory.InventoryID, 
        inventory.ItemQuantity, 
        inventory.ItemStatus, 
        inventory.Description, 
        inventory.Price, 
        bridaldais.ItemName, 
        bridaldais.DaisType 
    FROM 
        inventory 
    INNER JOIN 
        bridaldais 
    ON 
        inventory.InventoryID = bridaldais.InventoryID
";

$result = $conn->query($bridaldaisQuery);
$daisResult = $result->fetch_all(MYSQLI_ASSOC);

// Fetch joined data for diy  material from inventory and diy material tables
$diymaterialQuery = "
    SELECT 
        inventory.InventoryID, 
        inventory.ItemQuantity, 
        inventory.ItemStatus, 
        inventory.Description, 
        inventory.Price, 
        diymaterial.ItemName
    FROM 
        inventory 
    INNER JOIN 
        diymaterial 
    ON 
        inventory.InventoryID = diymaterial.InventoryID
";

$result = $conn->query($diymaterialQuery);
$diymaterialResult = $result->fetch_all(MYSQLI_ASSOC);

// Fetch joined data for photobooth from inventory and diy material tables
$photoboothQuery = "
    SELECT 
        inventory.InventoryID,
		inventory.InventoryType,
        inventory.ItemQuantity, 
        inventory.ItemStatus, 
        inventory.Description, 
        inventory.Price,
		photobooth.ItemName
    FROM 
        inventory 
    INNER JOIN 
        photobooth 
    ON 
        inventory.InventoryID = photobooth.InventoryID
";

$result = $conn->query($photoboothQuery);
$photoboothResult = $result->fetch_all(MYSQLI_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bridal Dais Inventory List</title>
	<link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffeaf7;
        }

        .sidebar {
            width: 250px;
            background-color: #f8f8f8;;
            color: black;
            position: fixed;
            height: 100%;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .profile {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar .profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .sidebar .profile h3 {
            margin: 0;
            font-size: 18px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 10px 20px;
            margin: 5px 0;
            cursor: pointer;
        }

        .sidebar ul li a.active{
            background-color:#ff66b3;
			padding:10px;
			border-radius:5px;
			color: white;
        }
	
        .sidebar ul li a {
            text-decoration: none;
            color: black;
            display: block;
			text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4b3b2f;
            margin-top: 20px;
        }
        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #ffeaf7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }

        table th {
            background-color: #ff3385;
        }


        table tr:hover {
            background-color: white;
        }

        button {
            background-color: #ff3385;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
			margin:1%;
        }

        button:hover {
            background-color: #ff66b3;
        }
		
		.add-inventory {
			padding: 20px;
			background: #f9f9f9;
			border: 1px solid #ccc;
			border-radius: 8px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			width: 60%; /* Adjust form width */
			max-width: 600px; /* Limit maximum size */
			margin: 20px auto; /* Center the form */
}

		.add-inventory input,
		.add-inventory select,
		.add-inventory textarea {
		width: 100%; /* Full width of container */
		padding: 10px;
		margin: 10px 0;
		border: 1px solid #ddd;
		border-radius: 4px;
		box-sizing: border-box; /* Include padding in width */
}

		.add-inventory button {
		display: block;
		width: 100%;
		background-color: #ff3385;
		color: white;
		border: none;
		padding: 10px;
		border-radius: 5px;
		font-size: 16px;
		cursor: pointer;
}

		.add-inventory button:hover {
		background-color: #ff66b3;
}

		h1 {
		text-align: center;
		color: #4b3b2f;
		margin-top: 20px;
		font-size: 24px;
}
		.container-inventory {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            max-width: 100%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
		#addInventoryForm {
    transition: all 0.3s ease;
}

    </style>
</head>
<body>
	
    <div class="sidebar">
        <div class="profile">
            <img src="img/icon.jpeg" alt="Admin Profile">
            <h3>Hello, Admin</h3>
        </div>
		<ul>
            <li><a href="#" class="tab-link" data-tab="dais" onclick="showTab('dais')">Bridal Dais Inventory List</a></li>
            <li><a href="#" class="tab-link" data-tab="diymaterial" onclick="showTab('diymaterial')">DIY Materials Inventory List</a></li>
			<li><a href="#" class="tab-link" data-tab="photobooth" onclick="showTab('photobooth')">Photobooth Inventory List</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </div>

    <div class="main-content">
		
		<!-- Bridal Dais Tab -->
		<section id="dais-content" class="tab-content" style="display: none;">
				<h1>Bridal Dais</h1>
				<div class="add-inventory">
					<h2 style="text-align:center; cursor: pointer;" onclick="toggleForm('addInventoryFormBridalDais')">+ Add New Bridal Dais</h2>
					<div id="addInventoryFormBridalDais" style="display: none;">
						<form method="post">
							<input type="hidden" name="action" value="add-bridal-dais">
							<label>Item Name: <input type="text" name="ItemName" required></label>
							<label>Inventory Type:
								<select name="InventoryType" required>
									<option value="Bridal Dais">Bridal Dais</option>
								</select>
							</label>
							<label>Dais Type:
								<select name="DaisType" required>
									<option value="Mini">Mini Dais</option>
									<option value="Canopy">Canopy Dais</option>
									<option value="Hall">Hall Dais</option>
								</select>
							</label>
							<label>Quantity: <input type="number" name="ItemQty" required></label>
							<label>Item Status:
								<select name="ItemStatus" required>
									<option value="Available">Available</option>
									<option value="Not Available">Not Available</option>
								</select>
							</label>
							<label>Description:
								<textarea id="ItemDesc" name="ItemDesc" rows="4" cols="30"></textarea>
							</label>
							<label>Price: <input type="number" name="ItemPrice" required></label>
							<br><button type="submit">Add</button>
						</form>
					</div>
				</div>

				<div class="container-inventory">
					<h2>Inventory List</h2>
					<div class="search-container">
						<h2>Bridal Dais Inventory List</h2>
						<!-- Search, Sort, and Filter for Bridal Dais -->
						<input type="text" id="dais-search" placeholder="Search by item name..." onkeyup="filterTable()">
						<select hidden id="dais-filterType" onchange="applyFilters()">
							<option value="">Filter By Type</option>
							<option value="Bridal Dais">Bridal Dais</option>
							<option value="Photobooth">Photobooth</option>
						</select>
						<select id="dais-sort">
							<option value="">Sort By</option>
							<option value="name">Item Name</option>
							<option value="quantity">Quantity</option>
							<option value="status">Status</option>
							<option value="price">Price</option>
						</select>
						<button onclick="applyFilters()">Apply</button>

						<!-- Bridal Dais Table -->
						<table id="dais-inventory-table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Item Name</th>
									<th>Quantity</th>
									<th>Status</th>
									<th>Description</th>
									<th>Price</th>
									<th>Dais Type</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($daisResult as $item): ?>
									<tr>
										<td><?= $item['InventoryID'] ?></td>
										<td><?= $item['ItemName'] ?></td>
										<td><?= $item['ItemQuantity'] ?></td>
										<td><?= $item['ItemStatus'] ?></td>
										<td><?= $item['Description'] ?></td>
										<td>RM <?= $item['Price'] ?></td>
										<td><?= $item['DaisType'] ?></td>
										<td>
											<form method="post" style="display:inline-block;">
											<input type="hidden" name="iddeletematerial" value="<?= $item['InventoryID'] ?>">
											<input type="hidden" name="action" value="deletediymaterial">
											<button type="submit" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
										</form>
										<button type="submit" onclick="location.href='edit_inventory.php?InventoryID=<?php echo $item['InventoryID'] ?>'">Edit</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

				</div>
			</section>
			
		
            <!-- DIY Materials Tab -->
            <section id="diymaterial-content" class="tab-content" style="display: none;">
                <h1>DIY Materials</h1>
				<div class="add-inventory">
					<h2 style="text-align:center; cursor: pointer;" onclick="toggleForm('addInventoryFormDIY')">+ Add New DIY Material</h2>
					<div id="addInventoryFormDIY" style="display: none;">
						<form method="post">
							<input type="hidden" name="action" value="add-diy-material">
							<label>Item Name: <input type="text" name="ItemName" required></label>
							<label>Inventory Type:
								<select name="InventoryType" required>
									<option value="DIY Material">DIY Material</option>
								</select>
							</label>
							<label>Quantity: <input type="number" name="ItemQty" required></label>
							<label>Item Status:
								<select name="ItemStatus" required>
									<option value="Available">Available</option>
									<option value="Not Available">Not Available</option>
								</select>
							</label>
							<label>Description:
								<textarea id="ItemDesc" name="ItemDesc" rows="4" cols="30"></textarea>
							</label>
							<label>Price: <input type="number" name="ItemPrice" required></label>
							<br><button type="submit">Add</button>
						</form>
					</div>
				</div>
				<div class="container-inventory">
					<h2>Inventory List</h2>
					<div class="search-container">
						<h2>DIY Materials Inventory List</h2>
						<!-- DIY Materials Search, Sort, and Filter -->
						<input type="text" id="diymaterial-search" placeholder="Search by item name..." onkeyup="filterTable()">
						<select hidden id="diymaterial-filterType" onchange="applyFilters()">
							<option value="">Filter By Type</option>
							<option value="Bridal Dais">Bridal Dais</option>
							<option value="Photobooth">Photobooth</option>
						</select>
						<select id="diymaterial-sort">
							<option value="">Sort By</option>
							<option value="name">Item Name</option>
							<option value="quantity">Quantity</option>
							<option value="status">Status</option>
							<option value="price">Price</option>
						</select>
						<button onclick="applyFilters()">Apply</button>

						<!-- DIY Materials Table -->
						<table id="diymaterial-inventory-table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Item Name</th>
									<th>Quantity</th>
									<th>Status</th>
									<th>Description</th>
									<th>Price</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($diymaterialResult as $item): ?>
									<tr>
										<td><?= $item['InventoryID'] ?></td>
										<td><?= $item['ItemName'] ?></td>
										<td><?= $item['ItemQuantity'] ?></td>
										<td><?= $item['ItemStatus'] ?></td>
										<td><?= $item['Description'] ?></td>
										<td>RM <?= $item['Price'] ?></td>
										<td>
											<form method="post" style="display:inline-block;">
											<input type="hidden" name="iddeletematerial" value="<?= $item['InventoryID'] ?>">
											<input type="hidden" name="action" value="deletediymaterial">
											<button type="submit" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
										</form>
										<button type="submit" onclick="location.href='edit_inventory.php?InventoryID=<?php echo $item['InventoryID'] ?>'">Edit</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

				</div>
            </section>
			
			<!-- Photobooth Tab -->
			<section id="photobooth-content" class="tab-content" style="display: none;">
				<h1>Photobooth</h1>
				<div class="add-inventory">
					<h2 style="text-align:center; cursor: pointer;" onclick="toggleForm('addInventoryFormPhotoBooth')">+ Add New Photobooth</h2>
					<div id="addInventoryFormPhotoBooth" style="display: none;">
						<form method="post">
							<input type="hidden" name="action" value="add-photobooth">
							<label>Item Name: <input type="text" name="ItemName" required></label>
							<label>Inventory Type:
								<select name="InventoryType" required>
									<option value="Photobooth">Photobooth</option>
								</select>
							</label>
							<label>Quantity: <input type="number" name="ItemQty" required></label>
							<label>Item Status:
								<select name="ItemStatus" required>
									<option value="Available">Available</option>
									<option value="Not Available">Not Available</option>
								</select>
							</label>
							<label>Description:
								<textarea id="ItemDesc" name="ItemDesc" rows="4" cols="30"></textarea>
							</label>
							<label>Price: <input type="number" name="ItemPrice" required></label>
							<br><button type="submit">Add</button>
						</form>
					</div>
				</div>
				<div class="container-inventory">
				<h2>Inventory List</h2>
				<div class="search-container">
						<h2>Photobooth Inventory List</h2>
						<!-- Photobooth Search, Sort, and Filter -->
						<input type="text" id="photobooth-search" placeholder="Search by item name..." onkeyup="filterTable()">
						<select hidden id="photobooth-filterType" onchange="applyFilters()">
							<option value="">Filter By Type</option>
							<option value="Bridal Dais">Bridal Dais</option>
							<option value="Photobooth">Photobooth</option>
						</select>
						<select id="photobooth-sort">
							<option value="">Sort By</option>
							<option value="name">Item Name</option>
							<option value="quantity">Quantity</option>
							<option value="status">Status</option>
							<option value="price">Price</option>
						</select>
						<button onclick="applyFilters()">Apply</button>

						<!-- Photobooth Table -->
						<table id="photobooth-inventory-table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Item Name</th>
									<th>Quantity</th>
									<th>Status</th>
									<th>Description</th>
									<th>Price</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($photoboothResult as $item): ?>
									<tr>
										<td><?= $item['InventoryID'] ?></td>
										<td><?= $item['ItemName'] ?></td>
										<td><?= $item['ItemQuantity'] ?></td>
										<td><?= $item['ItemStatus'] ?></td>
										<td><?= $item['Description'] ?></td>
										<td>RM <?= $item['Price'] ?></td>
										<td>
											<form method="post" style="display:inline-block;">
											<input type="hidden" name="iddeletematerial" value="<?= $item['InventoryID'] ?>">
											<input type="hidden" name="action" value="deletediymaterial">
											<button type="submit" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
										</form>
										<button type="submit" onclick="location.href='edit_inventory.php?InventoryID=<?php echo $item['InventoryID'] ?>'">Edit</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

				</div>
			</section>

    </div>
	
	<script>
		function sortTable() {
		let table = document.getElementById("inventoryTable");
		let rows = Array.from(table.rows).slice(1);
		let sortBy = document.getElementById("sort").value;
	
		let index = {name: 1, quantity: 2, status: 3, price: 5}[sortBy];

		if (!index) return;

		rows.sort((a, b) => {
        let x = a.cells[index].innerText;
        let y = b.cells[index].innerText;
        return x.localeCompare(y);
		});

		rows.forEach(row => table.appendChild(row));
		}


        function sortTable() {
            let table = document.getElementById("inventoryTable");
            let rows = Array.from(table.rows).slice(1);
            let sortBy = document.getElementById("sort").value;
            let index = {name: 1, quantity: 2, status: 3, price: 5}[sortBy];

            if (!index) return;
            rows.sort((a, b) => {
                let x = a.cells[index].innerText;
                let y = b.cells[index].innerText;
                return index === 2 || index === 5 ? parseFloat(x.replace(/[^\d.-]/g, '')) - parseFloat(y.replace(/[^\d.-]/g, '')) : x.localeCompare(y);
            });
            rows.forEach(row => table.appendChild(row));
        }

        function deleteItem(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                window.location.href = "delete_inventory.php?id=" + id;
            }
        }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab'); // Get the 'tab' parameter from the URL
        if (tab === 'dais') {
            showTab('dais'); // Switch to the "Bridal Dais" tab
        } else if (tab === 'diymaterial') {
            showTab('diymaterial'); // Switch to the "DIY Materials" tab
        }
		else {
            showTab('photobooth'); // Default to "Photobooth" tab
        }
    });

    let currentTab = 'dais'; // Default to bridal dais tab

	function showTab(tabName) {
		// Hide all tabs
		document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');

		// Show the selected tab
		document.getElementById(`${tabName}-content`).style.display = 'block';
		currentTab = tabName; // Update currentTab variable when switching tabs
		
		// Remove active class from all tab links
		const tabLinks = document.querySelectorAll('.tab-link');
		tabLinks.forEach(link => link.classList.remove('active'));
		
		const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
		if (activeTabLink) activeTabLink.classList.add('active');
	}

	function sortTable() {
		let table = document.getElementById(`${currentTab}-inventory-table`);
		let rows = Array.from(table.rows).slice(1);
		let sortBy = document.getElementById(`${currentTab}-sort`).value;
		
		let index = {name: 1, quantity: 2, status: 3, price: 5}[sortBy];

		if (!index) return;

		rows.sort((a, b) => {
			let x = a.cells[index].innerText.trim();
        let y = b.cells[index].innerText.trim();
			
			// If sorting by price, treat it as a number
			if (sortBy === "price") {
				x = parseFloat(x.replace(/[^\d.-]/g, '')); // Remove any non-numeric characters
				y = parseFloat(y.replace(/[^\d.-]/g, ''));
			}

			if (x < y) return -1;
			if (x > y) return 1;
			return 0;
		});

		rows.forEach(row => table.appendChild(row));
	}

	function filterTable() {
		let searchQuery = document.getElementById(`${currentTab}-search`).value.toLowerCase();
		let filterType = document.getElementById(`${currentTab}-filterType`).value;
		
		let table = document.getElementById(`${currentTab}-inventory-table`);
		let rows = table.querySelectorAll("tbody tr");
		
		for (let row of rows) {
			let itemName = row.cells[1]?.innerText.toLowerCase() || '';
			let itemType = row.cells[6]?.innerText.toLowerCase() || '';
			
			let match = itemName.includes(searchQuery) && 
						(filterType === '' || itemType === filterType.toLowerCase());
			
			if (match) {
				row.style.display = '';
			} else {
				row.style.display = 'none';
			}
		}
	}

	function applyFilters() {
		filterTable();  // Apply search and filter
		sortTable();    // Apply sorting
	}

	function toggleForm(formId) {
        const form = document.getElementById(formId);
        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    }

    </script>
</body>
</html>