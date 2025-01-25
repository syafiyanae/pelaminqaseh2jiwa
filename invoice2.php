<?php
session_start();
require 'config.php';

if (!isset($_SESSION['CustomerID'])) {
    header("Location: Login.php");
    exit();
}

$invoiceType = $_GET['invoice_type'];
$bookingID = $_GET['booking_id'] ?? null;
$invoice = [];
$items = [];

// Function to fetch booking and customer details
function getBookingDetails($conn, $bookingID) {
    $query = "SELECT b.BookingID, c.CustName, c.CustPhoneNum, c.CustEmail, 
              b.RentStartDate, b.RentEndDate, b.BookingTotalPrice, b.VenueAddress 
              FROM booking b
              JOIN customer c ON b.CustomerID = c.CustomerID
              WHERE b.BookingID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $bookingID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($result);
}

// Function to fetch rented items
function getRentedItems($conn, $bookingID) {
    $itemsQuery = "SELECT inv.InventoryType, 
                          COALESCE(bd.ItemName, dm.ItemName, pb.ItemName) AS ItemName, 
                          inv.Description, b.RentStartDate, b.RentEndDate, b.BookingTotalPrice
                   FROM booking b
                   JOIN inventory inv ON b.InventoryID = inv.InventoryID
                   LEFT JOIN bridaldais bd ON inv.InventoryID = bd.InventoryID
                   LEFT JOIN diymaterial dm ON inv.InventoryID = dm.InventoryID
                   LEFT JOIN photobooth pb ON inv.InventoryID = pb.InventoryID
                   WHERE b.BookingID = ?";
    $stmt = mysqli_prepare($conn, $itemsQuery);
    mysqli_stmt_bind_param($stmt, 'i', $bookingID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}

// Function to check and insert invoice if needed
function checkAndInsertInvoice($conn, $bookingID, $invoiceType, $amountToPay) {
    // Check if invoice exists
    $checkInvoiceQuery = "SELECT InvoiceID, TotalAmount, PaidStatus, DateGenerated 
                           FROM invoice WHERE BookingID = ? AND InvoiceType = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $checkInvoiceQuery);
    mysqli_stmt_bind_param($stmt, 'is', $bookingID, $invoiceType);
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);
	
	
    // If no invoice exists, insert it
    if (mysqli_num_rows($checkResult) == 0) {
        $invoiceQuery = "INSERT INTO invoice (BookingID, TotalAmount, InvoiceType, PaidStatus, DateGenerated) 
                         VALUES (?, ?, ?, '', CURDATE())";
        $stmt = mysqli_prepare($conn, $invoiceQuery);
        mysqli_stmt_bind_param($stmt, 'ids', $bookingID, $amountToPay, $invoiceType);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($conn); // Return the new InvoiceID
    } else {
        // If invoice exists, fetch and return InvoiceID
        $invoiceData = mysqli_fetch_assoc($checkResult);
        return $invoiceData['InvoiceID'];
    }
}

// Fetch booking and customer details
if ($bookingID) {
    $bookingDetails = getBookingDetails($conn, $bookingID);
    if (!$bookingDetails) {
        die("No booking found for the provided ID.");
    }

    // Calculate amounts
    $totalPrice = $bookingDetails['BookingTotalPrice'];
    $depositAmount = $totalPrice * 0.5;
    $items = getRentedItems($conn, $bookingID); // Fetch rented items
	
	if ($invoiceType === 'ExtraFee'){
		$amountToPay = 200;
	}else{
		$amountToPay = $depositAmount;
	}
	
	$invoiceID = checkAndInsertInvoice($conn, $bookingID, $invoiceType, $amountToPay);
	
	// Fetch invoice status
	$checkInvoiceStatusQuery = "SELECT PaidStatus FROM invoice WHERE InvoiceID = ? LIMIT 1";
	$stmt = mysqli_prepare($conn, $checkInvoiceStatusQuery);
	mysqli_stmt_bind_param($stmt, 'i', $invoiceID);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$invoiceStatus = mysqli_fetch_assoc($result)['PaidStatus'];

// Check if the invoice is already paid
$isPaid = ($invoiceStatus === 'Paid');


    // Store booking details in variables for use in HTML output
    $custName = $bookingDetails['CustName'];
    $custPhone = $bookingDetails['CustPhoneNum'];
    $custEmail = $bookingDetails['CustEmail'];
    $rentStartDate = $bookingDetails['RentStartDate'];
    $rentEndDate = $bookingDetails['RentEndDate'];
    $venueAddress = $bookingDetails['VenueAddress'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }
        .invoice-container {
            width: 70%;
            margin: 20px auto;
            background: #ffccdd;
            border: 2px solid #ff99bb;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .invoice-header {
            text-align: left;
            margin-bottom: 20px;
			background-color: #ff66b3;
			padding: 20px;
			border-radius: 8px;
        }
        .invoice-header h1 {
            color: white;
            font-size: 36px;
        }
        .invoice-info, .invoice-notes {
            margin-bottom: 20px;
            color: #333;
        }
        .table-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-container th, .table-container td {
            border: 1px solid #ff99bb;
            padding: 8px 12px;
            text-align: left;
        }
        .table-container th {
            background-color: #ff99bb;
            color: white;
        }
        .invoice-total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #d63384;
        }
		.confirmation-actions {
            align-item: center;
        }
		.invoice-actions a {
            background-color: #ff66b3; /* Medium pink */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>Invoice: #<?php echo htmlspecialchars($invoiceID); ?></h1>
        </div>
        <div class="invoice-info">
            <p><strong>Bill To:</strong> <?php echo htmlspecialchars($custName); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($custPhone); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($custEmail); ?></p>
            <p><strong>Date:</strong> <?php echo date('Y-m-d'); ?></p>
            <p><strong>Venue Address:</strong> <?php echo htmlspecialchars($venueAddress); ?></p>
        </div>
        <table class="table-container">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Rent Start Date</th>
                    <th>Rent End Date</th>
                    <th>Booking Total Price</th>
                    <th>Invoice Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bookingID); ?></td>
                        <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                        <td><?php echo htmlspecialchars($item['Description']); ?></td>
                        <td><?php echo htmlspecialchars($rentStartDate); ?></td>
                        <td><?php echo htmlspecialchars($rentEndDate); ?></td>
                        <td><?php echo "RM" . number_format($item['BookingTotalPrice'], 2); ?></td>
                        <td><?php echo htmlspecialchars($invoiceType); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="invoice-notes">
            <p><strong>Notes:</strong> Total amount calculated is already included with service installation charge. Thank you.</p>
        </div>
        <div class="invoice-total">
            <p>Invoice Total: <?php echo "RM" . number_format($amountToPay, 2); ?></p>
        </div>
		
        <div class="invoice-actions">
			<?php if ($isPaid): ?>
				<button disabled style="background-color: grey">Pay Now</button>
			<?php else: ?>
				<a href="Payment2.php?invoice_id=<?php echo $invoiceID; ?>&invoice_type=<?php echo htmlspecialchars($invoiceType); ?>">Pay Now</a>
			<?php endif; ?>
		</div>

    </div>
</body>
</html>
