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

// Function to get status text based on value
function getStatusText($status) {
    if ($status == -1) {
        return "Pending";
    } elseif ($status == 0) {
        return "Cancelled";
    } elseif ($status == 1) {
        return "Approved";
    }
}

// Initialize variables for search functionality
$search_term = "";
$where_clause = ""; // This will be used in the SQL query WHERE clause

// Process search term if submitted via POST
if (isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];
    // Prepare the WHERE clause for SQL query based on search term
    $where_clause = " WHERE id LIKE '%$search_term%' OR userEmail LIKE '%$search_term%' OR VehicleId LIKE '%$search_term%'";
}

// Fetch bookings from the database based on search criteria
$sql = "SELECT id, userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate 
        FROM tblbooking $where_clause";
$result = $conn->query($sql);

// Function to generate table rows
function generateTableRows($result, $conn) {
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Output table header
        echo "<table class='booking-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Booking ID</th>";
        echo "<th>Name</th>";
        echo "<th>Vehicle ID</th>";
        echo "<th>From Date</th>";
        echo "<th>To Date</th>";
        echo "<th>Message</th>";
        echo "<th>Status</th>";
        echo "<th>Posting Date</th>";
        echo "<th>Actions</th>"; // Add a column for actions (update status, delete, etc.)
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Fetch user's full name based on userEmail
            $useremail = $row['userEmail'];
            $query = "SELECT FullName FROM tblusers WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $useremail);
            $stmt->execute();
            $results = $stmt->get_result();

            // Fetch the user's full name
            if ($results->num_rows > 0) {
                $userData = $results->fetch_assoc();
                $fullName = $userData['FullName'];
            } else {
                $fullName = "Unknown"; // Default if user not found (shouldn't happen ideally)
            }

            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $fullName . "</td>";
            echo "<td>" . $row["VehicleId"] . "</td>";
            echo "<td>" . $row["FromDate"] . "</td>";
            echo "<td>" . $row["ToDate"] . "</td>";
            echo "<td>" . $row["message"] . "</td>";
            echo "<td>" . getStatusText($row["Status"]) . "</td>";
            echo "<td>" . $row["PostingDate"] . "</td>";
            echo "<td>";
            // Action buttons with JavaScript to update status
            echo "<a href='javascript:void(0);' class='btn btn-primary' onclick='updateStatus(" . $row["id"] . ", \"approve\")'>Approved</a>";
            echo "<a href='javascript:void(0);' class='btn btn-cancel' onclick='updateStatus(" . $row["id"] . ", \"cancel\")'>Cancel</a>";
            echo "<a href='javascript:void(0);' class='btn btn-danger' onclick='deleteBooking(" . $row["id"] . ")'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No bookings found.</p>";
    }
}

// Generate table rows based on the search query result
generateTableRows($result, $conn);
?>
