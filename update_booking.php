<?php
require 'config.php';
header('Content-Type: application/json');

// Check for POST parameters
$bookingID = isset($_POST['bookingID']) ? intval($_POST['bookingID']) : null;
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;
$newAddress = isset($_POST['newAddress']) ? trim($_POST['newAddress']) : null;

// Validate Booking ID
if (!$bookingID) {
    echo json_encode(['success' => false, 'error' => 'Invalid booking ID.']);
    exit;
}

// Initialize response array
$response = ['success' => true];

// Update dates if provided
if ($startDate && $endDate) {
    if (!strtotime($startDate) || !strtotime($endDate) || strtotime($startDate) >= strtotime($endDate)) {
        echo json_encode(['success' => false, 'error' => 'Invalid date range.']);
        exit;
    }

    $query = "UPDATE booking SET RentStartDate = ?, RentEndDate = ?, DateChangesCount = DateChangesCount + 1 WHERE BookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $startDate, $endDate, $bookingID);

    if (!$stmt->execute()) {
        $response['success'] = false;
        $response['error'] = 'Failed to update booking dates.';
    }
    $stmt->close();
}

// Update address if provided
if ($newAddress) {
    // Validate address input
    if (empty($newAddress)) {
        echo json_encode(['success' => false, 'error' => 'Address cannot be empty.']);
        exit;
    }

    // Update the address in the database
    $query = "UPDATE booking SET VenueAddress = ?, AddressChangesCount = DateChangesCount + 1 WHERE BookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $newAddress, $bookingID);

    if ($stmt->execute()) {
        $response['message'] = 'Address updated successfully.';
    } else {
        $response['success'] = false;
        $response['error'] = 'Failed to update address.';
    }
    $stmt->close();
}

// Return the response
echo json_encode($response);
?>
