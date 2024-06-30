<?php
include("includes/header.php");
include("includes/config.php");

function getStatusText($status) {
    switch ($status) {
        case -1:
            return "Pending";
        case 0:
            return "Cancelled";
        case 1:
            return "Approved";
        default:
            return "Unknown";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 40vh; 
            padding: 20px;
            margin-top: 10px;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            justify-content: center;
            text-align: center;
            margin: -10px 0;
            padding: 10px;
            border-radius: 10px;
            width: 60%;
            max-width: 1200px;
        }

        .container .sub-container {
            flex: 1;
            margin: 0 5px;
            padding: 20px;
            background-color: #3DC2EC;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container .sub-container i {
            font-size: 40px;
            color: #000;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .fa .fa-arrow-right
        {
            font-size: 30px;
        }
        .container .sub-container .link {
            background-color: #fff;
            width: 100%;
            text-align: center;
            height: 50px;
            margin-top: auto;
            border-radius:0.5rem;
        }
        .container .sub-container .link:hover{
            background-color: #402E7A;
        }
        .container .sub-container .link a {
            display: block;
            margin: -10px;
            color: #007BFF;
            text-decoration: none;
        }

        .container .sub-container .link a:hover {
            text-decoration: underline;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .booking-table th, .booking-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .booking-table th {
            background-color: #f2f2f2;
        }

        .btn {
            padding: 5px 10px;
            margin: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-cancel {
            background-color: #FFC107;
            color: white;
        }

        .btn-danger {
            background-color: #F44336;
            color: white;
        }

        .not-found {
            margin: 10px 200px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="container">
            <div class="sub-container">
                <i class="fas fa-users"></i>
                <?php
                $sql1 = "SELECT COUNT(id) AS user_count FROM tblusers";
                $stmt = $conn->prepare($sql1);
                $stmt->execute();
                $results1 = $stmt->get_result();
                $row1 = $results1->fetch_assoc();
                ?>
                <p><?php echo $row1['user_count']; ?></p>
                <p>Registered Users</p>
                <div class="link"><a href="manage-users.php">view details &nbsp;<i class="fa fa-arrow-right"></i></a></div>
            </div>
            <div class="sub-container">
                <i class="fas fa-book"></i>
                <?php
                $sql2 = "SELECT COUNT(id) AS book_count FROM tblbooking";
                $stmt = $conn->prepare($sql2);
                $stmt->execute();
                $results2 = $stmt->get_result();
                $row2 = $results2->fetch_assoc();
                ?>
                <p><?php echo $row2['book_count']; ?></p>
                <p>Total Bookings</p>
                <div class="link"><a href="manage-bookings.php">view details &nbsp;<i class="fa fa-arrow-right"></i></a></div>
            </div>
            <div class="sub-container">
                <i class="fas fa-car"></i>
                <?php
                $sql3 = "SELECT COUNT(id) AS vehicle_count FROM tblvehicles";
                $stmt = $conn->prepare($sql3);
                $stmt->execute();
                $results3 = $stmt->get_result();
                $row3 = $results3->fetch_assoc();
                ?>
                <p><?php echo $row3['vehicle_count']; ?></p>
                <p>Registered Vehicles</p>
                <div class="link"><a href="manage-vehicles.php">view details &nbsp;<i class="fa fa-arrow-right"></i></a></div>
            </div>
        </div>
        <div>
            <?php
            $todayDate = date('Y-m-d');
            $sql = "SELECT id, userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate 
                    FROM tblbooking 
                    WHERE PostingDate = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $todayDate);
            $stmt->execute();
            $results = $stmt->get_result();
            
            if ($results->num_rows > 0) {
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
                echo "<th>Actions</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                while ($row = $results->fetch_assoc()) {
                    $userEmail = $row['userEmail'];
                    $query = "SELECT FullName FROM tblusers WHERE id=?";
                    $stmt2 = $conn->prepare($query);
                    $stmt2->bind_param("s", $userEmail);
                    $stmt2->execute();
                    $userResults = $stmt2->get_result();
                    
                    if ($userResults->num_rows > 0) {
                        $userData = $userResults->fetch_assoc();
                        $fullName = $userData['FullName'];
                    } else {
                        $fullName = "Unknown";
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
                    echo "<a href='javascript:void(0);' class='btn btn-primary' onclick='updateStatus(" . $row["id"] . ", \"approve\")'>Approve</a>";
                    echo "<a href='javascript:void(0);' class='btn btn-cancel' onclick='updateStatus(" . $row["id"] . ", \"cancel\")'>Cancel</a>";
                    echo "<a href='javascript:void(0);' class='btn btn-danger' onclick='deleteBooking(" . $row["id"] . ")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p class='not-found'>No bookings found for today.</p>";
            }
            ?>
        </div>
    </div>
    <script>
        function updateStatus(bookingId, status) {
            // Implement the logic to update the booking status via AJAX or form submission
        }

        function deleteBooking(bookingId) {
            if (confirm("Are you sure you want to delete this booking?")) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete-data.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        if (xhr.responseText.trim() === 'success') {
                            window.location.reload();
                        }
                    }
                };
                xhr.send('action=deleteBooking&bookingId=' + bookingId);
            }
        }
    </script>
</body>
</html>
