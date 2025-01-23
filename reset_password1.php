<?php
session_start();
include('includes/dbconnection.php');

if(!isset($_SESSION['reset_email'])) {
    // If reset_email session variable is not set, redirect to the forgot password page
    header('location: forgot-password.php');
    exit();
}

if(isset($_POST['submit'])) {
    $email = $_SESSION['reset_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Update the password in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE tbluser SET Password=:password WHERE Email=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        if($query->rowCount() > 0) {
            // Password updated successfully, redirect to login page
            unset($_SESSION['reset_email']); // Clear the reset_email session variable
            echo "<script>alert('Password reset successful. You can now login with your new password.');</script>";
            header('location: signin.php');
            exit();
        } else {
            echo "<script>alert('Password reset failed. Please try again later.');</script>";
        }
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Reset Password</title>
    <!-- Add your CSS and JavaScript links here -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/responsiveslides.min.js"></script>
    <script>
        $(function () {
            $("#slider").responsiveSlides({
                auto: true,
                nav: true,
                speed: 500,
                namespace: "callbacks",
                pager: true,
            });
        });

        // Function to redirect to otp_verify.php after form submission
        function redirectToOtpVerify() {
            window.location.href = "otp_verify.php";
        }
    </script>
</head>
<body>
    <!-- Header and other HTML content -->

    <div class="content">
        <div class="contact">
            <div class="container">
                <h2>Reset Password</h2>
                <div class="contact-grids">
                    <div class="col-md-6 contact-right">
                        <form method="post">
                            <h5>New Password</h5>
                            <input type="password" name="new_password" class="form-control" required>
                            <h5>Confirm Password</h5>
                            <input type="password" name="confirm_password" class="form-control" required>
                            <br />
                            <input type="submit" value="Reset Password" name="submit">
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer and other HTML content -->
    <?php include_once('includes/getintouch.php');?>
</div>
<?php include_once('includes/footer.php');?>
</body>
</html>
