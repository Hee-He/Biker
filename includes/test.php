<?php
require("includes/config.php");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle booking process if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bike_id'])) {
    session_start(); // Start or resume session

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Show login modal if not logged in
        echo "<script>document.getElementById('loginModal').style.display = 'block';</script>";
    } else {
        $bikeId = $_POST['bike_id'];
        $userId = $_SESSION['user_id']; // Replace with your actual session variable for user ID
        $fromDate = $_POST['from_date'] ?? ''; // Replace with actual booking start date logic
        $toDate = $_POST['to_date'] ?? ''; // Replace with actual booking end date logic
        $message = $_POST['message'] ?? ''; // Replace with actual message from user input or default

        // Prepare and bind parameters to insert into booking table
        $stmt = $conn->prepare("INSERT INTO tblbooking (userEmail, VehicleId, FromDate, ToDate, message, Status, PostingDate) VALUES (?, ?, ?, ?, ?, null, NOW())");
        $stmt->bind_param("siiss", $userId, $bikeId, $fromDate, $toDate, $message);
        if ($stmt->execute()) {
            echo "<script>alert('Booking successful!');</script>";
        } else {
            echo "<script>alert('Booking failed. Please try again later.');</script>";
        }

        $stmt->close();
    }
}

// SQL query to fetch all bikes
$sql = "SELECT id, VehiclesTitle, VehiclesBrand, PricePerDay, FuelType, ModelYear, SeatingCapacity, Vimage1, Vimage2, Vimage3, Vimage4, Vimage5, VehiclesOverview FROM `bikerental`.`tblvehicles`";
$result = $conn->query($sql);

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bike List</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?v=2">
    <style>
        /* Add your styles here */
        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
        }

        .mySlides {
            display: none;
        }

        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover, .next:hover {
            background-color: rgba(0,0,0,0.8);
        }
    </style>
    <script>
        let slideIndices = {};

        function showSlides(n, id) {
            let i;
            let slides = document.getElementsByClassName("mySlides-" + id);
            if (n > slides.length) { slideIndices[id] = 1 }
            if (n < 1) { slideIndices[id] = slides.length }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndices[id] - 1].style.display = "block";
        }

        function plusSlides(n, id) {
            showSlides(slideIndices[id] += n, id);
        }

        function showSlidesAuto(id) {
            let i;
            let slides = document.getElementsByClassName("mySlides-" + id);
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndices[id]++;
            if (slideIndices[id] > slides.length) { slideIndices[id] = 1 }
            slides[slideIndices[id] - 1].style.display = "block";
            setTimeout(() => showSlidesAuto(id), 2000); // Change image every 2 seconds
        }

        document.addEventListener("DOMContentLoaded", function() {
            <?php
            if ($result->num_rows > 0) {
                $result->data_seek(0); // Reset result pointer to the beginning
                while($row = $result->fetch_assoc()) {
                    $id = $row["id"];
                    echo "slideIndices[$id] = 0;";
                    // Start auto slideshow only if there are images
                    $hasImages = false;
                    for ($i = 1; $i <= 5; $i++) {
                        if (!empty($row["Vimage$i"])) {
                            $hasImages = true;
                            break;
                        }
                    }
                    if ($hasImages) {
                        echo "showSlidesAuto($id);";
                    }
                }
            }
            ?>
        });
    </script>
</head>
<body>
    <?php include("includes/header.php"); ?>
    <h1>List of Available Bikes</h1>

    <div class="bike-list">
    <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            $id = $row["id"];
            echo "<div class='bike'>";
            echo "<h2>" . $row["VehiclesTitle"] . "</h2>";
            echo "<p><strong>Brand:</strong> " . $row["VehiclesBrand"] . "</p>";
            echo "<p><strong>Price per Day:</strong> $" . $row["PricePerDay"] . "</p>";
            echo "<p><strong>Fuel Type:</strong> " . $row["FuelType"] . "</p>";
            echo "<p><strong>Model Year:</strong> " . $row["ModelYear"] . "</p>";
            echo "<p><strong>Seating Capacity:</strong> " . $row["SeatingCapacity"] . "</p>";

            // Check for images and display slideshow
            $hasImages = false;
            echo "<div class='slideshow-container' id='slideshow-container-$id'>";
            for ($i = 1; $i <= 5; $i++) {
                $image = $row["Vimage$i"];
                if (!empty($image)) {
                    $hasImages = true;
                    echo "<div class='mySlides-$id fade'>";
                    echo "<img src='assets/img/" . $image . "' alt='Bike Image' style='width:100%'>";
                    echo "</div>";
                }
            }
            if ($hasImages) {
                echo "<a class='prev' onclick='plusSlides(-1, $id)'>&#10094;</a>";
                echo "<a class='next' onclick='plusSlides(1, $id)'>&#10095;</a>";
            }
            echo "</div>";

            // View Details Link
            echo "<a href='bike-details.php?id=$id' class='view-details-btn'>View Details</a>";

            echo "</div>";
        }
    } else {
        echo "<p>No bikes available.</p>";
    }
    ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginLink = document.getElementById('loginLink');
            if (loginLink) {
                loginLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    openModal('loginModal');
                });
            }

            var signupLink = document.getElementById('signupLink');
            if (signupLink) {
                signupLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    openModal('signupModal');
                });
            }
        });

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            var loginModal = document.getElementById('loginModal');
            var signupModal = document.getElementById('signupModal');
            if (event.target == loginModal) {
                closeModal('loginModal');
            }
            if (event.target == signupModal) {
                closeModal('signupModal');
            }
        }

        function bookNow(bikeId) {
            var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                openModal('loginModal');
            } else {
                document.getElementById('bookingPopup').style.display = 'block';
            }
        }
    </script>
</body>
</html>
