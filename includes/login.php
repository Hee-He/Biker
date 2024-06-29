<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("config.php");

$loginError = '';

// Check if the login form is submitted
if (isset($_POST['login'])) {
    // Retrieve user inputs
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Example hashing; consider more secure options

    // Query to check if the user exists in the database
    $sql = "SELECT id, FullName, Status FROM tblusers WHERE EmailId=? AND Password=?";
    $query = $conn->prepare($sql);
    $query->bind_param("ss", $email, $password);

    // Execute the query
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    // Check if a user was found with the provided credentials
    if ($result->num_rows == 1) {
        if ($row['Status'] === 1) {
            // Start session and store user information
            session_regenerate_id();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['FullName']; // Set the session variable for username

            // Redirect back to the original page or a default page
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            // Redirect with a query parameter indicating the account is blocked
            header("Location: index.php?blocked=true");
            exit();
        }
    } else {
        $loginError = "Invalid email or password";
    }
}
?>

<div class="login-container">
    <h2>Login</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">Email or Phone:</label>
            <input type="text" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <input type="submit" name="login" value="Login" class="btn btn-block">
        </div>
        <?php if ($loginError): ?>
            <div class="error-message"><?php echo $loginError; ?></div>
        <?php endif; ?>
    </form>
    <p>Don't have an account? <a href="#" id="signupLinkFromLogin">Signup Here</a></p>
    <p><a href="forgotpassword.php">Forgot Password?</a></p>
</div>

<script>
    document.getElementById('signupLinkFromLogin').addEventListener('click', function(event) {
        event.preventDefault();
        closeModal('loginModal');
        openModal('signupModal');
    });

    // Check if the account is blocked
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('blocked') === 'true') {
        alert("Your account is blocked by Admin!");
        window.location.href="index.php";
    }
</script>
