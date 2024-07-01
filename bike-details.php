<?php
require("includes/config.php");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch brand names from tblbrands
$sql_brands = "SELECT id, brandName FROM tblbrands";
$result_brands = $conn->query($sql_brands);

// Initialize variables for booking form
$fromDate = '';
$toDate = '';
$message = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bike_id'])) {
    $loggedInUser = $_SESSION['username'];

    // Checking whether the profile is fully setup or not
    $sql = "SELECT citizen_img, license_img FROM tblusers WHERE FullName=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Show login modal if not logged in
        echo "<script>document.getElementById('loginModal').style.display = 'block';</script>";
    } elseif ($row['citizen_img'] === NULL || $row['license_img'] === NULL) {
        // Show profile setup modal if profile is incomplete
        echo "<script>document.addEventListener('DOMContentLoaded', function() { openProfileSetupModal(); });</script>";
    } else {
        // Existing booking logic
        $bikeId = $_POST['bike_id'];
        $userId = $_SESSION['user_id'];

        // Validate and format dates
        if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
            $fromDate = date('Y-m-d', strtotime($_POST['from_date']));
            $toDate = date('Y-m-d', strtotime($_POST['to_date']));
        }

        $message = $_POST['message'] ?? '';

        // Check if the user has already booked a vehicle
        $sql = "SELECT userEmail FROM tblbooking WHERE userEmail=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) {
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO tblbooking (userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate) VALUES (?, ?, ?, ?, ?, -1, NOW())");
            $stmt->bind_param("iisss", $userId, $bikeId, $fromDate, $toDate, $message);

            // Execute SQL statement
            if ($stmt->execute()) {
                // Decrease bike quantity by 1 after successful booking
                $stmt_update = $conn->prepare("UPDATE tblvehicles SET vehicle_quantity = vehicle_quantity - 1 WHERE id = ?");
                $stmt_update->bind_param("i", $bikeId);
                $stmt_update->execute();
                $stmt_update->close();

                echo "<script>alert('Booking successful!');</script>";
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=$bikeId");
                exit();
            } else {
                echo "<script>alert('Booking failed. Please try again later.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('You have already booked one vehicle!');</script>";
        }
    }
}

// Fetch bike details if bike ID is provided in the URL
if (isset($_GET['id'])) {
    $bikeId = $_GET['id'];

    // Query to fetch bike details
    $stmt = $conn->prepare("SELECT id, VehiclesTitle, VehiclesBrand, PricePerDay, ModelYear, vehicle_quantity, Vimage1, VehiclesOverview FROM tblvehicles WHERE id = ?");
    $stmt->bind_param("i", $bikeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Extract bike details
        $bikeTitle = $row["VehiclesTitle"];
        $bikeBrand = $row["VehiclesBrand"];
        $pricePerDay = $row["PricePerDay"];
        $modelYear = $row["ModelYear"];
        $quantity = $row["vehicle_quantity"];
        $bikeOverview = $row["VehiclesOverview"];
        $image = $row["Vimage1"];
    } else {
        echo "Bike not found.";
        exit;
    }

    $stmt->close();
} else {
    echo "Bike ID not specified.";
    exit;
}

// Close connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $bikeTitle; ?> Details - Ride Ready Rentals</title>
    <link rel="stylesheet" href="assets/css/style.css?v=7">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .containers {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Bike Details Section */
        .bike-details {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
            min-height: 500px;
        }

        .bike-details .bike {
            flex: 1 1 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .bike-details .booking-form {
            flex: 0 0 300px; 
            max-width: 300px; 
            padding: 20px;
            background-color: #f0f0f0;
            border-left: 1px solid #ccc;
        }

        .bike-details h1,
        .bike-details h2 {
            margin-top: 0;
        }

        .bike-details p {
            margin: 10px 0;
        }

        .bike-details .bike-images {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .bike-details .bike-images img {
            max-width: 100%;
            height: auto;
        }

        /* Fixed Height for Overview Section */
        .bike-details .bike-overview {
            flex-grow: 1;
            overflow-y: auto;
            max-height: 200px; 
            margin-top: 20px;
        }

        /* Booking Form Styles */
        .booking-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .booking-form input[type="date"],
        .booking-form textarea {
            width: 80%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .booking-form textarea {
            resize: vertical;
        }

        .booking-form .book-now-btn {
            width: 80%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }

        .booking-form .book-now-btn:hover {
            background-color: #45a049;
        }

        /* Add blur and dim effect when the modal or setup profile div is active */
        body.modal-active {
            overflow: hidden;
        }

        .background-blur {
            filter: blur(5px);
            opacity: 0.3;
            pointer-events: none; /* Disable interaction with background content */
        }

        /* Profile setup modal styles */
        #profileSetupModal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000; /* Ensure it's above other content */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scrolling if needed */
            background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
        }

        #profileSetupModal .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%; /* Could be more or less, depending on screen size */
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        #profileSetupModal .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        #profileSetupModal .close:hover,
        #profileSetupModal .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Include header -->
    <?php include("includes/header.php"); ?>

    <div class="containers">
        <div class="bike-details">
            <!-- Display bike details -->
            <div class="bike">
                <h1><?php echo $bikeTitle; ?> Details</h1>
                <div class="bike">
                    <!-- Display bike details as needed -->
                    <?php
                    // Find the corresponding brand name for this vehicle
                    $brandName = "";
                    $result_brands->data_seek(0); // Reset result pointer to the beginning
                    while ($brand = $result_brands->fetch_assoc()) {
                        if ($row["VehiclesBrand"] == $brand["id"]) {
                            $brandName = $brand["brandName"];
                            break;
                        }
                    }
                    ?>
                    <h2><?php echo $bikeTitle; ?></h2>
                    <p><strong>Brand:</strong> <?php echo $brandName; ?></p>
                    <p><strong>Price per Day:</strong> Rs. <?php echo $pricePerDay; ?></p>
                    <p><strong>Model Year:</strong> <?php echo $modelYear; ?></p>
                    <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                    
                    <!-- Display bike images -->
                    <div class="bike-images">
                        <img src="<?php echo 'assets/img/' . $image; ?>" alt="Bike Image" width="500">
                    </div>

                    <!-- Display bike overview in a fixed height container -->
                    <div class="bike-overview">
                        <p><?php echo $bikeOverview; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Booking Form -->
            <div class="booking-form">
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="bike_id" value="<?php echo $bikeId; ?>">
                    <label for="from_date">From Date:</label>
                    <input type="date" id="from_date" name="from_date" value="<?php echo $fromDate; ?>" min="<?php echo date('Y-m-d'); ?>" required><br><br>
                    <label for="to_date">To Date:</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo $toDate; ?>" max="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" required><br><br>
                    
                    <label for="message">Message:</label><br>
                    <textarea id="message" name="message" rows="4" cols="50"><?php echo $message; ?></textarea><br><br>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button type="submit" class="book-now-btn">Book Now</button>
                    <?php else: ?>
                        <button type="button" onclick="openModal('loginModal')" class="book-now-btn">Book Now</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Setup Modal -->
    <div id="profileSetupModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProfileSetupModal()">&times;</span>
            <span>Setup Your Profile!</span><br>
            <a href="profile.php">Setup</a>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <?php include 'includes/login.php'; ?>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
        function openProfileSetupModal() {
            console.log("Opening profile setup modal...");
            document.getElementById('profileSetupModal').style.display = 'block';
            document.body.classList.add('modal-active');
            document.querySelector('.containers').classList.add('background-blur');
        }

        function closeProfileSetupModal() {
            console.log("Closing profile setup modal...");
            document.getElementById('profileSetupModal').style.display = 'none';
            document.body.classList.remove('modal-active');
            document.querySelector('.containers').classList.remove('background-blur');
        }

        window.onclick = function(event) {
            var profileSetupModal = document.getElementById('profileSetupModal');
            if (event.target == profileSetupModal) {
                console.log("Click outside profile setup modal, closing modal...");
                closeProfileSetupModal();
            }

            var loginModal = document.getElementById('loginModal');
            if (event.target == loginModal) {
                console.log("Click outside login modal, closing modal...");
                closeModal('loginModal');
            }
        }

        function openModal(modalId) {
            console.log("Opening modal: " + modalId);
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            console.log("Closing modal: " + modalId);
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>

