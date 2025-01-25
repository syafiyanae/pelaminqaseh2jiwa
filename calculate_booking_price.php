<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function calculateBookingPrice($rentalType, $daisType, $startDate, $endDate, $startTime = null, $endTime = null, $inventoryPrice = 0) {
    $totalPrice = $inventoryPrice;
    $extraFee = 0;
	$photoboothFee = 0;

    try {
        $startDateTime = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);
        $rentalDays = $startDateTime->diff($endDateTime)->days + 1;

        switch ($rentalType) {
            case "DIY material":
                $extraFee = 200 + (50 * $rentalDays);
                break;
            case "Bridal Dais":
                switch ($daisType) {
                    case "Mini":
                        $extraFee = 150 + (100 * $rentalDays);
                        break;
                    case "Canopy":
                        $extraFee = 200 + (100 * $rentalDays);
                        break;
                    case "Hall":
                        $extraFee = 250 + (100 * $rentalDays);
                        break;
                }
                break;
            case "Photobooth":
                if ($startTime && $endTime) {
                    $startTimeObj = DateTime::createFromFormat('H:i', $startTime);
                    $endTimeObj = DateTime::createFromFormat('H:i', $endTime);
                    if ($startTimeObj && $endTimeObj) {
                        $hours = max(3, ceil(($endTimeObj->getTimestamp() - $startTimeObj->getTimestamp()) / 3600));
                        $photoboothFee = 200 * $hours;
                    }
                }
                break;
        }

        
		if ($rentalType == 'Photobooth'){
			$totalPrice = $photoboothFee;
		}else{
		$totalPrice += $extraFee;
		}

        return [
            'success' => true,
            'extraFee' => $extraFee,
            'totalPrice' => $totalPrice,
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rentalType = $_POST['rentalType'] ?? '';
    $daisType = $_POST['daisType'] ?? '';
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $startTime = $_POST['startTime'] ?? null;
    $endTime = $_POST['endTime'] ?? null;
    $inventoryPrice = $_POST['inventoryPrice'] ?? 0;

    file_put_contents('debug_log.txt', print_r($_POST, true), FILE_APPEND); // Log input data

    if ($rentalType && $startDate && $endDate) {
        $result = calculateBookingPrice($rentalType, $daisType, $startDate, $endDate, $startTime, $endTime, $inventoryPrice);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
