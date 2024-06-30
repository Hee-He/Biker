<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="assets/css/style.css?v=6"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include("includes/header.php"); ?>

    <div class="container-manage-vehicle">
    <div class="header">
        <h1>Manage Vehicles</h1>
        <div class="search-container">
            <form action="" method="GET">
                <input type="text" id="search_term" placeholder="Search..." name="search">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="content" id="vehicle_details">
        <?php
        // Function to generate table rows
        function generateTableRows($result, $conn) {
            // Check if there are any rows returned
            if ($result->num_rows > 0) {
                // Output table header
                echo "<table class='vehicle-table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Vehicle Name</th>";
                echo "<th>Vehicle Brand</th>";
                echo "<th>Price Per Day</th>";
                echo "<th>Model Year</th>";
                echo "<th>Actions</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["VehiclesTitle"] . "</td>";
                    echo "<td>" . getBrandName($row["VehiclesBrand"], $conn) . "</td>";
                    echo "<td>" . $row["PricePerDay"] . "</td>";
                    echo "<td>" . $row["ModelYear"] . "</td>";
                    echo "<td>";
                    // Action buttons with JavaScript to update status
                    echo '<a href="edit-vehicle.php?id=' . $row["id"] . '" class="action-btn edit-btn" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                    // echo '<a href="javascript:void(0);" onclick=deleteVehicle("' . $row["id"] . '")' . '" ></a>';
                    echo "<a href='javascript:void(0);' class='action-btn delete-btn' title='Delete' onclick='deleteVehicle(" . $row["id"] . ")'><i class='fa fa-trash-alt'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>No Vehicles Found.</p>";
            }
        }

        // Function to get brand name based on brand ID
        function getBrandName($brandId, $conn) {
            $sql = "SELECT BrandName FROM tblbrands WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $brandId);
            $stmt->execute();
            $stmt->bind_result($brandName);
            $stmt->fetch();
            $stmt->close();
            return $brandName;
        }

        // Include database connection and configuration
        require("includes/config.php");

        // Fetch initial vehicles from the database
        $sql = "SELECT id, VehiclesTitle, VehiclesBrand, PricePerDay, ModelYear FROM tblvehicles";
        $result = $conn->query($sql);

        // Generate table rows based on the initial query result
        generateTableRows($result, $conn);
        ?>
    </div>
</div>

</body>
<script>
    var searchBox = document.getElementById('search_term');
searchBox.addEventListener('input', function() {
    var search_term = this.value.trim();
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'search-vehicles.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('vehicle_details').innerHTML = xhr.responseText;
        }
    };
    xhr.send('search_term=' + search_term);
});

function deleteVehicle(vehicleId) {
    var confirmDelete = confirm("Are you sure you want to delete this booking?");
    if (confirmDelete) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete-data.php', true); 
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Handle the response here, such as showing a message or updating the UI
                alert(xhr.responseText); 
                    window.location.reload();
            }
        };
        xhr.send('action=deleteVehicle&vehicleId=' + vehicleId); // Send action and bookingId as query parameters
    }
}

</script>
</html>
