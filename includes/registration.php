<?php
require("config.php");

if(isset($_POST['signup'])) {
    $fname = $_POST['fullname'];
    $email = $_POST['emailid']; 
    $mobile = $_POST['mobileno'];
    $password = md5($_POST['password']); 

    $sql = "INSERT INTO tblusers (FullName, EmailId, ContactNo, Password,Status) VALUES (?, ?, ?, ?,1)";
    $query = $conn->prepare($sql);
    $query->bind_param("ssss", $fname, $email, $mobile, $password);
    $query->execute();
    $lastInsertId = $conn->insert_id;

    if($lastInsertId) {
        echo "<script>alert('Registration successful. Now you can login');</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again');</script>";
    }
}
?>
<script>
function checkAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
        url: "check-availability.php",
        data: 'emailid=' + $("#emailid").val(),
        type: "POST",
        success: function(data) {
            $("#user-availability-status").html(data);
            $("#loaderIcon").hide();
        },
        error: function() {}
    });
}

function valid() {
    if (document.signup.password.value != document.signup.confirmpassword.value) {
        alert("Password and Confirm Password Field do not match !!");
        document.signup.confirmpassword.focus();
        return false;
    }
    return true;
}
</script>
<div class="signup-container">
    <h2>Sign Up</h2>
    <form method="post" name="signup" onSubmit="return valid();">
        <div class="form-group">
            <label for="fullname">Full Name:</label>
            <input type="text" class="form-control" name="fullname" required="required">
        </div>
        <div class="form-group">
            <label for="mobileno">Mobile Number:</label>
            <input type="text" class="form-control" name="mobileno" maxlength="10" required="required">
        </div>
        <div class="form-group">
            <label for="emailid">Email Address:</label>
            <input type="email" class="form-control" name="emailid" id="emailid" onBlur="checkAvailability()" required="required">
            <span id="user-availability-status" style="font-size:12px;"></span> 
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" required="required">
        </div>
        <div class="form-group">
            <label for="confirmpassword">Confirm Password:</label>
            <input type="password" class="form-control" name="confirmpassword" required="required">
        </div>
        <div class="form-group checkbox">
            <input type="checkbox" id="terms_agree" required="required" checked="">
            <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
        </div>
        <div class="form-group">
            <input type="submit" value="Sign Up" name="signup" class="btn btn-block">
        </div>
    </form>
    <p>Already have an account? <a href="#" id="loginLinkFromSignup">Login Here</a></p>
</div>
<script>
    document.getElementById('loginLinkFromSignup').addEventListener('click', function(event) {
        event.preventDefault();
        closeModal('signupModal');
        openModal('loginModal');
    });
</script>
