<?php
require 'config.php';
session_start();


// Check if user is logged in
if (!isset($_SESSION['CustomerID'])) {
    // If not logged in, redirect to login page
	echo "<script>
            alert('Please login to view profile.');
            window.location.href = 'Login.php';
          </script>";
    exit();
}

// Get the customer ID from the session
$customerID = $_SESSION['CustomerID'];

// Fetch customer details
$customerQuery = "SELECT * FROM customer WHERE CustomerID = ?";
if ($stmt = mysqli_prepare($conn, $customerQuery)) {
    mysqli_stmt_bind_param($stmt, 'i', $customerID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $customerData = mysqli_fetch_assoc($result);
    } else {
        die("Customer data not found.");
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error preparing the query.");
}

// Fetch bookings for "My Bookings" tab
$bookingQuery = "
    SELECT 
        b.BookingID, 
        b.RentalItem, 
        b.RentStartDate, 
        b.RentEndDate, 
        b.BookingTotalPrice,
        -- Check if deposit is paid
        (SELECT PaidStatus 
         FROM invoice 
         WHERE BookingID = b.BookingID AND InvoiceType = 'Deposit') AS DepositStatus,
        -- Check if settlement is paid
        (SELECT PaidStatus 
         FROM invoice 
         WHERE BookingID = b.BookingID AND InvoiceType = 'Settlement') AS SettlementStatus
    FROM booking b
    WHERE b.CustomerID = ?
";

if ($stmt = mysqli_prepare($conn, $bookingQuery)) {
    mysqli_stmt_bind_param($stmt, 'i', $customerID);
    mysqli_stmt_execute($stmt);
    $bookingResult = mysqli_stmt_get_result($stmt);
    if (!$bookingResult) {
        die("Error fetching bookings.");
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error preparing booking query.");
}

// Fetch invoices for "My Invoices" tab
$invoiceQuery = "
    SELECT 
        i.InvoiceID, 
        i.BookingID,
		i.InvoiceType,
		i.PaidStatus,
		b.BookingTotalPrice,
        i.TotalAmount,
       (b.BookingTotalPrice - i.TotalAmount) AS BalanceDue,
        i.DateGenerated AS InvoiceDate
    FROM invoice i
    JOIN booking b ON i.BookingID = b.BookingID
    WHERE b.CustomerID = ?
";
if ($stmt = mysqli_prepare($conn, $invoiceQuery)) {
    mysqli_stmt_bind_param($stmt, 'i', $customerID);
    mysqli_stmt_execute($stmt);
    $invoiceResult = mysqli_stmt_get_result($stmt);
    if (!$invoiceResult) {
        die("Error fetching bookings.");
    }
    mysqli_stmt_close($stmt);
} else {
    die("Error preparing booking query.");
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab'); // Get the 'tab' parameter from the URL
        if (tab === 'bookings') {
            showTab('bookings'); // Switch to the "My Bookings" tab
        }else if (tab === 'invoices') {
            showTab('invoices'); // Switch to the "My Invoices" tab
        } else {
            showTab('profile'); // Default to "My Profile" tab
        }
    });

    function showTab(tabName) {
    // Hide all tab content
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.style.display = 'none');

    // Remove active class from all tab links
    const tabLinks = document.querySelectorAll('.tab-link');
    tabLinks.forEach(link => link.classList.remove('active'));

    // Show the selected tab content and mark the corresponding tab link as active
    const selectedTab = document.getElementById(`${tabName}-content`);
    if (selectedTab) selectedTab.style.display = 'block';

    const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeTabLink) activeTabLink.classList.add('active');
	}

    function modifyBooking(bookingID) {
        alert('Modify booking functionality for Booking ID: ' + bookingID);
    }

    function payBalance(bookingID, paymentPurpose) {
		alert(`Pay ${paymentPurpose} for Booking ID: ${bookingID}`);
		// Redirect to payment page or process payment logic
		window.location.href = `invoice2.php?booking_id=${bookingID}&invoice_type=${paymentPurpose}`;
	}

    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo" ><a style="color:white" href="index.php">Pelamin Qaseh 2 Jiwa</a></div>
        <nav class="header-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="Catalog.php">Catalog</a></li>
                <li><a href="aboutus.php?customer_id=<?php echo $customerData['CustomerID']; ?>">About Us</a></li>
                <li><a href="profilepage.php?customer_id=<?php echo $customerData['CustomerID']; ?>&tab=profile">Profile</a></li>
            </ul>
        </nav>
    </header>
	

    <div class="profile-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>My Profile</h2>
            <nav>
                <ul>
                    <li><a href="#" class="tab-link" data-tab="profile" onclick="showTab('profile')">My Details</a></li>
                    <li><a href="#" class="tab-link" data-tab="bookings" onclick="showTab('bookings')">My Bookings</a></li>
					<li><a href="#" class="tab-link" data-tab="invoices" onclick="showTab('invoices')">My Invoices</a></li>
                    <li><a href="Logout.php">Log Out</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="profile-content">
            <!-- Profile Tab -->
            <section id="profile-content" class="tab-content">
                <h1>Welcome, <?php echo htmlspecialchars($customerData['CustName']); ?></h1>
                <div class="profile-info">
                    <h3>Your Details</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customerData['CustName']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customerData['CustEmail']); ?></p>
                    <p><strong>Phone:</strong> +60<?php echo htmlspecialchars($customerData['CustPhoneNum']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($customerData['CustAddress']); ?></p>
                </div>
            </section>

            <!-- Bookings Tab -->
			<section id="bookings-content" class="tab-content" style="display: none;">
				<h1>Your Bookings</h1>
				<table class="bookings-table">
					<thead>
						<tr>
							<th>Booking ID</th>
							<th>Item</th>
							<th>Booking Dates</th>
							<th>Total Price</th>
							<th>Deposit Status</th>
							<th>Settlement Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ($bookingResult && mysqli_num_rows($bookingResult) > 0) {
							while ($row = mysqli_fetch_assoc($bookingResult)) {
								$depositStatus = $row['DepositStatus'] === 'Paid' ? "Paid" : "Not Paid";
								$settlementStatus = $row['SettlementStatus'] === 'Paid' ? "Paid" : "Not Paid";

								echo "<tr>";
								echo "<td><a href='bookingconfirmationpage.php?booking_id={$row['BookingID']}'>#{$row['BookingID']}</a></td>";
								echo "<td>{$row['RentalItem']}</td>";
								echo "<td>{$row['RentStartDate']} until {$row['RentEndDate']}</td>";
								echo "<td>RM " . number_format($row['BookingTotalPrice'], 2) . "</td>";
								echo "<td>{$depositStatus}</td>";
								echo "<td>{$settlementStatus}</td>";
								echo "<td>";

								if ($depositStatus === "Not Paid") {
									echo "<button onclick=\"payBalance('{$row['BookingID']}', 'Deposit')\">Pay Deposit</button>";
								} elseif ($depositStatus === "Paid" && $settlementStatus === "Not Paid") {
									echo "<button onclick=\"window.location.href='bookingmodificationpage.php?BookingID={$row['BookingID']}'\">Modify</button>";
									echo "<button onclick=\"payBalance('{$row['BookingID']}', 'Settlement')\">Pay Balance</button>";
								} elseif ($depositStatus === "Paid" && $settlementStatus === "Paid") {
									echo "Fully Paid";
								} 

								echo "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='7'>No bookings found.</td></tr>";
						}
						?>
					</tbody>
				</table>
			</section>

			
			<!-- Invoices Tab -->
			<section id="invoices-content" class="tab-content" style="display: none;">
				<h1>My Invoices</h1>
				<table class="invoices-table">
					<thead>
						<tr>
							<th>Invoice ID</th>
							<th>Booking ID</th>
							<th>Invoice Type</th>
							<th>Booking Total Price</th>
							<th>Amount Paid</th>
							<th>Status</th>
							<th>Invoice Date</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ($invoiceResult && mysqli_num_rows($invoiceResult) > 0) {
							while ($invoice = mysqli_fetch_assoc($invoiceResult)) {
								echo "<tr>";
								echo "<td><a href='invoice2.php?booking_id={$invoice['BookingID']}&invoice_type={$invoice['InvoiceType']}'>#{$invoice['InvoiceID']}</a></td>";
								//echo "<td>#{$invoice['InvoiceID']}</td>";
								echo "<td>#{$invoice['BookingID']}</td>";
								echo "<td>{$invoice['InvoiceType']}</td>";
								echo "<td>RM " . number_format($invoice['BookingTotalPrice'], 2) . "</td>";
								echo "<td>RM " . number_format($invoice['TotalAmount'], 2) . "</td>";
								echo "<td>{$invoice['PaidStatus']}</td>";
								echo "<td>{$invoice['InvoiceDate']}</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='6'>No invoices found.</td></tr>";
						}
						?>
					</tbody>
				</table>
			</section>
        </main>
    </div>
</body>
</html>

