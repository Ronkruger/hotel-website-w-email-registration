<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit']))
{
    $otp=$_POST['otp'];
    $email=$_SESSION['email'];

    // Echo out the session email for verification
    echo "Session Email: " . $email . "<br>";

    $sql = "SELECT * FROM tbluser WHERE Email=:email AND otp=:otp AND verify_status=0";
    echo "SQL: " . $sql . "<br>"; // Echo out the SQL query for verification
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':otp', $otp, PDO::PARAM_STR); // Ensure parameter type matches with OTP in the database
    $query->execute();

    // Echo out the number of rows fetched for verification
    echo "Number of Rows Fetched: " . $query->rowCount() . "<br>";

    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0)
    {
        $sql = "UPDATE tbluser SET verify_status=1 WHERE Email=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Congratulations! Your account has been verified successfully. You can now log in.');</script>";
        session_destroy();
        header('location:signin.php');
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
    <title>Hotel Booking Management System | OTP Verification</title>
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
    </script>
</head>
<body>
<!--header-->
<div class="header head-top">
    <div class="container">
        <?php include_once('includes/header.php');?>
    </div>
</div>
<!--header-->
<!--about-->
<div class="content">
    <div class="contact">
        <div class="container">
            <h2>OTP Verification</h2>
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
<?php include_once('includes/footer.php');?>
</html>
