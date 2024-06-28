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

// Initialize error message
$error = "";
$success = "";

// Fetch brand names from tblbrands
$sql_brands = "SELECT id, brandName FROM tblbrands";
$result_brands = $conn->query($sql_brands);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleTitle = $_POST['vehicleTitle'];
    $brandId = $_POST['brand']; // Use brand ID instead of brand name
    $overview = $_POST['overview'];
    $pricePerDay = $_POST['pricePerDay'];
    $fuelType = $_POST['fuelType'];
    $modelYear = $_POST['modelYear'];
    $seatingCapacity = $_POST['seatingCapacity'];

    // Validate inputs
    if (empty($vehicleTitle) || empty($brandId) || empty($overview) || empty($pricePerDay) || empty($fuelType) || empty($modelYear) || empty($seatingCapacity)) {
        $error = "All fields are required.";
    } elseif ($pricePerDay < 0) {
        $error = "Price Per Day cannot be negative.";
    } elseif ($modelYear < 0) {
        $error = "Model Year cannot be negative.";
    } else {
        // Insert vehicle into the database
        $sql = "INSERT INTO tblvehicles (VehiclesTitle, VehiclesBrand, VehiclesOverview, PricePerDay, FuelType, ModelYear, SeatingCapacity) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsdsii", $vehicleTitle, $brandId, $overview, $pricePerDay, $fuelType, $modelYear, $seatingCapacity);

        if ($stmt->execute()) {
            $success = "Vehicle posted successfully!";
            unset($_POST);
        } else {
            $error = "Error: Could not post vehicle.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Vehicle</title>
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
            <h1 class="header">Post a Vehicle</h1>
            <form method="POST" class="post-vehicle-form">
                <div class="form-group">
                    <label for="vehicleTitle">Vehicle Title</label>
                    <input type="text" name="vehicleTitle" id="vehicleTitle" value="" required>
                </div>
                <div>
                    <label for="brand">Brand</label>
                    <select name="brand" id="brand" required>
                        <option value="">Select Brand</option>
                        <?php while ($row = $result_brands->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['brandName']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="full-row">
                    <label for="overview">Overview</label>
                    <textarea name="overview" id="overview" required></textarea>
                </div>
                <div class="full-row">
                    <label for="pricePerDay">Price Per Day</label>
                    <input type="number" name="pricePerDay" id="pricePerDay" required min="0">
                </div>
                <div>
                    <label for="fuelType">Fuel Type</label>
                    <input type="text" name="fuelType" id="fuelType" required>
                </div>
                <div>
                    <label for="modelYear">Model Year</label>
                    <input type="number" name="modelYear" id="modelYear" required min="0">
                </div>
                <div>
                    <label for="seatingCapacity">Seating Capacity</label>
                    <input type="number" name="seatingCapacity" id="seatingCapacity" required>
                </div>
                <div class="full-row">
                    <label for="vehicleImage">Vehicle Image</label>
                    <input type="file" name="vehicleImage" id="vehicleImage" required>
                </div>
                <button type="submit" class="btn btn-primary">Post Vehicle</button>
            </form>
        </section>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Admin Dashboard. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
