<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require("includes/config.php");

// Check if user is logged in
$user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    // Redirect if user is not logged in
    header("Location: login.php");
    exit();
}

// Fetch user details from database
$sql = "SELECT ContactNo, FullName, citizen_img, license_img FROM `bikerental`.`tblusers` WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Initialize variables to prevent undefined index errors
$fullName = isset($row['FullName']) ? $row['FullName'] : '';
$contactNo = isset($row['ContactNo']) ? $row['ContactNo'] : '';
$citizenImg = isset($row['citizen_img']) ? $row['citizen_img'] : '';
$licenseImg = isset($row['license_img']) ? $row['license_img'] : '';

// Function to handle file upload
function uploadFile($fileInput, $uploadDir) {
    $fileName = basename($_FILES[$fileInput]["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allow certain file formats
    $allowedTypes = array('jpg', 'jpeg', 'png', 'bmp', 'webp');
    if (in_array($fileType, $allowedTypes)) {
        // Check if file already exists
        if (!file_exists($targetFilePath)) {
            if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $targetFilePath)) {
                return $fileName;
            }
        }
    }
    return null;
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $newName = $_POST['username'];
    $newContact = $_POST['contact'];
    
    // Handle file uploads only if new files are provided
    $citizenImg = !empty($_FILES['citizen']['name']) ? uploadFile('citizen', './img/') : $citizenImg;
    $licenseImg = !empty($_FILES['license']['name']) ? uploadFile('license', './img/') : $licenseImg;

    $updateSql = "UPDATE `bikerental`.`tblusers`
                SET
                `FullName` = ?,
                `ContactNo` = ?,
                `citizen_img` = ?,
                `license_img` = ?
                WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('ssssi', $newName, $newContact, $citizenImg, $licenseImg, $user_id);

    if ($stmt->execute()) {
        $_SESSION['username'] = $newName; 
        header("Location: profile.php");
        exit();
    } else {
        echo "Failed to update!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities($user); ?> Profile</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-container input[type="text"],
        .form-container input[type="file"],
        .form-container input[type="submit"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include("includes/header.php"); ?>
    <div class="form-container">
        <h2>Update Profile</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="username">Full Name:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlentities($fullName); ?>"><br>
            
            <label for="contact">Contact No:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlentities($contactNo); ?>"><br>
            
            <?php if (empty($citizenImg)): ?>
                <label for="citizen">Citizen Image:</label>
                <input type="file" id="citizen" accept=".jpg,.png,.jpeg,.bmp,.webp" name="citizen"><br>
            <?php endif; ?>
            
            <?php if (empty($licenseImg)): ?>
                <label for="license">License Image:</label>
                <input type="file" id="license" accept=".jpg,.png,.jpeg,.bmp,.webp" name="license"><br>
            <?php endif; ?>
            
            <input type="submit" name="update" value="Update">
        </form>
    </div>
</body>
</html>
