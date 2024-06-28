<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
?>


    <nav class="ts-sidebar">
        <ul class="ts-sidebar-menu">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li>
                <a href="#" onclick="toggleMenu('brandsMenu')"><i class="fa fa-files-o"></i> Brands</a>
                <ul id="brandsMenu" class="submenu">
                    <li><a href="create-brand.php">Create Brand</a></li>
                    <li><a href="manage-brands.php">Manage Brands</a></li>
                </ul>
            </li>
            <li>
                <a href="#" onclick="toggleMenu('vehiclesMenu')"><i class="fa fa-motorcycle" aria-hidden="true"></i> Vehicles</a>
                <ul id="vehiclesMenu" class="submenu">
                    <li><a href="post-a-vehicle.php">Post a Vehicle</a></li>
                    <li><a href="manage-vehicles.php">Manage Vehicles</a></li>
                </ul>
            </li>
            <li><a href="manage-bookings.php"><i class="fa fa-book" aria-hidden="true"></i> Manage Booking</a></li>
            <li><a href="manage-users.php"><i class="fa-solid fa-users"></i> Reg Users</a></li>

            <?php if (isset($_SESSION["admin"])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-user-tie"></i> <?php echo $_SESSION["admin"]; ?>
                    </a>
                    <div class="dropdown-content">
                        <a class="dropdown-item" href="#">Profile Update</a>
                        <a class="dropdown-item" href="update-password.php">Password</a>
                        <a class="dropdown-item" href="my-booking.php">My Booking</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="loginLink">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <!-- Admin dashboard content goes here -->
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <?php include 'includes/login.php'; ?>
        </div>
    </div>

    <script>
        function toggleMenu(menuId) {
            var menu = document.getElementById(menuId);
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var loginLink = document.getElementById('loginLink');
            if (loginLink) {
                loginLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    openModal('loginModal');
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
            if (event.target == loginModal) {
                closeModal('loginModal');
            }
        }
    </script>
<link rel="stylesheet" href="assets/css/style.css?v=3">