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
$uploadDir = '../assets/img/';

// Fetch brand names from tblbrands
$sql_brands = "SELECT id, brandName FROM tblbrands";
$result_brands = $conn->query($sql_brands);

// Check for success message in session and display it
$success = "";
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleTitle = $_POST['vehicleTitle'];
    $brandId = $_POST['brand']; // Use brand ID instead of brand name
    $overview = $_POST['overview'];
    $pricePerDay = $_POST['pricePerDay'];
    $modelYear = $_POST['modelYear'];

    // Validate inputs
    if (empty($vehicleTitle) || empty($brandId) || empty($overview) || empty($pricePerDay) || empty($modelYear)) {
        $error = "All fields are required.";
    } elseif ($pricePerDay < 0) {
        $error = "Price Per Day cannot be negative.";
    } elseif ($modelYear < 0) {
        $error = "Model Year cannot be negative.";
    } else {
        $fileNames = [];
        $fileErrors = [];

        // Handle file uploads
        for ($i = 0; $i < count($_FILES['vehicleImage']['name']); $i++) {
            $file = [
                'name' => $_FILES['vehicleImage']['name'][$i],
                'type' => $_FILES['vehicleImage']['type'][$i],
                'tmp_name' => $_FILES['vehicleImage']['tmp_name'][$i],
                'error' => $_FILES['vehicleImage']['error'][$i],
                'size' => $_FILES['vehicleImage']['size'][$i]
            ];

            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExt, $allowedExtensions)) {
                if ($fileError === 0) {
                    $newFileName = uniqid('', true) . "." . $fileExt;
                    $uploadPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        $fileNames[] = $newFileName;
                    } else {
                        $fileErrors[] = "Error: Could not upload file " . $fileName;
                    }
                } else {
                    $fileErrors[] = "Error: $fileError for file " . $fileName;
                }
            } else {
                $fileErrors[] = "File type not allowed for file " . $fileName . ". Allowed types: jpg, jpeg, png, gif.";
            }
        }

        if (empty($fileErrors)) {
            // Prepare SQL statement
            $sql = "INSERT INTO tblvehicles (VehiclesTitle, VehiclesBrand, VehiclesOverview, PricePerDay, ModelYear, Vimage1, Vimage2, Vimage3, Vimage4, Vimage5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisdisssss", $vehicleTitle, $brandId, $overview, $pricePerDay, $modelYear, 
                                $fileNames[0], $fileNames[1], $fileNames[2], $fileNames[3], $fileNames[4]);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Vehicle posted successfully!";
                // Redirect to the same page to clear form and prevent duplicate submissions
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = "Error: Could not post vehicle.";
            }
            $stmt->close();
        } else {
            $error = implode('<br>', $fileErrors);
        }
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
            <form method="POST" class="post-vehicle-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="vehicleTitle">Vehicle Title</label>
                    <input type="text" name="vehicleTitle" id="vehicleTitle" value="<?php echo isset($vehicleTitle) ? htmlspecialchars($vehicleTitle) : ''; ?>" required>
                </div>
                <div>
                    <label for="brand">Brand</label>
                    <select name="brand" id="brand" required>
                        <option value="">Select Brand</option>
                        <?php while ($row = $result_brands->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($brandId) && $brandId == $row['id']) ? 'selected' : ''; ?>><?php echo $row['brandName']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="full-row">
                    <label for="overview">Overview</label>
                    <textarea name="overview" id="overview" required><?php echo isset($overview) ? htmlspecialchars($overview) : ''; ?></textarea>
                </div>
                <div class="full-row">
                    <label for="pricePerDay">Price Per Day</label>
                    <input type="number" name="pricePerDay" id="pricePerDay" required min="0" value="<?php echo isset($pricePerDay) ? htmlspecialchars($pricePerDay) : ''; ?>">
                </div>
                <div>
                    <label for="modelYear">Model Year</label>
                    <input type="number" name="modelYear" id="modelYear" required min="0" value="<?php echo isset($modelYear) ? htmlspecialchars($modelYear) : ''; ?>">
                </div>
                <div class="full-row">
                    <label for="vehicleImage">Vehicle Images (up to 5)</label>
                    <input type="file" name="vehicleImage[]" id="vehicleImage" multiple required>
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
