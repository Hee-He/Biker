document.addEventListener("DOMContentLoaded", function() {
    <?php
    require("includes/config.php");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, VehiclesTitle, VehiclesBrand, PricePerDay, FuelType, ModelYear, SeatingCapacity, Vimage1, Vimage2, Vimage3, Vimage4, Vimage5, BrakeAssist, AirConditioner, VehiclesOverview FROM `bikerental`.`tblvehicles`";
    $result = $conn->query($sql);

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

    // Close connection
    $conn->close();
    ?>
});