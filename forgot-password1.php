<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
require 'vendor/autoload.php'; // Include PHPMailer autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['submit']))
{
    $email=$_POST['email'];
    $mobile=$_POST['mobile'];

    // Generate OTP
    $otp = sprintf('%06d', rand(0, 999999));

    // Update OTP in the database
    $sql = "UPDATE tbluser SET otp=:otp WHERE Email=:email AND MobileNumber=:mobile";
    $query = $dbh->prepare($sql);
    $query->bindParam(':otp', $otp, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->execute();

    // Check if query was successful
    if($query->rowCount() > 0) {
        // Send OTP via email using PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.elasticemail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'phantomhivehotel7@gmail.com';
        $mail->Password   = 'BB9A1AE0DA1ACA570FAD3C656C4388F0F488';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 2525;

        $mail->setFrom('phantomhivehotel7@gmail.com');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body    = 'Your OTP for password reset is: ' . $otp;

        try {
            $mail->send();
            // Redirect to OTP verification page
            header('Location: otp_verify_reset.php?email=' . $email);
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        // Output debugging information
        echo "<script>alert('Email ID or Mobile number is invalid.');</script>";
        echo "<script>console.log('SQL Query: $sql');</script>";
    }
}
?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Hotel :: Forgot Password Page</title>
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
                <h2>Reset Your Password</h2>
                <div class="contact-grids">
                    <div class="col-md-6 contact-right">
                        <form method="post">
                            <h5>Email Address</h5>
                            <input type="email" placeholder="Email address" class="form-control" value="" name="email" required="true">
                            <h5>Mobile Number</h5>
                            <input type="text" placeholder="Mobile Number" class="form-control" name="mobile" required="true">
                            <br />
                            <input type="submit" value="Send OTP" name="submit">
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
