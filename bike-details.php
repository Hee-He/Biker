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
$uploadDir = 'img/'; // Directory where uploaded files will be saved

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bike_id'])) {
    $loggedInUser = $_SESSION['username'];
    
    // Checking whether the profile is fully setup or not
    $sql = "SELECT dob, Address FROM `bikerental`.`tblusers` WHERE FullName=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Debug: output fetched user data
    echo "<script>console.log('User data: " . json_encode($row) . "');</script>";

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Show login modal if not logged in
        echo "<script>document.getElementById('loginModal').style.display = 'block';</script>";
    } 
    // elseif ($row['dob'] === NULL && $row['Address'] === NULL) {
    //     echo '<div>';
    //     echo '<span>&times;</span>';
    //     echo '<span>Setup Your Profile!</span><br>';
    //     echo '<a href="profile.php">Setup</a>';
    //     echo '</div>';
    // } 
    else {
        $bikeId = $_POST['bike_id'];
        $userId = $_SESSION['user_id'];

        // Validate and format dates
        if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
            $fromDate = date('Y-m-d', strtotime($_POST['from_date']));
            $toDate = date('Y-m-d', strtotime($_POST['to_date']));
        }

        $message = $_POST['message'] ?? '';

        // Handle file upload
        
                        // Prepare SQL statement
                        $stmt = $conn->prepare("INSERT INTO tblbooking (userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate) VALUES (?, ?, ?, ?, ?, -1, NOW())");
                        $stmt->bind_param("iisss", $userId, $bikeId, $fromDate, $toDate, $message);

                        // Execute SQL statement
                        if ($stmt->execute()) {
                            echo "<script>alert('Booking successful!');</script>";
                            header("Location: " . $_SERVER['PHP_SELF'] ."?id=$bikeId");
                            exit();
                            // Optionally redirect after successful booking
                            // header("Location: bike-details.php?id=$bikeId");
                            // exit;
                        } else {
                            echo "<script>alert('Booking failed. Please try again later.');</script>";
                        }

                        $stmt->close();
                    } 
    }

// Fetch bike details if bike ID is provided in the URL
if (isset($_GET['id'])) {
    $bikeId = $_GET['id'];

    // Query to fetch bike details
    $stmt = $conn->prepare("SELECT id, VehiclesTitle, VehiclesBrand, PricePerDay, FuelType, ModelYear, SeatingCapacity, Vimage1, Vimage2, Vimage3, Vimage4, Vimage5, VehiclesOverview FROM tblvehicles WHERE id = ?");
    $stmt->bind_param("i", $bikeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Extract bike details
        $bikeTitle = $row["VehiclesTitle"];
        $bikeBrand = $row["VehiclesBrand"];
        $pricePerDay = $row["PricePerDay"];
        $fuelType = $row["FuelType"];
        $modelYear = $row["ModelYear"];
        $seatingCapacity = $row["SeatingCapacity"];
        $bikeOverview = $row["VehiclesOverview"];

        // Collect image URLs
        $imageUrls = [];
        for ($i = 1; $i <= 5; $i++) {
            $image = $row["Vimage$i"];
            if (!empty($image)) {
                $imageUrls[] = "assets/img/" . $image;
            }
        }
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

        .container {
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
    </style>
</head>
<body>
    <!-- Include header -->
    <?php include("includes/header.php"); ?>

    <div class="container">
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
                    <p><strong>Fuel Type:</strong> <?php echo $fuelType; ?></p>
                    <p><strong>Model Year:</strong> <?php echo $modelYear; ?></p>
                    <p><strong>Seating Capacity:</strong> <?php echo $seatingCapacity; ?></p>
                    
                    <!-- Display bike images -->
                    <div class="bike-images">
                        <?php foreach ($imageUrls as $imageUrl): ?>
                            <img src="<?php echo $imageUrl; ?>" alt="Bike Image">
                        <?php endforeach; ?>
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
                    <input type="date" id="from_date" name="from_date" value="<?php echo $fromDate; ?>" required><br><br>
                    <label for="to_date">To Date:</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo $toDate; ?>" required><br><br>
                    
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

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <?php include 'includes/login.php'; ?>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            var loginModal = document.getElementById('loginModal');
            if (event.target == loginModal) {
                closeModal('loginModal');
            }
        }
    

        

    </script>
</body>
</html>
