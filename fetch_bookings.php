<?php
require 'config.php'; 

$search = $_GET['search'] ?? '';
$filterStatus = $_GET['filterStatus'] ?? '';
$sortColumn = $_GET['sortColumn'] ?? 'BookingID';
$sortDirection = $_GET['sortDirection'] ?? 'asc';

$allowedColumns = ['BookingID', 'CustName', 'RentStartDate']; // Allow only these columns for sorting
if (!in_array($sortColumn, $allowedColumns)) $sortColumn = 'BookingID';

$sql = "SELECT * FROM bookings WHERE 1=1";

if ($search) {
    $sql .= " AND CustName LIKE ?";
    $params[] = "%$search%";
}

if ($filterStatus) {
    $sql .= " AND BookingStatus = ?";
    $params[] = $filterStatus;
}

$sql .= " ORDER BY $sortColumn $sortDirection";

$stmt = $conn->prepare($sql);
$stmt->execute($params ?? []);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $booking) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($booking['BookingID']) . "</td>";
    echo "<td>" . htmlspecialchars($booking['CustName']) . "</td>";
    echo "<td>" . htmlspecialchars($booking['RentStartDate']) . "</td>";
    // Add other fields similarly
    echo "</tr>";
}
?>
