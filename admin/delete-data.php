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
    } elseif ($_POST['action'] === 'deleteBrand' && isset($_POST['brandId'])) {
        $brandId = $_POST['brandId'];
        deleteBrand($brandId);
    } else {
        echo "Invalid action or ID."; // Handle invalid requests
    }
}

// Function to delete a vehicle by ID
function deleteVehicle($vehicleId) {
    global $conn;
    $uploadDir = '../assets/img/';

    // Retrieve image file names from the database
    $stmt = $conn->prepare("SELECT Vimage1, Vimage2, Vimage3, Vimage4, Vimage5 FROM tblvehicles WHERE id = ?");
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();
    $stmt->bind_result($Vimage1, $Vimage2, $Vimage3, $Vimage4, $Vimage5);
    $stmt->fetch();
    $stmt->close();

    // Delete the image files from the server
    $images = [$Vimage1, $Vimage2, $Vimage3, $Vimage4, $Vimage5];
    foreach ($images as $image) {
        if (!empty($image) && file_exists($uploadDir . $image)) {
            unlink($uploadDir . $image);
        }
    }

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

// Function to delete a brand by ID
function deleteBrand($brandId) {
    global $conn;

    // Prepare SQL statement to delete brand
    $stmt = $conn->prepare("DELETE FROM tblbrands WHERE id = ?");
    $stmt->bind_param("i", $brandId);

    // Execute SQL statement
    if ($stmt->execute()) {
        echo "Brand with ID $brandId deleted successfully.";
    } else {
        echo "Error deleting brand.";
    }

    $stmt->close();
}

// Close connection
$conn->close();
?>
