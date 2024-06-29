<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="assets/css/style.css?v=6"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include("includes/header.php"); ?> <!-- Include your header -->

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
        <div class="content" id="vehicle_details"> <!-- Added ID here -->
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
                    echo "<th>Vehicle Brand</th>";
                    echo "<th>Actions</th>"; 
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["BrandName"] . "</td>";
                        echo "<td>";
                        // Action buttons with JavaScript to update status
                        echo '<a href="edit-brand.php?id=' . $row["id"] . '" class="action-btn edit-btn" title="Edit"><i class="fa fa-pencil-alt"></i></a>';
                        // echo '<a href="javascript:void(0);" onclick=deleteVehicle("' . $row["id"] . '")' . '" ></a>';
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

            // Include database connection and configuration
            require("includes/config.php");

            // Fetch initial vehicles from the database
            $sql = "SELECT id, BrandName FROM tblbrands";
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
        xhr.open('POST', 'search-brand.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('vehicle_details').innerHTML = xhr.responseText;
            }
        };
        xhr.send('search_term=' + search_term);
    });

    function deleteBrand(brandId) {
        var confirmDelete = confirm("Are you sure you want to delete this brand?");
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
            xhr.send('action=deleteBrand&brandId=' + brandId); // Send action and brandId as query parameters
        }
    }
</script>
</html>
