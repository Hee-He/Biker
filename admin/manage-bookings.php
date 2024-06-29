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

// Handle action buttons
if (isset($_GET['action']) && isset($_GET['booking_id'])) {
    $action = $_GET['action'];
    $booking_id = $_GET['booking_id'];

    // Update status based on action
    if ($action == "approve") {
        $status = 1;
    } elseif ($action == "cancel") {
        $status = 0;
    }

    // Update status in the database
    $update_sql = "UPDATE tblbooking SET Status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $status, $booking_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to reload the page
    header("Location: manage-bookings.php");
    exit();
}

// Fetch initial bookings from the database
$sql = "SELECT id, userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate 
        FROM tblbooking";
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
        p{
            margin: 0 100px;
        }
    </style>
</head>
<body>
    <?php include("includes/header.php"); ?> <!-- Include admin header that manages session start -->
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

                // Generate table rows based on the initial query result
                generateTableRows($result, $conn);
                ?>
            </div>
        </section>
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
            xhr.open('POST', 'search-bookings.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('booking_details').innerHTML = xhr.responseText;
                }
            };
            xhr.send('search_term=' + search_term);
        });

        // Function to update booking status
        function updateStatus(bookingId, action) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'manage-bookings.php?action=' + action + '&booking_id=' + bookingId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Reload the page after successful update
                    location.reload();
                }
            };
            xhr.send();
        }

        // Function to delete booking (optional)
        function deleteBooking(bookingId) {
            var confirmDelete = confirm("Are you sure you want to delete this booking?");
            if (confirmDelete) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete-data.php', true); // Adjust the URL to your PHP script handling deletions
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Handle the response here, such as showing a message or updating the UI
                        alert(xhr.responseText); // Show the response from PHP
                            window.location.reload();
                    }
                };
                xhr.send('action=deleteBooking&bookingId=' + bookingId); // Send action and bookingId as query parameters
                
            }

        }

    </script>
</body>
</html>
