<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit']))
{
    $otp=$_POST['otp'];
    if(isset($_GET['email'])) {
        $email=$_GET['email']; // Get email from URL parameter
        $_SESSION['email'] = $email; // Set email in session
    } else {
        // Handle the case where email is not set in URL parameter
        echo "<script>alert('Email not found.');</script>";
        exit(); // Stop further execution
    }

    // Debugging statement to output email and OTP
    echo "<script>console.log('Email: $email, OTP: $otp');</script>";

    $sql = "SELECT * FROM tbluser WHERE Email=:email AND otp=:otp";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':otp', $otp, PDO::PARAM_STR);
    $query->execute();

    // Debugging statement to output SQL query
    echo "<script>console.log('SQL Query: $sql');</script>";

    if($query->rowCount() > 0)
    {
        // Valid OTP, allow password reset
        // Redirect to password reset page
        $_SESSION['reset_email'] = $email;
        header('location: reset_password.php');
        exit();
    }
    else
    {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | OTP Verification for Password Reset</title>
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
    <div class="header head-top">
    <div class="container">
        <?php include_once('includes/header.php');?>
    </div>
</div>


    <div class="content">
        <div class="contact">
            <div class="container">
                <h2>OTP Verification for Password Reset</h2>
                <div class="contact-grids">
                    <div class="col-md-6 contact-right">
                        <form method="post">
                            <h5>Enter OTP</h5>
                            <input type="text" name="otp" class="form-control" required="true" maxlength="6" pattern="\d{6}">
                            <br />
                            <input type="submit" value="Verify OTP" name="submit">
                        </form>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer and other HTML content -->
    <?php include_once('includes/footer.php');?>
</body>
</html>
