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
    $brandId = $_GET['id'];

    // Fetch vehicle details from the database
    $sql = "SELECT BrandName FROM tblbrands WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $brandId);
    $stmt->execute();
    $stmt->bind_result($brandName);
    $stmt->fetch();
    $stmt->close();
} else {
    header("Location: manage-brand.php"); // Redirect to manage vehicles page if no ID is provided
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandName = $_POST['brandName'];

    // Validate inputs
    if (empty($brandName)) {
        $error = "All fields are required.";
    } 
    else {
        // Update vehicle details in the database
        $sql = "UPDATE tblbrands SET BrandName = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $brandName,$brandId);

        if ($stmt->execute()) {
            $success = "Brand updated successfully!";
        } else {
            $error = "Error: Could not update Brand.";
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
                    <label for="brandName">Vehicle Title</label>
                    <input type="text" name="brandName" id="brandName" value="<?php echo htmlspecialchars($brandName); ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
            </form>
        </section>
    </div>
    
</body>
</html>
