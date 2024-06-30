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

// Initialize variables for search functionality
$search_term = "";
$where_clause = ""; 

// Process search term if submitted via POST
if (isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];
    // Prepare the WHERE clause for SQL query based on search term
    $where_clause = " WHERE id LIKE '%$search_term%' OR FullName LIKE '%$search_term%' OR EmailId LIKE '%$search_term%'";
}

// Fetch users from the database based on search criteria
$sql = "SELECT id, FullName, EmailId, ContactNo, RegDate, Status FROM tblusers $where_clause";
$result = $conn->query($sql);

// Function to generate table rows
function generateTableRows($result) {
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Output table header
        echo "<table class='user-table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>User ID</th>";
        echo "<th>Full Name</th>";
        echo "<th>Email ID</th>";
        echo "<th>Contact No</th>";
        echo "<th>Registration Date</th>";
        echo "<th>Status</th>";
        echo "<th>Actions</th>"; 
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["FullName"] . "</td>";
            echo "<td>" . $row["EmailId"] . "</td>";
            echo "<td>" . $row["ContactNo"] . "</td>";
            echo "<td>" . $row["RegDate"] . "</td>";
            echo "<td>" . ($row["Status"] == 1 ? "Active" : "Inactive") . "</td>";
            echo "<td>";
            // Action buttons with JavaScript to update status
            if ($row["Status"] == 1) {
                echo "<a href='javascript:void(0);' class='btn btn-cancel' onclick='updateStatus(" . $row["id"] . ", \"deactivate\")'>Deactivate</a>";
            } else {
                echo "<a href='javascript:void(0);' class='btn btn-primary' onclick='updateStatus(" . $row["id"] . ", \"activate\")'>Activate</a>";
            }
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No users found.</p>";
    }
}

// Generate table rows based on search query result
generateTableRows($result);
?>
