<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin
if (!isset($_SESSION['admin'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Include database connection and configuration
require("includes/config.php");

// Initialize error and success messages
$error = "";
$success = "";

// Check if the vehicle ID is set in the URL
if (isset($_GET['id'])) {
    $vehicleId = $_GET['id'];

    // Fetch vehicle details from the database
    $sql = "SELECT VehiclesTitle, VehiclesBrand, VehiclesOverview, PricePerDay, ModelYear, vehicle_quantity FROM tblvehicles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();
    $stmt->bind_result($vehicleTitle, $brandId, $overview, $pricePerDay, $modelYear, $vehicleQuantity);
    $stmt->fetch();
    $stmt->close();
} else {
    header("Location: manage-vehicles.php"); // Redirect to manage vehicles page if no ID is provided
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleTitle = $_POST['vehicleTitle'];
    $brandId = $_POST['brand'];
    $overview = $_POST['overview'];
    $pricePerDay = $_POST['pricePerDay'];
    $vehicleQuantity = $_POST['vehicleQuantity'];
    $modelYear = $_POST['modelYear'];

    // Validate inputs
    if (empty($vehicleTitle) || empty($brandId) || empty($overview) || empty($pricePerDay) || empty($modelYear) || empty($vehicleQuantity)) {
        $error = "All fields are required.";
    } elseif ($pricePerDay < 0) {
        $error = "Price Per Day cannot be negative.";
    } elseif ($modelYear < 0) {
        $error = "Model Year cannot be negative.";
    } else {
        // Update vehicle details in the database
        $sql = "UPDATE tblvehicles SET VehiclesTitle = ?, VehiclesBrand = ?, VehiclesOverview = ?, PricePerDay = ?, ModelYear = ?, vehicle_quantity= ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsii", $vehicleTitle, $brandId, $overview, $pricePerDay, $modelYear, $vehicleQuantity, $vehicleId);

        if ($stmt->execute()) {
            $success = "Vehicle updated successfully!";
        } else {
            $error = "Error: Could not update vehicle.";
        }
        $stmt->close();
    }
}

// Fetch brands for the dropdown
function fetchBrands($conn) {
    $brands = [];
    $sql = "SELECT id, BrandName FROM tblbrands";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
    return $brands;
}

$brands = fetchBrands($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="assets/css/style.css?v=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include("includes/header.php"); ?> <!-- Include admin header that manages session start -->
    <div class="containers">
        <div class="message-container">
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
        </div>
        <section class="post-vehicle-section">
            <h1 class="header">Edit Vehicle</h1>
            <form method="POST" class="post-vehicle-form">
                <div class="form-group">
                    <label for="vehicleTitle">Vehicle Title</label>
                    <input type="text" name="vehicleTitle" id="vehicleTitle" value="<?php echo htmlspecialchars($vehicleTitle); ?>" required>
                </div>
                <div class="form-group">
                    <label for="brand">Brand</label>
                    <select name="brand" id="brand" required>
                        <option value="">Select Brand</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>" <?php if ($brand['id'] == $brandId) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($brand['BrandName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-row">
                    <label for="overview">Overview</label>
                    <textarea name="overview" id="overview" required><?php echo htmlspecialchars($overview); ?></textarea>
                </div>
                <div class="form-group full-row">
                    <label for="pricePerDay">Price Per Day</label>
                    <input type="number" name="pricePerDay" id="pricePerDay" value="<?php echo htmlspecialchars($pricePerDay); ?>" required min="0">
                </div>
                <div class="form-group">
                    <label for="modelYear">Model Year</label>
                    <input type="number" name="modelYear" id="modelYear" value="<?php echo htmlspecialchars($modelYear); ?>" required min="0">
                </div>
                <div class="form-group">
                    <label for="vehicleQuantity">Quantity</label>
                    <input type="number" name="vehicleQuantity" id="vehicleQuantity" value="<?php echo htmlspecialchars($vehicleQuantity); ?>" required min="0">
                </div>
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
            </form>
        </section>
    </div>
    
</body>
</html>
