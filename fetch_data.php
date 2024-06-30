<?php
require("includes/config.php");

// Function to get vehicle name based on ID
function getVehicleName($vehicleId) {
    global $conn;
    $sql = "SELECT VehiclesTitle FROM tblvehicles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['VehiclesTitle'];
    } else {
        return "Unknown"; 
    }
}

// Function to fetch brand names from tblbrands (existing function)
function getBrandNames() {
    global $conn;
    $sql = "SELECT id, brandName FROM tblbrands";
    $result = $conn->query($sql);

    $brands = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $brands[$row['id']] = $row['brandName'];
        }
    }

    return $brands;
}
// function to fetch Username
function getUsername($userId) {
    global $conn; 

    $query = "SELECT FullName FROM tblusers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        return $userData['FullName'];
    } else {
        return "Unknown"; 
    }
}
// Close connection
// $conn->close();
?>
