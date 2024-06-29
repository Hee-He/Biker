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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandName = $_POST['brandName'];

    // Validate inputs
    if (empty($brandName)) {
        $error = "All fields are required.";
    } else {
        // Insert brand into the database
        $sql = "INSERT INTO tblbrands (BrandName) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $brandName);

        if ($stmt->execute()) {
            $success = "Brand posted successfully!";
            unset($_POST);
        } else {
            $error = "Error: Could not post brand.";
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
    <title>Post a Brand</title>
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
            <h1 class="header">Post a Brand</h1>
            <form method="POST" class="post-vehicle-form">
                <div class="form-group">
                    <label for="vehicleTitle">Brand Title</label>
                    <input type="text" name="brandName" id="vehicleTitle" value="" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Post Brand</button>
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
