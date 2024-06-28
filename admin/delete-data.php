<?php
require("includes/config.php");

// Check if action and corresponding ID are set
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'deleteVehicle' && isset($_POST['vehicleId'])) {
        $vehicleId = $_POST['vehicleId'];
        deleteVehicle($vehicleId); // Call deleteVehicle function
    } elseif ($_POST['action'] === 'deleteBooking' && isset($_POST['bookingId'])) {
        $bookingId = $_POST['bookingId'];
        deleteBooking($bookingId); // Call deleteBooking function
    } else {
        echo "Invalid action or ID."; // Handle invalid requests
    }
}

// Function to delete a vehicle by ID
function deleteVehicle($vehicleId) {
    global $conn;

    // Prepare SQL statement to delete vehicle
    $stmt = $conn->prepare("DELETE FROM tblvehicles WHERE id = ?");
    $stmt->bind_param("i", $vehicleId);

    // Execute SQL statement
    if ($stmt->execute()) {
        echo "Vehicle with ID $vehicleId deleted successfully.";
    } else {
        echo "Error deleting vehicle.";
    }

    $stmt->close();
}

// Function to delete a booking by ID
function deleteBooking($bookingId) {
    global $conn;

    // Prepare SQL statement to delete booking
    $stmt = $conn->prepare("DELETE FROM tblbooking WHERE id = ?");
    $stmt->bind_param("i", $bookingId);

    // Execute SQL statement
    if ($stmt->execute()) {
        echo "Booking with ID $bookingId deleted successfully.";
    } else {
        echo "Error deleting booking.";
    }

    $stmt->close();
}

// Close connection
$conn->close();
?>
