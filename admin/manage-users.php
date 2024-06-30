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

// Handle action buttons
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $user_id = $_GET['user_id'];

    // Update status based on action
    if ($action == "activate") {
        $status = 1;
    } elseif ($action == "deactivate") {
        $status = 0;
    }

    // Update status in the database
    $update_sql = "UPDATE tblusers SET Status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $status, $user_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to reload the page
    header("Location: manage-users.php");
    exit();
}

// Initialize variables for search functionality
$search_term = "";
$where_clause = ""; // This will be used in the SQL query WHERE clause

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="assets/css/style.css?v=6"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include("includes/header.php"); ?> 
    <div class="container-manage-users">
            <div class="header">
            <h1>Manage Users</h1>
            <div class="search-container">
                <form action="" method="GET">
                    <input type="text" id="search_term" placeholder="Search..." name="search">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            </div>
            <div class="user-details" id="user_details">
                <?php
                // Generate table rows based on the initial query result
                generateTableRows($result);
                ?>
            </div>
</div>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Admin Dashboard. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Function to handle live search on text change
        var searchBox = document.getElementById('search_term');
        searchBox.addEventListener('input', function() {
            var search_term = this.value.trim();
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'search-users.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('user_details').innerHTML = xhr.responseText;
                }
            };
            xhr.send('search_term=' + search_term);
        });

        // Function to update user status
        function updateStatus(userId, action) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'manage-users.php?action=' + action + '&user_id=' + userId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Reload the page after successful update
                    location.reload();
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>