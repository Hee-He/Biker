<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require("config.php");

$loginError = '';

// Check if the login form is submitted
if(isset($_POST['login'])) {
    // Retrieve user inputs
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Example hashing; consider more secure options

    // Query to check if the user exists in the database
    $sql = "SELECT UserName FROM admin WHERE UserName=? AND Password=?";
    $query = $conn->prepare($sql);
    $query->bind_param("ss", $username, $password);

    // Execute the query
    $query->execute();
    $result = $query->get_result();

    // Check if a user was found with the provided credentials
    if ($result->num_rows == 1) {
        // Start session and store user information
        $row = $result->fetch_assoc();
        $_SESSION['admin'] = $row['UserName']; // Set the session variable for username

        // Redirect back to the original page or a default page
        header("Location: dashboard.php");
        exit();
    } else {
        $loginError = "Invalid Username or password";
    }
}
?>

<div class="login-container">
    <h2>Admin Login</h2>
    <form method="post">
        <div class="form-group">
            <label for="email">Username:</label>
            <input type="text" class="form-control" id="email" name="username" required>
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
    
    <p><a href="forgotpassword.php">Forgot Password?</a></p>
</div>


