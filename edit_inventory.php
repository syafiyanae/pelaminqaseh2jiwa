<?php
// Database connection
include 'config.php';

// Check if the InventoryID is set
if (isset($_GET['InventoryID'])) {
    $inventoryID = $_GET['InventoryID'];

    // Fetch the inventory details and join with the appropriate subclass to get the ItemName
    $query = "SELECT i.*, 
                     CASE i.InventoryType 
                         WHEN 'Bridal Dais' THEN bd.ItemName 
                         WHEN 'DIY Material' THEN dm.ItemName 
                         WHEN 'Photobooth' THEN pb.ItemName 
                     END AS ItemName,
                     bd.DaisType
              FROM inventory i
              LEFT JOIN bridaldais bd ON i.InventoryID = bd.InventoryID
              LEFT JOIN diymaterial dm ON i.InventoryID = dm.InventoryID
              LEFT JOIN photobooth pb ON i.InventoryID = pb.InventoryID
              WHERE i.InventoryID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $inventoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $inventory = $result->fetch_assoc();
    } else {
        echo "Inventory item not found.";
        exit;
    }
} else {
    echo "No InventoryID provided.";
    exit;
}

// Handle the update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Preserve existing values if fields are not submitted
    $itemName = $_POST['ItemName'] ?? $inventory['ItemName'];
    $itemQuantity = $_POST['ItemQuantity'] ?? $inventory['ItemQuantity'];
    $itemStatus = $_POST['ItemStatus'] ?? $inventory['ItemStatus'];
    $description = $_POST['Description'] ?? $inventory['Description'];
    $price = $_POST['Price'] ?? $inventory['Price'];

    // Update the inventory table
    $updateQuery = "UPDATE inventory SET ItemQuantity = ?, ItemStatus = ?, Description = ?, Price = ? WHERE InventoryID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("issii", $itemQuantity, $itemStatus, $description, $price, $inventoryID);

    if ($updateStmt->execute()) {
        // Update the subclass table based on the inventory type
        switch ($inventory['InventoryType']) {
            case 'Bridal Dais':
                $daisType = $_POST['DaisType'] ?? $inventory['DaisType'];
                $subUpdateQuery = "UPDATE bridaldais SET ItemName = ?, DaisType = ? WHERE InventoryID = ?";
                $subUpdateStmt = $conn->prepare($subUpdateQuery);
                $subUpdateStmt->bind_param("ssi", $itemName, $daisType, $inventoryID);
                break;
            case 'DIY Material':
                $subUpdateQuery = "UPDATE diymaterial SET ItemName = ? WHERE InventoryID = ?";
                $subUpdateStmt = $conn->prepare($subUpdateQuery);
                $subUpdateStmt->bind_param("si", $itemName, $inventoryID);
                break;
            case 'Photobooth':
                $subUpdateQuery = "UPDATE photobooth SET ItemName = ? WHERE InventoryID = ?";
                $subUpdateStmt = $conn->prepare($subUpdateQuery);
                $subUpdateStmt->bind_param("si", $itemName, $inventoryID);
                break;
        }

        if ($subUpdateStmt->execute()) {
            echo "<script>
                    alert('Inventory details updated successfully!');
                  </script>";
            header("Location: inventory_list.php?tab=dais");
            exit;
        } else {
            echo "Error updating subclass details: " . $conn->error;
        }
    } else {
        echo "Error updating inventory: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory</title>
	<style>

	body {
    display: grid; /* Enables flexbox layout */
    justify-content: center; /* Centers content horizontally */
    align-items: center; /* Centers content vertically */
    min-height: 50vh; /* Full viewport height */
    margin: 5; /* Removes default body margins */
    background-color: #ffeaf7; /* Light pinkish background for bridal theme */
}

form {
    width: 100%; /* Full width */
    max-width: 600px; /* Limit maximum size */
    background-color: white;
    border-radius: 10px;
    padding: 60px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: left; /* Align text to the left for better usability */
    box-sizing: border-box; /* Ensure padding is included in width */
}

form input,
form select,
form textarea {
    width: 100%; /* Full width of the container */
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box; /* Include padding and borders in width calculation */
}

form button {
    width: 100%;
    background-color: #ff3385;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

form button:hover {
    background-color: #ff66b3;
}

h1 {
    text-align: center;
    color: #4b3b2f;
    margin-bottom: 20px;
    font-size: 24px;
}


	</style>
</head>
<body>
	<div class="container">
    <h1>Edit Inventory Item</h1>
    <form method="POST">
		<label for="ItemName">Item ID:</label>
        <input type="text" id="ItemID" name="ItemID" disabled style="background-color:#D3D3D3" value="<?php echo htmlspecialchars($inventoryID); ?>" required><br>
		
        <label for="ItemName">Item Name:</label>
        <input type="text" id="ItemName" name="ItemName" value="<?php echo htmlspecialchars($inventory['ItemName']); ?>" required><br>

        <label for="ItemQuantity">Quantity:</label>
        <input type="number" id="ItemQuantity" name="ItemQuantity" value="<?php echo htmlspecialchars($inventory['ItemQuantity']); ?>" required><br>

        <label for="ItemStatus">Item Status:</label>
        <input type="text" id="ItemStatus" name="ItemStatus" value="<?php echo htmlspecialchars($inventory['ItemStatus']); ?>" required><br>

        <label for="Description">Description:</label>
        <textarea id="Description" name="Description" required><?php echo htmlspecialchars($inventory['Description']); ?></textarea><br>

        <label for="Price">Price:</label>
        <input type="number" id="Price" name="Price" step="0.01" value="<?php echo htmlspecialchars($inventory['Price']); ?>" required><br>

        <?php if ($inventory['InventoryType'] == 'Bridal Dais'): ?>
            <label for="DaisType">Dais Type:</label>
            <input type="text" id="DaisType" name="DaisType" value="<?php echo htmlspecialchars($inventory['DaisType']); ?>" required><br>
        <?php endif; ?>

        <button type="submit">Update</button>
    </form>
	
	</div>
</body>
</html>