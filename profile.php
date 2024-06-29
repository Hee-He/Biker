<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/header.php");

$user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    // Redirect if user is not logged in
    header("Location: login.php");
    exit();
}

// Fetch user details from database
$sql = "SELECT ContactNo FROM `bikerental`.`tblusers` WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $newName = $_POST['username'];
    $newContact = $_POST['contact'];
    $updateSql = "UPDATE `bikerental`.`tblusers`
                SET
                `FullName` = ?,
                `ContactNo` = ?
                WHERE id = ?
                ";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('sii', $newName, $newContact, $user_id);
    if ($stmt->execute()) {
        echo "Update Successfully";
        $_SESSION['username'] = $newName; // Update session variable with new username
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
    <title><?php echo $user; ?> Profile</title>
</head>
<body>
    <div>
        <div>
            <form action="" method="post">
                <input type="text" name="username" value="<?php echo htmlentities($user); ?>"><br>
                <input type="text" name="contact" value="<?php echo htmlentities($row['ContactNo']); ?>"><br>
                <input type="file" accept=".jpg,.png,.jpeg,.bmp,.wemp" name="citizen"><br>
                <input type="file" accept=".jpg,.png,.jpeg,.bmp,.wemp" name="license"><br>
                <input type="submit" name="update" value="Update"><br>
            </form>
        </div>
    </div>
</body>
</html>
