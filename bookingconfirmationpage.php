<?php
require 'config.php'; // Include your database connection file
session_start();

// Get the customer ID from the session
$customerID = $_SESSION['CustomerID'];

// Check if booking ID is passed via GET or session
if (isset($_GET['booking_id']) && !empty($_GET['booking_id'])) {
    $bookingID = $_GET['booking_id'];

    // Query to fetch booking confirmation details
    $query = "
        SELECT
			b.BookingID,
			i.InvoiceID,
            p.PaymentID, 
            c.CustomerID, 
            c.CustName, 
            b.RentalItem, 
            b.RentStartDate, 
            b.RentEndDate, 
            b.BookingTotalPrice AS TotalPrice,
            i.TotalAmount AS Deposit,
            p.PaymentType, 
            p.PaymentStatus 
        FROM booking b
        JOIN customer c ON b.CustomerID = c.CustomerID
        LEFT JOIN invoice i ON b.BookingID = i.BookingID
        LEFT JOIN payment p ON b.BookingID = p.BookingID
        WHERE b.BookingID = ?
    ";

    // Prepare statement for better security
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $bookingID); // 'i' for integer
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        } else {
            die("No booking found for the provided ID.");
        }

        mysqli_stmt_close($stmt);
    } else {
        die("Error preparing the query.");
    }
} else {
    die("Booking ID not provided.");
}

mysqli_close($conn); // Close the database connection
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Booking Confirmation Page -->
    <div class="confirmation-container">
        <section id="booking-confirmation">
            <h1>Booking Confirmation</h1>
            <p>Thank you for booking with us!</p>
            <div class="confirmation-details">
				<p><strong>Booking ID:</strong> #<?php echo $row['BookingID']; ?></p>
				<p><strong>Invoice ID:</strong> #<?php echo $row['InvoiceID']; ?></p>
                <p><strong>Payment ID:</strong> #<?php echo $row['PaymentID']; ?></p>
                <p><strong>Customer ID:</strong> #<?php echo $row['CustomerID']; ?></p>
                <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($row['CustName']); ?></p>
                <p><strong>Item Details:</strong> <?php echo htmlspecialchars($row['RentalItem']); ?></p>
                <p><strong>Rental Dates:</strong> <?php echo $row['RentStartDate'] . " to " . $row['RentEndDate']; ?></p>
                <p><strong>Total Price:</strong> RM <?php echo number_format($row['TotalPrice'], 2); ?></p>
                <p><strong>Deposit:</strong> RM <?php echo number_format($row['Deposit'], 2); ?></p>
                <p><strong>Payment Type:</strong> <?php echo $row['PaymentType']; ?></p>
                <p><strong>Payment Status:</strong> <?php echo $row['PaymentStatus']; ?></p>
            </div>
            <div class="confirmation-actions">
                <a href="index.php">Return to Home</a>
                <a href="profilepage.php?customer_id=<?php echo $row['CustomerID'] ?>&tab=bookings">View Bookings</a>

            </div>
        </section>
    </div>
</body>
</html>
