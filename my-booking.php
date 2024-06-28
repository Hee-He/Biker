<?php
require("includes/config.php");

// Start or resume the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's username from session
$username = $_SESSION['username'];

// Prepare SQL query to fetch user's email
$sql = "SELECT id FROM bikerental.tblusers WHERE FullName=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$results = $stmt->get_result();

// Check if user exists and fetch the email
if ($results->num_rows > 0) {
    $row = $results->fetch_assoc();
    $userEmail = $row['id'];
    
    // SQL query to fetch booking data for the logged-in user
    $sql = "SELECT id, userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate 
            FROM tblbooking 
            WHERE userEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    // Handle case where user does not exist (though it should ideally not happen if session is managed properly)
    echo "User not found.";
    exit();
}

// Close prepared statement and database connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Additional styles for list items */
        .booking-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include("includes/header.php"); ?> <!-- Include header that manages session start -->
    <main class="container">
        <section class="booking-section">
            <h2>My Booking Details</h2>
            <div class="booking-details">
                <?php
                // Check if there are any rows returned
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='booking-item'>";
                        echo "<p><strong>Vehicle ID:</strong> " . $row["VehicleId"] . "</p>";
                        echo "<p><strong>From Date:</strong> " . $row["FromDate"] . "</p>";
                        echo "<p><strong>To Date:</strong> " . $row["ToDate"] . "</p>";
                        echo "<p><strong>Message:</strong> " . $row["message"] . "</p>";
                        echo "<p><strong>Status:</strong> " . getStatusText($row["Status"]) . "</p>"; // Display status based on value
                        echo "<p><strong>Posting Date:</strong> " . $row["PostingDate"] . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No bookings found.</p>";
                }

                // Function to get status text based on value
                function getStatusText($status) {
                    if ($status == -1) {
                        return "Pending";
                    } elseif ($status == 0) {
                        return "Cancelled";
                    } else {
                        return "Approved"; // Handle unexpected status values gracefully
                    }
                }
                ?>
            </div>
        </section>
    </main>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> My Bookings. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
