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
require("../fetch_data.php"); // Include fetch_data.php for functions

// Function to get status text based on value (existing function)
function getStatusText($status) {
    if ($status == -1) {
        return "Pending";
    } elseif ($status == 0) {
        return "Cancelled";
    } elseif ($status == 1) {
        return "Approved";
    }
}

// Handle action buttons (existing code)
if (isset($_GET['action']) && isset($_GET['booking_id'])) {
    $action = $_GET['action'];
    $booking_id = $_GET['booking_id'];

    // Update status based on action (existing code)
    if ($action == "approve") {
        $status = 1;
    } elseif ($action == "cancel") {
        $status = 0;
    }

    // Update status in the database (existing code)
    $update_sql = "UPDATE tblbooking SET Status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $status, $booking_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to reload the page (existing code)
    header("Location: manage-bookings.php");
    exit();
}

// Fetch initial bookings from the database (modified SQL to join tblusers)
$sql = "SELECT b.id, u.FullName, b.VehicleId, b.FromDate, b.ToDate, b.message, b.Status, b.PostingDate 
        FROM tblbooking b
        INNER JOIN tblusers u ON b.userEmail = u.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        p {
            margin: 0 100px;
        }
    </style>
</head>
<body>
    <?php include("includes/header.php"); ?>
    <div class="container-manage-booking">
        <div class="header">
            <h1>Manage Booking</h1>
            <div class="search-container">
                <form action="" method="GET">
                    <input type="text" id="search_term" placeholder="Search..." name="search">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>
        <div class="booking-details" id="booking_details">
            <?php
            // Function to generate table rows (existing function)
            function generateTableRows($result, $conn) {
                if ($result->num_rows > 0) {
                    echo "<table class='booking-table'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Booking ID</th>";
                    echo "<th>Name</th>";
                    echo "<th>Vehicle</th>"; 
                    echo "<th>From Date</th>";
                    echo "<th>To Date</th>";
                    echo "<th>Message</th>";
                    echo "<th>Status</th>";
                    echo "<th>Posting Date</th>";
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    while ($row = $result->fetch_assoc()) {
                        // Fetch vehicle name using getVehicleName function
                        $vehicleName = getVehicleName($row["VehicleId"]);

                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["FullName"] . "</td>"; 
                        echo "<td>" . $vehicleName . "</td>"; 
                        echo "<td>" . $row["FromDate"] . "</td>";
                        echo "<td>" . $row["ToDate"] . "</td>";
                        echo "<td>" . $row["message"] . "</td>";
                        echo "<td>" . getStatusText($row["Status"]) . "</td>";
                        echo "<td>" . $row["PostingDate"] . "</td>";
                        echo "<td>";
                        // Action buttons with JavaScript to update status and delete booking
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

            // Generate table rows based on the initial query result
            generateTableRows($result, $conn);
            ?>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Admin Dashboard. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // JavaScript functions for live search, update status, and delete booking (existing script)
        var searchBox = document.getElementById('search_term');
        searchBox.addEventListener('input', function() {
            var search_term = this.value.trim();
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'search-bookings.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('booking_details').innerHTML = xhr.responseText;
                }
            };
            xhr.send('search_term=' + search_term);
        });

        function updateStatus(bookingId, action) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'manage-bookings.php?action=' + action + '&booking_id=' + bookingId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    location.reload();
                }
            };
            xhr.send();
        }

        function deleteBooking(bookingId) {
            var confirmDelete = confirm("Are you sure you want to delete this booking?");
            if (confirmDelete) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete-data.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        window.location.reload();
                    }
                };
                xhr.send('action=deleteBooking&bookingId=' + bookingId);
            }
        }

    </script>
</body>
</html>
