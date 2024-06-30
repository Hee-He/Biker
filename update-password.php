<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database configuration file
require("includes/config.php");

// Initialize variables
$error = "";
$success = "";

// Process the form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $error = "New password and confirm password do not match.";
    } else {
        // Get the logged-in user's username from session
        $username = $_SESSION['username'];

        // Fetch the user's current password hash from the database
        $sql = "SELECT Password FROM bikerental.tblusers WHERE FullName=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedPasswordHash = $row['Password'];

            // Verify the current password
            if (md5($currentPassword) === $storedPasswordHash) {
                // Hash the new password with MD5
                $newPasswordHash = md5($newPassword);

                // Update the password in the database
                $updateSql = "UPDATE bikerental.tblusers SET Password=? WHERE FullName=?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param('ss', $newPasswordHash, $username);

                if ($updateStmt->execute()) {
                    $success = "Password updated successfully.";
                } else {
                    $error = "Error updating password. Please try again later.";
                }

                $updateStmt->close();
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .containers {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .form-groups {
            margin-bottom: 15px;
        }
        .form-groups label {
            display: block;
            font-weight: bold;
        }
        .form-groups input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        .form-groups .btn {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-groups .btn:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 3px;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
    </style>
</head>
<body>
    <?php include("includes/header.php"); ?>
    <div class="containers">
        <h2>Update Password</h2>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-groups">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-groups">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-groups">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-groups">
                <button type="submit" class="btn">Update Password</button>
            </div>
        </form>
    </div>
</body>
</html>
