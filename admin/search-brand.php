<?php
require("includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_term'])) {
    $searchTerm = $_POST['search_term'];
    $sql = "SELECT id, BrandName FROM tblbrands WHERE BrandName LIKE ? OR id LIKE ?";
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
            echo "<th>Brand Name</th>";
            echo "<th>Actions</th>"; 
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["BrandName"] . "</td>";
                echo "<td>";
                echo '<a href="edit-brand.php?id=' . $row["id"] . '" class="action-btn edit-btn" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                echo "<a href='javascript:void(0);' class='action-btn delete-btn' title='Delete' onclick='deleteBrand(" . $row["id"] . ")'><i class='fa fa-trash-alt'></i></a>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No Brand Found.</p>";
        }
    }

    header('Content-Type: text/html'); 
    generateTableRows($result, $conn);

    $stmt->close();
    $conn->close();
}
?>
