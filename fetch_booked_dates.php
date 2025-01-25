<?php
require 'config.php'; // Include your database connection here

$inventoryID = isset($_GET['inventoryID']) ? $_GET['inventoryID'] : 0;
if ($inventoryID == 0) {
    echo json_encode([]); // Return an empty array if no valid inventory ID
    exit;
}

$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Validate inputs
if ($month < 1 || $month > 12) $month = date('m');
if ($year < 1970) $year = date('Y');

$startOfMonth = date('Y-m-01', strtotime("$year-$month-01"));
$endOfMonth = date('Y-m-t', strtotime("$year-$month-01"));


$sql = "SELECT RentStartDate, RentEndDate FROM booking 
        WHERE InventoryID = ? AND 
        (RentStartDate <= ? AND RentEndDate >= ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $inventoryID, $endOfMonth, $startOfMonth);
$stmt->execute();
$result = $stmt->get_result();

$booked_dates = []; // Initialize the array to store unavailable dates

// Loop through the result set and add unavailable dates
while ($row = $result->fetch_assoc()) {
    $start = strtotime($row['RentStartDate']);
    $end = strtotime($row['RentEndDate']);
    
    // Loop through each date in the range between RentStartDate and RentEndDate
    for ($date = $start; $date <= $end; $date = strtotime("+1 day", $date)) {
        $booked_dates[] = date("Y-m-d", $date); // Add each date to the array
    }
}

// Ensure no duplicate entries in the booked dates array
$booked_dates = array_unique($booked_dates);

// Output the result as JSON
echo json_encode($booked_dates);

?>
