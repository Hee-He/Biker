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

// Initialize error message
$error = "";

// Check for success message in session and display it
$success = "";
if (isset($_SESSION['message'])) {
    $success = $_SESSION['message'];
    unset($_SESSION['message']);
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

    // Prepare SQL query for update
    $updateSql = "UPDATE `bikerental`.`tblusers`
                SET
                `FullName` = ?,
                `ContactNo` = ?";

    // Append image fields to update query only if they are not empty
    $params = array($newName, $newContact);
    if (!empty($citizenImg)) {
        $updateSql .= ", `citizen_img` = ?";
        $params[] = $citizenImg;
    }
    if (!empty($licenseImg)) {
        $updateSql .= ", `license_img` = ?";
        $params[] = $licenseImg;
    }

    $updateSql .= " WHERE id = ?";
    $params[] = $user_id;

    // Execute update query
    $stmt = $conn->prepare($updateSql);
    if ($stmt) {
        // Dynamically bind parameters based on the presence of image updates
        $types = str_repeat('s', count($params)); // Generate type string
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['username'] = $newName;
            $_SESSION['message'] = "Profile Update successfully!";
            header("Location: profile.php");
            exit();
        } else {
            $error =  "Failed To Update profile";
        }
    } else {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities($user); ?> Profile</title>
    <link rel="stylesheet" href="assets/css/style.css?v=7">
    <style>
        .form-container {
            max-width: 600px;
            border: 1px solid #ccc;
            width: 60%;
            margin: 10px 450px;
            padding: 20px;
            background-color: transparent;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            position: relative;
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
        /* Hide file inputs if images are already uploaded */
        .form-container .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php include("includes/header.php"); ?>
    <div class="form-container">
    <div class="message-container">
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
        </div>
        <h2>Update Profile</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="username">Full Name:</label>
            <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>"><br>

            <label for="contact">Contact No:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlentities($contactNo); ?>"><br>

            <?php if (empty($citizenImg)): ?>
                <label for="citizen">Citizen Image:</label>
                <input type="file" id="citizen" accept=".jpg,.png,.jpeg" name="citizen"><br>
            <?php else: ?>
                <input type="hidden" name="citizen_current" value="<?php echo htmlentities($citizenImg); ?>">
            <?php endif; ?>

            <?php if (empty($licenseImg)): ?>
                <label for="license">License Image:</label>
                <input type="file" id="license" accept=".jpg,.png,.jpeg" name="license"><br>
            <?php else: ?>
                <input type="hidden" name="license_current" value="<?php echo htmlentities($licenseImg); ?>">
            <?php endif; ?>

            <input type="submit" name="update" value="Update">
        </form>
    </div>
</body>
</html>
