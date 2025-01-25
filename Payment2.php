<?php
// Start the session
session_start();

// Database connection
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['CustomerID'])) {
    die("User not logged in.");
}
$customerID = $_SESSION['CustomerID'];

// Get invoice details
$invoiceID = $_POST['invoice_id'] ?? $_GET['invoice_id'] ?? 0;
$invoiceType = $_POST['invoice_type'] ?? $_GET['invoice_type'] ?? '';

if (!$invoiceID || !$invoiceType) {
    die("Invalid invoice data provided.");
}

if (isset($_POST['cancel-btn'])) {
    // Check if the necessary data is available
    $invoiceID = $_POST['invoice_id'] ?? 0;
    $invoiceType = $_POST['invoice_type'] ?? '';

    if (!$invoiceID || !$invoiceType) {
        die("Invalid request.");
    }
	 // If the payment type is 'deposit'
    if (strtolower($invoiceType) === 'deposit') {
        // Retrieve BookingID associated with the InvoiceID
        $query = "SELECT BookingID FROM invoice WHERE InvoiceID = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $invoiceID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $bookingID = $row['BookingID'] ?? 0;

                // Delete the booking and related invoices
                if ($bookingID) {
                    // Delete the booking
                    $deleteBookingQuery = "DELETE FROM booking WHERE BookingID = ?";
                    $deleteInvoiceQuery = "DELETE FROM invoice WHERE InvoiceID = ?";

                    if ($deleteBookingStmt = mysqli_prepare($conn, $deleteBookingQuery)) {
                        mysqli_stmt_bind_param($deleteBookingStmt, 'i', $bookingID);
                        mysqli_stmt_execute($deleteBookingStmt);
                        mysqli_stmt_close($deleteBookingStmt);
                    }

                    if ($deleteInvoiceStmt = mysqli_prepare($conn, $deleteInvoiceQuery)) {
                        mysqli_stmt_bind_param($deleteInvoiceStmt, 'i', $invoiceID);
                        mysqli_stmt_execute($deleteInvoiceStmt);
                        mysqli_stmt_close($deleteInvoiceStmt);
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
        // Redirect to index
        header("Location: index.php");
        exit;
    }

    // If the payment type is 'settlement' or 'extra fee'
    elseif (strtolower($invoiceType) === 'settlement' || strtolower($invoiceType) === 'extrafee') {
        // Simply redirect to the profile page
        header("Location: profilepage.php?customer_id={$_SESSION['CustomerID']}&tab=invoices");
        exit;
    } else {
        die("Unknown invoice type.");
    }
}

// Determine the payment type based on the invoice type
$paymentType = '';
switch (strtolower($invoiceType)) {
    case 'deposit':
        $paymentType = 'deposit';
        break;
    case 'settlement':
        $paymentType = 'balance';
        break;
    case 'extrafee':
        $paymentType = 'extra-fee';
        break;
    default:
        die("Unknown invoice type.");
}

//Deposit Payment
if (isset($_POST['checkout-btn'])){
	$invoiceID = $_POST['invoice_id'] ?? '';
    $invoice_type = $_POST['invoice_type'] ?? '';
	
	
	if ($invoice_type=='Deposit' ) {
		$paymentOption = $_POST['payment_option'] ?? '';

		// Query to fetch booking and customer details
		$query = "SELECT 
				b.BookingID,
				i.InvoiceID, 
				i.InvoiceType, 
				p.PaymentPurpose, 
				p.PaymentType, 
				i.TotalAmount
			  FROM booking b
			  LEFT JOIN invoice i ON b.BookingID = i.BookingID
			  LEFT JOIN payment p ON i.InvoiceID = p.InvoiceID
			  WHERE i.InvoiceID = ?";

		if ($stmt = mysqli_prepare($conn, $query)) {
			mysqli_stmt_bind_param($stmt, 'i', $invoiceID);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$bookingID = $row['BookingID'] ?? '';
				$invoiceID = $row['InvoiceID'] ?? '';
				$invoiceType = $row['InvoiceType'] ?? '';
				$paymentPurpose = $row['PaymentPurpose'] ?? '';
				$paymentType = $row['PaymentType'] ?? '';
				$rentEndDate = $row['RentEndDate'] ?? '';
				$totalAmount = $row['TotalAmount'] ?? 0.0;


				// Insert data into the payment table
				$paymentQuery = "INSERT INTO payment (BookingID, InvoiceID, PaymentPurpose, PaymentType, Amount, PaymentStatus, DateGenerated) 
								 VALUES (?, ?, 'Deposit', ?, ?, 'Successful', CURDATE())";

				if ($paymentStmt = mysqli_prepare($conn, $paymentQuery)) {
					mysqli_stmt_bind_param($paymentStmt, 'iisd', $bookingID, $invoiceID, $paymentOption, $totalAmount);
					mysqli_stmt_execute($paymentStmt);
					
					// Retrieve the auto-incremented PaymentID
					$PaymentID = mysqli_insert_id($conn);
					
					// Insert 
					$invoiceQuery = "UPDATE invoice
									SET PaidStatus = 'Paid'
									WHERE InvoiceID = ?";
				
					if ($invoiceStmt = mysqli_prepare($conn, $invoiceQuery)) {
						mysqli_stmt_bind_param($invoiceStmt, 'i', $invoiceID);
						mysqli_stmt_execute($invoiceStmt);
					mysqli_stmt_close($paymentStmt);
					}
					
					
				} else {
					die("Error preparing the payment query.");
				}
			} else {
				echo "No invoice found for the provided ID.";
			}
			mysqli_stmt_close($stmt);
		} else {
			die("Error preparing the query.");
		}
		sleep(1);
		header("Location: bookingconfirmationpage.php?booking_id=$bookingID");
		exit;
	}
		//Settlement Payment
	if ($invoice_type=='Settlement' ) {
		$paymentOption = $_POST['payment_option'] ?? '';

		// Query to fetch booking and customer details
		$query = "SELECT 
				b.BookingID,
				i.InvoiceID, 
				i.InvoiceType, 
				p.PaymentPurpose, 
				p.PaymentType, 
				i.TotalAmount
			  FROM booking b
			  LEFT JOIN invoice i ON b.BookingID = i.BookingID
			  LEFT JOIN payment p ON i.InvoiceID = p.InvoiceID
			  WHERE i.InvoiceID = ?";

		if ($stmt = mysqli_prepare($conn, $query)) {
			mysqli_stmt_bind_param($stmt, 'i', $invoiceID);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$bookingID = $row['BookingID'] ?? '';
				$invoiceID = $row['InvoiceID'] ?? '';
				$invoiceType = $row['InvoiceType'] ?? '';
				$paymentPurpose = $row['PaymentPurpose'] ?? '';
				$paymentType = $row['PaymentType'] ?? '';
				$rentEndDate = $row['RentEndDate'] ?? '';
				$totalAmount = $row['TotalAmount'] ?? 0.0;


				// Insert data into the payment table
				$paymentQuery = "INSERT INTO payment (BookingID, InvoiceID, PaymentPurpose, PaymentType, Amount, PaymentStatus, DateGenerated) 
								 VALUES (?, ?, 'Settlement', ?, ?, 'Successful', CURDATE())";

				if ($paymentStmt = mysqli_prepare($conn, $paymentQuery)) {
					mysqli_stmt_bind_param($paymentStmt, 'iisd', $bookingID, $invoiceID, $paymentOption, $totalAmount);
					mysqli_stmt_execute($paymentStmt);
					
					// Retrieve the auto-incremented PaymentID
					$PaymentID = mysqli_insert_id($conn);
					// Insert 
					$invoiceQuery = "UPDATE invoice
									SET PaidStatus = 'Paid'
									WHERE InvoiceID = ?";
				
					if ($invoiceStmt = mysqli_prepare($conn, $invoiceQuery)) {
						mysqli_stmt_bind_param($invoiceStmt, 'i', $invoiceID);
						mysqli_stmt_execute($invoiceStmt);
						
						// Insert 
						$bookingQuery = "UPDATE booking
										SET BookingStatus = 'Completed'
										WHERE BookingID = ?";
					
						if ($bookingStmt = mysqli_prepare($conn, $bookingQuery)) {
							mysqli_stmt_bind_param($bookingStmt, 'i', $bookingID);
							mysqli_stmt_execute($bookingStmt);
						
							mysqli_stmt_close($paymentStmt);
						}
				} else {
					die("Error preparing the payment query.");
				}
			} else {
				echo "No invoice found for the provided ID.";
			}
			mysqli_stmt_close($stmt);
		} else {
			die("Error preparing the query.");
		}
		sleep(1);
		header("Location: profilepage.php?customer_id=$customerID&tab=invoices");
		exit;
		}
	}
	
	//Extra Fee Payment
	if ($invoice_type=='ExtraFee' ) {
		$paymentOption = $_POST['payment_option'] ?? '';

		// Query to fetch booking and customer details
		$query = "SELECT 
				b.BookingID,
				b.BookingTotalPrice,
				i.InvoiceID, 
				i.InvoiceType, 
				p.PaymentPurpose, 
				p.PaymentType, 
				i.TotalAmount
			  FROM booking b
			  LEFT JOIN invoice i ON b.BookingID = i.BookingID
			  LEFT JOIN payment p ON i.InvoiceID = p.InvoiceID
			  WHERE i.InvoiceID = ?";

		if ($stmt = mysqli_prepare($conn, $query)) {
			mysqli_stmt_bind_param($stmt, 'i', $invoiceID);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$bookingID = $row['BookingID'] ?? '';
				$invoiceID = $row['InvoiceID'] ?? '';
				$invoiceType = $row['InvoiceType'] ?? '';
				$paymentPurpose = $row['PaymentPurpose'] ?? '';
				$paymentType = $row['PaymentType'] ?? '';
				$rentEndDate = $row['RentEndDate'] ?? '';
				$totalAmount = $row['TotalAmount'] ?? 0.0;
				$extrafee = 200;


				// Insert data into the payment table
				$paymentQuery = "INSERT INTO payment (BookingID, InvoiceID, PaymentPurpose, PaymentType, Amount, PaymentStatus, DateGenerated) 
								 VALUES (?, ?, 'ExtraFee', ?, ?, 'Successful', CURDATE())";

				if ($paymentStmt = mysqli_prepare($conn, $paymentQuery)) {
					mysqli_stmt_bind_param($paymentStmt, 'iisd', $bookingID, $invoiceID, $paymentOption, $extrafee);
					mysqli_stmt_execute($paymentStmt);
					
					// Retrieve the auto-incremented PaymentID
					$PaymentID = mysqli_insert_id($conn);
					
					// Insert 
					$invoiceQuery = "UPDATE invoice
									SET PaidStatus = 'Paid'
									WHERE InvoiceID = ?";
				
					if ($invoiceStmt = mysqli_prepare($conn, $invoiceQuery)) {
						mysqli_stmt_bind_param($invoiceStmt, 'i', $invoiceID);
						mysqli_stmt_execute($invoiceStmt);
					
					
						// Insert 
					$invoiceQuery = "UPDATE booking
                     SET ExtraFee = ?, BookingTotalPrice = BookingTotalPrice + 200
                     WHERE BookingID = ?";
									
					
									
					if ($invoiceStmt = mysqli_prepare($conn, $invoiceQuery)) {
						mysqli_stmt_bind_param($invoiceStmt, 'di', $extrafee, $bookingID);
						mysqli_stmt_execute($invoiceStmt);
					}			
					mysqli_stmt_close($paymentStmt);
					}
				} else {
					die("Error preparing the payment query.");
				}
			} else {
				echo "No invoice found for the provided ID.";
			}
			mysqli_stmt_close($stmt);
		} else {
			die("Error preparing the query.");
		}
		
		sleep(1);
		header("Location: profilepage.php?tab=invoices");
		exit;
	}
	

} else {
	$invoiceID = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : 0;
	$invoice_type = isset($_GET['invoice_type']) ? $_GET['invoice_type'] : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Payment Form</title>
   <style>
       body {
           font-family: 'Arial', sans-serif;
           background-color: #ffeaf7; /* Soft pink background */
           margin: 0;
           padding: 20px;
           display: flex;
           justify-content: center;
           align-items: center;
           height: 100vh;
       }

       .form-container {
           max-width: 600px;
           background: linear-gradient(145deg, #ffffff, #f7e3f3);
           border-radius: 12px;
           box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
           padding: 40px;
           text-align: center;
           transition: transform 0.3s ease;
       }

       .form-container:hover {
           transform: scale(1.10);
       }

       h2 {
           font-size: 24px;
           margin-bottom: 20px;
           color: #ff66b3;
       }

       .form-group {
           margin-bottom: 30px;
           text-align: left;
       }

       .form-group label {
           font-size: 14px;
           font-weight: bold;
           color: #333;
           margin-bottom: 15px;
           display: block;
       }

       .form-group select,
       .form-group input[type="text"],
       .form-group input[type="number"] {
           width: 90%;
           padding: 10px;
           border: 1px solid #ddd;
           border-radius: 6px;
           background-color: #fdfdfd;
           font-size: 14px;
           outline: none;
           transition: border 0.3s ease;
       }

       .form-group select:hover,
       .form-group input:focus {
           border-color: #ff66b3;
       }

       .form-group input[type="submit"] {
           background: linear-gradient(145deg, #ff99cc, #a569bd);
           color: #fff;
           font-weight: bold;
           border: none;
           padding: 12px 20px;
           font-size: 16px;
           border-radius: 6px;
           cursor: pointer;
           width: 100%;
           transition: background 0.3s ease;
       }

       .form-group input[type="submit"]:hover {
           background: linear-gradient( #ff99cc, #ff99cc);
       }

       .form-group input[type="submit"]:active {
           background: #f9f9f9;
       }

       .form-group .back-button {
           background: linear-gradient(145deg, #ff99cc, #f1948a);
           color: #fff;
           border: none;
           padding: 12px 20px;
           border-radius: 6px;
           font-size: 16px;
           font-weight: bold;
           cursor: pointer;
           width: 100%;
           margin-top: 10px;
           transition: background 0.3s ease;
       }

       .form-group .back-button:hover {
           background: linear-gradient(145deg, #f1948a, #ff99cc);
       }

       .card-details, .bank-options {
           display: none;
           margin-top: 20px;
           padding: 20px;
           border: 1px solid #ddd;
           border-radius: 8px;
           background: #fdfdfd;
       }
	   
	    .success-bubble {
           display: none;
           position: fixed;
           top: 50%;
           left: 50%;
           transform: translate(-50%, -50%);
           padding: 20px 30px;
           background-color: #f9f9f9;
           color: pink;
           font-size: 18px;
           font-weight: bold;
           text-align: center;
           border-radius: 10px;
           box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
           z-index: 1000;
       }

       .success-bubble.show {
           display: block;
           animation: fadeOut 3s forwards;
       }

       @keyframes fadeOut {
           0% {
               opacity: 1;
           }
           80% {
               opacity: 1;
           }
           100% {
               opacity: 0;
           }
       }
   </style>
   <script>
       
	   function toggleDetails() {
           const paymentOption = document.getElementById("payment-option").value;
           const cardDetails = document.getElementById("card-details");
           const bankOptions = document.getElementById("bank-options");

           if (paymentOption === "credit card") {
               cardDetails.style.display = "block";
               bankOptions.style.display = "none";
           } else if (paymentOption === "online banking") {
               cardDetails.style.display = "none";
               bankOptions.style.display = "block";
           } else {
               cardDetails.style.display = "none";
               bankOptions.style.display = "none";
           }
       }
	   
	   function showSuccessBubble(event) {
           event.preventDefault(); // Prevent form submission

           const successBubble = document.getElementById("success-bubble");
           successBubble.classList.add("show");

           // Automatically hide the bubble after 3 seconds
           setTimeout(() => {
               successBubble.classList.remove("show");
               // Optionally, redirect to another page after the success message
               // window.location.href = "thank_you.php";
           }, 3000);
       }
	   
	  
    function cancelPayment() {
        const confirmation = confirm("Are you sure? The payment will be canceled.");
        if (confirmation) {
            window.location.href = "index.php";
        }
    }
</script>

</head>
<body>
   <div class="form-container">
       <h2>Payment</h2>
		   
       <form action="Payment2.php" method="POST">
            <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoiceID); ?>">
			<input type="hidden" name="invoice_type" value="<?php echo htmlspecialchars($invoiceType); ?>">
			<input type="hidden" name="payment_type" value="<?php echo htmlspecialchars($paymentType); ?>">

            <!-- Display Payment Type -->
            <div class="form-group">
                <label>Payment Purpose</label>
                <p><?php echo ucfirst($paymentType); ?></p>
            </div>

            <!-- Payment Option -->
            <div class="form-group">
                <label for="payment-option">Payment Type</label>
                <select id="payment-option" name="payment_option" onchange="toggleDetails()">
                    <option value="ewallet">E-Wallet</option>
                    <option value="credit card">Credit/Debit Card</option>
                    <option value="online banking">Online Banking</option>
                </select>
            </div>

            <!-- Card Details Section -->
            <div id="card-details" class="card-details">
                <div class="form-group">
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" name="card_number" maxlength="16" placeholder="xxxx xxxx xxxx xxxx">
                </div>
                <div class="form-group">
                    <label for="expiry-date">Expiry Date (MM/YY)</label>
                    <input type="text" id="expiry-date" name="expiry_date" maxlength="5" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="number" id="cvv" name="cvv" maxlength="3" placeholder="">
                </div>
                <div class="form-group">
                    <label for="cardholder-name">Name on Card</label>
                    <input type="text" id="cardholder-name" name="cardholder_name" placeholder="">
                </div>
            </div>

            <!-- Bank Options Section -->
            <div id="bank-options" class="bank-options">
                <div class="form-group">
                    <label for="bank-name">Bank</label>
                    <select id="bank-name" name="bank_name">
                        <option value="bank_a">CIMB Bank</option>
                        <option value="bank_b">Bank Islam</option>
                        <option value="bank_c">Maybank</option>
						<option value="bank_d">Hong Leong Bank</option>
                    </select>
                </div>
            </div>
			<div class="form-group">
				<input type="submit" name="checkout-btn" value="Check Out" onclick="showSuccessBubble()">
				<input type="submit" name="cancel-btn" value="Cancel Payment" class="back-button" onclick="cancelPayment()">
			</div>
        </form>

</div>


   <!-- Success Bubble -->
   <div id="success-bubble" class="success-bubble">
       Successfully Payment!
   </div>
   
</body>
</html>