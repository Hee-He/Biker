<?php
require("includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_term'])) {
    $searchTerm = $_POST['search_term'];
    $sql = "SELECT id, VehiclesTitle, VehiclesBrand, PricePerDay, ModelYear FROM tblvehicles WHERE VehiclesTitle LIKE ? OR VehiclesBrand LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeSearchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param("ss", $likeSearchTerm, $likeSearchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    function generateTableRows($result, $conn) {
        if ($result->num_rows > 0) {
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

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["VehiclesTitle"] . "</td>";
                echo "<td>" . getBrandName($row["VehiclesBrand"], $conn) . "</td>";
                echo "<td>" . $row["PricePerDay"] . "</td>";
                echo "<td>" . $row["ModelYear"] . "</td>";
                echo "<td>";
                echo '<a href="edit-vehicle.php?id=' . $row["id"] . '" class="action-btn edit-btn" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
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

    generateTableRows($result, $conn);

    $stmt->close();
    $conn->close();
}
?>
