<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">Your Logo / Brand</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="aboutus.php">About Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="bike-list.php">Bike List</a>
            </li>
            <?php if (isset($_SESSION["username"])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button">
                        <?php echo $_SESSION["username"]; ?>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">Profile Update</a>
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
                <li class="nav-item">
                    <a class="nav-link" href="#" id="signupLink">Signup</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('loginModal')">&times;</span>
        <?php include 'includes/login.php'; ?>
    </div>
</div>

<!-- Signup Modal -->
<div id="signupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('signupModal')">&times;</span>
        <?php include 'includes/registration.php'; ?>
    </div>
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
</script>

<link rel="stylesheet" href="assets/css/style.css">
