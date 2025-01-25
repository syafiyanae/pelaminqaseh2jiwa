<?php
header('Content-Type: application/json');

require 'config.php';
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch customer and inventory data from the session and POST request
    $customerID = $_SESSION['CustomerID'];
    $inventoryID = intval($_POST['inventoryID']);
    $rentalItem = $_POST['rentalItem'];
    $rentStartDate = $_POST['rentStartDate'];
    $rentEndDate = $_POST['rentEndDate'];
    $venueAddress = $_POST['venueAddress'];
    $rentalType = $_POST['rentalType'];
    $totalPrice = $_POST['bookingTotalPrice'];
    $bookingStatus = 'Pending'; // Default status for a new booking
    
    error_log("Total Price: " . $totalPrice);

    // Validate required fields
    if (empty($customerID) || empty($inventoryID) || empty($rentalItem) || empty($rentStartDate) || empty($rentEndDate) || empty($venueAddress) || empty($rentalType) || empty($totalPrice)) {
        die("All fields are required.");
    } else { // Here it was getting messed up because of improper brace closure

        // Prepare the SQL query
        $query = "
            INSERT INTO booking (CustomerID, InventoryID, RentalItem, RentStartDate, RentEndDate, VenueAddress, RentalType, BookingStatus, BookingTotalPrice)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        // Execute the query
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param(
                "iissssssd",
                $customerID,
                $inventoryID,
                $rentalItem,
                $rentStartDate,
                $rentEndDate,
                $venueAddress,
                $rentalType,
                $bookingStatus,
                $totalPrice
            );

            if ($stmt->execute()) {
                $bookingID = $conn->insert_id; // Retrieve the inserted BookingID
                echo json_encode([
                    "success" => true,
                    "message" => "Booking successful",
                    "bookingID" => $bookingID
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error: Could not complete the booking. " . $stmt->error
                ]);
            }

            $stmt->close();
        } else {
            die("Error preparing the query: " . $conn->error); // Fixed incorrect method call
        }
    } // Make sure this closes the initial "else" block properly.
} else {
    echo "Invalid request method.";
}
?>
