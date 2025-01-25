<?php
require 'config.php'; // Include your database connection file
session_start();

if (isset($_GET['BookingID'])) {
    // Fetch RentStartDate from the database
    $booking_id = $_GET['BookingID'];
    $query = "SELECT RentStartDate FROM booking WHERE BookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id); // Bind BookingID as an integer
    $stmt->execute();
    $dateresult = $stmt->get_result();

    if ($dateresult->num_rows > 0) {
        // Fetch the RentStartDate
        $row = $dateresult->fetch_assoc();
        $rentStartDate = $row['RentStartDate']; // Get the RentStartDate as a string

        // Perform the date comparison
        $today = new DateTime();
        $originalStartDate = new DateTime($rentStartDate); // Convert RentStartDate string to DateTime object

        $interval = $today->diff($originalStartDate);
        $daysDifference = $interval->days;

        if ($daysDifference < 30) {
            echo "<script>
                    alert('You cannot modify a booking that is less than 30 days away from today.');
                    window.location.href = 'profilepage.php';
                  </script>";
            exit;
        } else {
            // Proceed with modification
        }
    } else {
        echo "<script>
                alert('Booking not found.');
                window.location.href = 'profilepage.php';
              </script>";
        exit;
    }
}

// Fetch the booking ID from the GET request (passed when the user clicks the modify button)
if (isset($_GET['BookingID'])) {
    $booking_id = $_GET['BookingID'];

    // Fetch booking details from the database
    $query = "SELECT b.BookingID, b.InventoryID, b.RentStartDate AS currentStartDate, b.RentEndDate AS currentEndDate, b.VenueAddress as currentVenueAddress, b.BookingTotalPrice AS totalPrice, bd.ItemName AS rentalItem
          FROM booking b
          JOIN inventory i ON b.InventoryID = i.InventoryID
          LEFT JOIN bridaldais bd ON i.InventoryID = bd.InventoryID
          WHERE b.BookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id); // Bind booking ID as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch booking details
        $booking = $result->fetch_assoc();
        $rentalItem = $booking['rentalItem'];
		$inventoryID = $booking['InventoryID'];
        $currentStartDate = $booking['currentStartDate'];
        $currentEndDate = $booking['currentEndDate'];
        $totalPrice = $booking['totalPrice'];
        $currentVenueAddress = $booking['currentVenueAddress'];
    } else {
        echo "No booking found with this ID.";
        exit;
    }
} else {
    echo "Invalid booking ID.";
    exit;
}

//Check datechangescount and addresschangescount
if (isset($_GET['BookingID'])) {
    $bookingID = $_GET['BookingID'];
    $query = "SELECT DateChangesCount, AddressChangesCount FROM booking WHERE BookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$bookingID]);
    $bookingresult = $stmt->get_result();

    if ($bookingresult->num_rows > 0) {
        $countquery = $bookingresult->fetch_assoc();
        $dateChanges = $countquery['DateChangesCount'];
        $addressChanges = $countquery['AddressChangesCount'];
    }

    // Prepare variables to control visibility
    $enableCalendar = false;
    $enableAddress = false;

	// if both date changes and address changes count are 0, enable calendar container & address-modification container
    if ($dateChanges == 0 && $addressChanges == 0) {
        $enableCalendar = true;
        $enableAddress = true;
    } elseif ($dateChanges == 0 && $addressChanges == 1) {
        $enableCalendar = false; //enable calendar container only
		echo "<script>
                alert('You have reach the maximum limit of booking changes. No more changes allowed.');
                window.location.href = 'profilepage.php';
              </script>";
    } elseif ($dateChanges == 1 && $addressChanges == 0) {
        $enableCalendar = false; //enable calendar container only
		echo "<script>
                alert('You have reach the maximum limit of booking changes. No more changes allowed.');
                window.location.href = 'profilepage.php';
              </script>";
    } else {
        // Redirect with an alert if no modifications are allowed
        echo "<script>
                alert('You have reach the maximum limit of booking changes. No more changes allowed.');
                window.location.href = 'profilepage.php';
              </script>";
        exit;
    }
} else {
    echo "Error: Modification counts not found.";
    exit;
}

// Assuming a user/session-based system
$currentTotalPrice = $totalPrice; // Example: fetch from database based on user's booking details
echo "<script>var currentTotalPrice = $currentTotalPrice;</script>";


// Calendar Logic
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Ensure valid month and year
if ($month < 1 || $month > 12) $month = date('m');
if ($year < 1970) $year = date('Y');

// Calculate details for the calendar
$firstDayOfMonth = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDayOfMonth);
$startDayOfWeek = date('w', $firstDayOfMonth);

// Focus on dates only within the current displayed month
$startOfMonth = date('Y-m-01', $firstDayOfMonth);
$endOfMonth = date('Y-m-t', $firstDayOfMonth);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Booking</title>
	<link rel="stylesheet" href="styles.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
			background-color: #ffeaf7;
		}
		
    </style>
</head>
<body>
    <div class="modification-container">
		<section id="booking-modification">
			<div class="modification-details">
						<div id="booking-details">
							<h2>1) Booking Details</h2>
							<form>	
								<input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($bookingID); ?>">
								
								<label for="rental_item">Rental Item:</label>
								<input style="background-color:#D3D3D3" type="text" id="rental_item" name="rental_item" value="<?= htmlspecialchars($rentalItem) ?>" readonly>

								<label for="rental_start_date">Current Rental Start Date:</label>
								<input style="background-color:#D3D3D3" type="text" id="rental_start_date" name="rental_start_date" value="<?= htmlspecialchars($currentStartDate) ?>" readonly>
								
								<label for="rental_end_date">Current Rental End Date:</label>
								<input style="background-color:#D3D3D3" type="text" id="rental_end_date" name="rental_end_date" value="<?= htmlspecialchars($currentEndDate) ?>" readonly>
								
								<label for="booking_price">Booking Total Price: RM</label>
								<input style="background-color:#D3D3D3" type="text" id="booking_price" name="booking_price" value="<?= htmlspecialchars($totalPrice, 2) ?>" readonly>

								<label for="venue_address">Venue Address:</label>
								<input style="background-color:#D3D3D3" type="text" id="venue_address" name="venue_address" value="<?= htmlspecialchars($currentVenueAddress) ?>" readonly>
							</form>
						</div>
				
				<div id="date-address-modify">
					<div id="date-selection">
						<h2>2) Modify Booking Dates</h2>
						<div class="date-field">
							<label for="new-start-date">New Booking Date:</label>
							<span id="new-start-date" class="date-placeholder">Select date from calendar</span>
						</div>
						<div class="date-field">
							<label for="new-end-date">New Booking End Date:</label>
							<span id="new-end-date" class="date-placeholder">Select date from calendar</span>
						</div>
					</div>
					<div id="address-modification"  style="display: <?= $enableAddress ? 'block' : 'none'; ?>">
						<h2>3) Modify Venue Address</h2>
						<label for="new-address">New Venue Address:</label>
						<input type="text" id="new-address" class="venue-placeholder" placeholder="Enter new venue address">
						<button onclick="updateExtraFee()">Confirm Address</button>
					</div>
				</div>

				
					
				<div id="extrafee-container">
					<h2>4) Modification Fee</h2>
					<div id="price-details">
						<p>
							Charges: <span id="extra-fee" class="price-placeholder">RM 0.00</span>
						</p>
						<p>
							New Total Price: <span id="new-total-price" class="price-placeholder">RM 0.00</span>
						</p>
						<button id="pay-extra-fee-btn" class="cta-button" disabled>Pay Extra Fee</button>
						
					</div>
				</div>

			</div>
		</section>
			
			<div class="calendar-container" style="display: <?= $enableCalendar ? 'block' : 'none'; ?>">
				<h2>2) Choose New Booking Dates</h2>
				<h3>Item: <?= htmlspecialchars($rentalItem) ?></h3>
				
				<div class="calendar-header">
					<a href="#" class="previous">&#9664; Previous</a>
					<span></span> <!-- The month and year will be dynamically updated by JavaScript -->
					<a href="#" class="next">Next &#9654;</a>
				</div>
				
				<!-- The calendar grid will be dynamically generated by JavaScript -->
				<div class="calendar"></div>
				
				<!--<button onclick="confirmDate()">Confirm Date</button>-->
			</div>
			
			

			
			<!-- Modal HTML -->
				<div id="warningModal" class="modal">
				  <div class="modal-content">
					<span class="close">&times;</span>
					<h2>Booking Modification Terms & Conditions</h2>
					<p>Please note the following before modifying your booking:</p>
					<ol>
						<li>1. Changes to any details can <strong>only be made once</strong>.</li>
						<li>2. Any chanes can only be made up to <strong>one month before the booking date</strong>.</li>
						<li>3. Any changes in booking details may incur <strong>extra fee</strong> up to RM200.</li>
					</ol>
					<p>Do you want to proceed with your changes?</p>
					<button id="confirmButton">Yes, Proceed</button>
					<button id="cancelButton">No, Cancel</button>
				  </div>
				</div>
	</div>

	
			<script>
			
				<!-- Modal JS -->
				  document.addEventListener("DOMContentLoaded", function () {
					const modal = document.getElementById("warningModal");
					const closeButton = document.querySelector(".close");
					const cancelButton = document.getElementById("cancelButton");

					// Display the modal
					modal.style.display = "block";
					
					closeButton.onclick = function () {
					  modal.style.display = "none";
					};
					
					confirmButton.onclick = function () {
					  modal.style.display = "none";
					};
					
					cancelButton.onclick = function () {
						modal.style.display = "none"; // Hide modal
						window.location.href = "profilepage.php"; // Redirect to profile page (ensure this is the correct URL)
					};

					window.onclick = function (event) {
					  if (event.target == modal) {
						modal.style.display = "none";
					  }
					};
				  });

				function confirmDate() {
				if (!selectedStartDate || !selectedEndDate) {
					alert('Please select both a start and end date before confirming.');
					return;
				}

				const bookingID = <?= $booking_id ?>;
				const formData = new URLSearchParams();
				formData.append('bookingID', bookingID);
				formData.append('startDate', selectedStartDate);
				formData.append('endDate', selectedEndDate);

				fetch('update_booking.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: formData.toString()
				})
				.then(response => response.text()) // Use `.text()` for non-JSON responses
				.then(data => {
					try {
						const parsedData = JSON.parse(data);
						if (parsedData.success) {
							alert('Booking dates successfully updated!');
							window.location.reload(); // Reload the page or redirect as needed
						} else {
							alert('Error updating booking: ' + parsedData.error);
						}
					} catch (error) {
						console.error('Response parsing error:', error);
						alert('An unexpected response was received from the server.');
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('An unexpected error occurred.');
				});
			}

			function updateAddress() {
			const bookingID = <?= $booking_id ?>; // Ensure this PHP variable is passed correctly
			const newAddress = document.getElementById('new-address').value.trim();

			if (!newAddress) {
				alert('Please enter a new address before confirming.');
				return;
			}

			const formData = new URLSearchParams();
			formData.append('bookingID', bookingID);
			formData.append('newAddress', newAddress);

			fetch('update_booking.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: formData.toString()
			})
			.then(response => response.text()) // Use `.text()` for non-JSON responses
			.then(data => {
				try {
					const parsedData = JSON.parse(data);
					if (parsedData.success) {
						alert('Address successfully updated!');
						window.location.reload(); // Reload the page or redirect as needed
					} else {
						alert('Error updating address: ' + parsedData.error);
					}
				} catch (error) {
					console.error('Response parsing error:', error);
					alert('An unexpected response was received from the server.');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('An unexpected error occurred.');
			});
		}

				

				let currentMonth = <?= $month ?>;
				let currentYear = <?= $year ?>;


				document.addEventListener("DOMContentLoaded", () => {
				const prevButton = document.querySelector(".calendar-header a.previous");
				const nextButton = document.querySelector(".calendar-header a.next");
				const calendarHeader = document.querySelector(".calendar-header span");
				const calendarContainer = document.querySelector(".calendar");

				let currentMonth = <?= $month ?> - 1; // JavaScript months are 0-based
				let currentYear = <?= $year ?>;

				// Update calendar when month or year changes
				function updateCalendar(month, year) {
					const inventoryID = <?= $inventoryID ?>;
					
					// Fetch booked dates
					fetch(`fetch_booked_dates.php?month=${month + 1}&year=${year}&inventoryID=${inventoryID}`)
						.then(response => response.json())
						.then(bookedDates => {
							// Render the calendar
							renderCalendar(month, year, bookedDates);
						})
						.catch(error => {
							console.error("Error fetching booked dates:", error);
							alert("Failed to load calendar data. Please try again.");
						});
				}

				// Render the calendar for a specific month and year
				function renderCalendar(month, year, bookedDates) {
					// Clear existing calendar content
					calendarContainer.innerHTML = `
						<div>Sun</div>
						<div>Mon</div>
						<div>Tue</div>
						<div>Wed</div>
						<div>Thu</div>
						<div>Fri</div>
						<div>Sat</div>
					`;

					const firstDay = new Date(year, month, 1);
					const lastDay = new Date(year, month + 1, 0); // Last day of the month

					// Add empty slots for days before the first day of the month
					for (let i = 0; i < firstDay.getDay(); i++) {
						const emptyCell = document.createElement("div");
						calendarContainer.appendChild(emptyCell);
					}

					// Add day cells
					for (let day = 1; day <= lastDay.getDate(); day++) {
						const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
						const dayElement = document.createElement("div");
						dayElement.className = "day";
						if (bookedDates.includes(dateStr)) {
							dayElement.classList.add("booked");
						}
						dayElement.dataset.date = dateStr;
						dayElement.textContent = day;
						dayElement.onclick = () => toggleDate(dayElement, dateStr);
						calendarContainer.appendChild(dayElement);
					}

					// Update header
					calendarHeader.textContent = new Date(year, month).toLocaleString("default", {
						month: "long",
						year: "numeric",
					});
				}

				// Navigation logic
				prevButton.addEventListener("click", (event) => {
					event.preventDefault();
					if (currentMonth === 0) {
						currentMonth = 11;
						currentYear--;
					} else {
						currentMonth--;
					}
					updateCalendar(currentMonth, currentYear);
				});

				nextButton.addEventListener("click", (event) => {
					event.preventDefault();
					if (currentMonth === 11) {
						currentMonth = 0;
						currentYear++;
					} else {
						currentMonth++;
					}
					updateCalendar(currentMonth, currentYear);
				});
				
				

				// Initial rendering
				updateCalendar(currentMonth, currentYear);
			});



				
				
				//fvihfe;hf
				
				let selectedStartDate = null; // Track the selected start date
				let selectedEndDate = null; // Track the selected end date

				function toggleDate(element, day) {
					if (element.classList.contains('booked')) {
						alert('This date is unavailable. Please select a different date.');
						return; // Do nothing for booked dates
					}

					// Use the clicked element's data-date attribute directly
					const selectedDate = element.dataset.date;

					console.log(`Selected date: ${selectedDate}, Current month: ${currentMonth}, Current year: ${currentYear}`);

					// First click: Set the start date
					if (!selectedStartDate || (selectedStartDate && selectedEndDate)) {
						selectedStartDate = selectedDate;
						selectedEndDate = null; // Reset the end date
						clearSelections(); // Remove previous selections
						element.classList.add('selected'); // Highlight the selected start date
					}
					// Second click: Set the end date if it's after the start date
					else if (!selectedEndDate && new Date(selectedDate) > new Date(selectedStartDate)) {
						selectedEndDate = selectedDate;
						highlightRange(); // Highlight the range between start and end dates
					}
					// Third click: Reset both dates
					else {
						selectedStartDate = null;
						selectedEndDate = null;
						clearSelections(); // Clear all highlights
					}

					updateSelectedDates(); // Update placeholders
					updateExtraFee(selectedStartDate, selectedEndDate);
					handleBookingChanges(selectedStartDate, selectedEndDate);
					updateDatabaseWithChanges(selectedStartDate, selectedEndDate);
				}

				// Update the extra fee placeholder based on modifications
				function updateExtraFee(selectedStartDate, selectedEndDate) {
					let penaltyFee = 200;  // The fixed penalty fee for modifications
					// If either start date, end date, or address is modified, apply the penalty
					const isDateModified = selectedStartDate !== null && selectedEndDate !== null;
					const isAddressModified = document.getElementById('new-address').value.trim() !== '';

					if (isDateModified || isAddressModified) {
						// Update extra fee and new total price
						document.getElementById('extra-fee').textContent = `RM ${penaltyFee.toFixed(2)}`;
						const newTotalPrice = parseFloat(document.getElementById('booking_price').value) + penaltyFee;
						document.getElementById('new-total-price').textContent = `RM ${newTotalPrice.toFixed(2)}`;
						document.getElementById('pay-extra-fee-btn').disabled = false;  // Enable the payment button
					} else {
						// No modifications, reset fee
						document.getElementById('extra-fee').textContent = `RM 0.00`;
						document.getElementById('new-total-price').textContent = `RM 0.00`;
						document.getElementById('pay-extra-fee-btn').disabled = true;  // Disable payment button
					}
				}

				// Function to update the placeholders with the selected dates
				function updateSelectedDates() {
					document.getElementById('new-start-date').textContent = selectedStartDate || 'Not selected';
					document.getElementById('new-end-date').textContent = selectedEndDate || 'Not selected';
				}

				// Function to highlight the selected date range on the calendar
				function highlightRange() {
				if (selectedStartDate && selectedEndDate) {
					const startDate = new Date(selectedStartDate);
					const endDate = new Date(selectedEndDate);

					// Loop through each day in the range
					for (let date = startDate; date <= endDate; date.setDate(date.getDate() + 1)) {
						const dateStr = date.toISOString().split('T')[0];
						const dayElement = document.querySelector(`.day[data-date="${dateStr}"]`);
						if (dayElement) {
							dayElement.classList.add('in-range');
						}
					}

					// Ensure start and end dates are specifically highlighted
					const startDateElement = document.querySelector(`.day[data-date="${selectedStartDate}"]`);
					const endDateElement = document.querySelector(`.day[data-date="${selectedEndDate}"]`);
					if (startDateElement) startDateElement.classList.add('selected');
					if (endDateElement) endDateElement.classList.add('selected');
				}
			}

			function clearSelections() {
				document.querySelectorAll('.day').forEach(day => {
					day.classList.remove('selected', 'in-range');
				});
			}

			let bookedDates = [];  // This will store all the booked dates
			
			// Function to load booked dates for the current month
			function loadBookedDates(month, year, inventoryID) {
				$.ajax({
					url: 'fetch_booked_dates.php', // Make sure this path is correct
					method: 'GET',
					data: {
						month: month,
						year: year,
						inventoryID: inventoryID
					},
					success: function(response) {
						const bookedDates = JSON.parse(response);
						markBookedDates(bookedDates);
					}
				});
			}

			// Function to mark the booked dates on the calendar
			function markBookedDates(bookedDates) {
				// Loop through all the days in the calendar and mark the ones that are booked
				const allDays = document.querySelectorAll('.calendar .day');
				
				allDays.forEach(function(dayElement) {
					const date = dayElement.getAttribute('data-date');
					
					if (bookedDates.includes(date)) {
						dayElement.classList.add('booked'); // Add 'booked' class if the date is in the booked dates list
						dayElement.onclick = null; // Prevent clicking on booked dates
					}
				});
			}

			// Call this function to load booked dates for the current month
			const inventoryID = <?= $inventoryID ?>; // Use the actual inventory ID
			loadBookedDates(<?= $month ?>, <?= $year ?>, inventoryID);

			
			//wteyebwxecxvbn
			
			
			function handleBookingChanges(selectedStartDate, selectedEndDate) {
			let temporaryChanges = {
				startDate: null,
				endDate: null,
				address: null
			};

			// Update temporary variables with the user's changes
			temporaryChanges.startDate = selectedStartDate;
			temporaryChanges.endDate = selectedEndDate;
			temporaryChanges.address = document.getElementById('new-address').value.trim();

			console.log('Temporary changes stored:', temporaryChanges);
			alert('New Booking Start Date: ' + temporaryChanges.startDate + '\nNew Booking End Date: ' + temporaryChanges.endDate);
			updateExtraFee(); // Recalculate and enable payment button
		}

		document.addEventListener('DOMContentLoaded', () => {
			const bookingID = <?= $booking_id ?>; // Ensure this PHP variable is passed correctly
			document.getElementById('pay-extra-fee-btn').addEventListener('click', async function() {
				const extraFee = parseFloat(document.getElementById('extra-fee').textContent.replace('RM ', ''));
				console.log('Processing payment for extra fee:', extraFee);

				try {
					// First, update the database with the booking changes
					const dbResponse = await updateDatabaseWithChanges();
					console.log('Database Update Response:', dbResponse);

					if (dbResponse.success) {
						// Proceed with payment redirection only after the database is successfully updated
						simulatePayment(bookingID);
					} else {
						alert('Error updating booking: ' + dbResponse.error);
					}
				} catch (error) {
					console.error('Error during the payment process:', error);
				}
			});

			// Simulate a payment gateway (replace with actual implementation)
			function simulatePayment(bookingID) {
				alert(`Pay Extra Fee for Booking ID: ${bookingID}`);
				// Redirect to payment page or process payment logic
				window.location.href = `invoice2.php?booking_id=${bookingID}&invoice_type=ExtraFee`;
			}

			// Send temporary changes to the server to update the database
			async function updateDatabaseWithChanges(selectedStartDate, selectedEndDate) {
				const newStartDate = selectedStartDate; // Assuming this input exists
				const newEndDate = selectedEndDate; // Assuming this input exists
				const newAddress = document.getElementById('new-address').value.trim();

				const formData = new URLSearchParams();
				formData.append('bookingID', bookingID);

				if (newStartDate && newEndDate) {
					formData.append('startDate', newStartDate);
					formData.append('endDate', newEndDate);
				}

				if (newAddress) {
					formData.append('newAddress', newAddress);
				}

				try {
					const response = await fetch('update_booking.php', {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: formData.toString()
					});

					const data = await response.json();
					return data; // Return the server response
				} catch (error) {
					console.error('Error updating booking:', error);
					return { success: false, error: 'An unexpected error occurred while updating the booking.' };
				}
			}
		});


			
			</script>

</body>
</html>
