<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

include('includes/dbconnection.php');
session_start();
error_reporting(0);

if (strlen($_SESSION['hbmsuid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $booknum = mt_rand(100000000, 999999999);
        $rid = intval($_GET['rmid']);
        $uid = $_SESSION['hbmsuid'];
        $idtype = $_POST['idtype'];
        $address = $_POST['address'];
        $checkindate = $_POST['checkindate'];
        $checkoutdate = $_POST['checkoutdate'];
        $downPay = $_POST['downPay'];
        $payment_mode = $_POST['payment_mode'];

        // Fetch room price from the database based on the room ID ($rid)
        // Replace this with your actual code to fetch the room price
        $roomPrice = 800; // Example room price

        // Calculate 50% of the room price
        $fiftyPercent = $roomPrice * 0.5;

        $cdate = date('Y-m-d');
        if ($checkindate <  $cdate) {
            echo '<script>alert("Check in date must be greater than current date")</script>';
        } else if ($checkindate > $checkoutdate) {
            echo '<script>alert("Check out date must be equal to / greater than check in date")</script>';
        } else if ($downPay != $fiftyPercent) {
            echo '<script>alert("The down payment must be 50% of the room price.")</script>';
        } else {
            $sql = "INSERT INTO tblbooking(RoomId, BookingNumber, UserID, IDType, Address, CheckinDate, CheckoutDate, downPay, payment_mode) 
            VALUES (:rid, :booknum, :uid, :idtype, :address, :checkindate, :checkoutdate, :downPay, :payment_mode)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':rid', $rid, PDO::PARAM_STR);
            $query->bindParam(':booknum', $booknum, PDO::PARAM_STR);
            $query->bindParam(':uid', $uid, PDO::PARAM_STR);
            $query->bindParam(':idtype', $idtype, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':checkindate', $checkindate, PDO::PARAM_STR);
            $query->bindParam(':checkoutdate', $checkoutdate, PDO::PARAM_STR);
            $query->bindParam(':downPay', $downPay, PDO::PARAM_STR);
            $query->bindParam(':payment_mode', $payment_mode, PDO::PARAM_STR);
            $query->execute();

            $LastInsertId = $dbh->lastInsertId();
            if ($LastInsertId > 0) {
                // Send email confirmation
  

                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.elasticemail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phantomhivehotel7@gmail.com';
                    $mail->Password = 'BB9A1AE0DA1ACA570FAD3C656C4388F0F488';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 2525;

                    //Recipients
                    $mail->setFrom('phantomhivehotel7@gmail.com');
                    $mail->addAddress($_POST['email']);

                    // Content
                    $mail->isHTML(true); // Set email format to HTML
                    $mail->Subject = 'Booking Confirmation';
                    $template  = "
                       <h2>You have successfully booked a room with us</h2>
                       <p>Booking Number: $booknum</p>
                       <p>Check-in Date: $checkindate</p>
                       <p>Check-out Date: $checkoutdate</p>
                       <p>Down Payment:  $downPay z</p>
                       <p>Thank you for choosing our hotel.</p>
                   ";
                    $mail->Body = $template;

                    $mail->send();
                    echo '<script>alert("Your room has been booked successfully. Booking Number is ' . $booknum . '");window.location.href ="index.php"</script>';
                } catch (Exception $e) {
                    echo '<script>alert("Error sending email: ' . $mail->ErrorInfo . '");</script>';
                }
            } else {
                echo '<script>alert("Something Went Wrong. Please try again")</script>';
            }
        }
    }
}
?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Hotel :: Book Room</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />

    <script type="application/x-javascript">addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
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
    <style>
        .book{
            border: 1px solid black;
            display: flex;
  flex-direction: column;
  justify-content: center;
 align-content: center;
        }
    </style>
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
                <h2>Book Your Room</h2>
                <div class="contact-grids">
                    <div class="col-md-6 contact-right">
                        <form method="post" class="book">
                            <?php
                            $uid = $_SESSION['hbmsuid'];
                            $sql = "SELECT * from  tbluser where ID=:uid";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            $cnt = 1;
                            if ($query->rowCount() > 0) {
                                foreach ($results as $row) {
                                    ?>
                                    <h5>Name</h5>
                                    <input type="text" value="<?php echo $row->FullName;?>" name="name" class="form-control" required="true" readonly="true">
                                    <h5>Mobile Number</h5>
                                    <input type="text" name="phone" class="form-control" required="true" maxlength="10" pattern="[0-9]+" value="<?php echo $row->MobileNumber;?>" readonly="true">
                                    <h5>Email Address</h5>
                                    <input  type="email" value="<?php echo $row->Email;?>" class="form-control" name="email" required="true" readonly="true"><?php $cnt=$cnt+1;}} ?>
                                    <h5>ID Type</h5>
                                    <select  type="text" value="" class="form-control" name="idtype" required="true" class="form-control">
                                        <option value="">Choose ID Type</option>
                                        <option value="Voter Card">Voter Card</option>
                                        <option value="Adhar Card">Adhar Card</option>
                                        <option value="Driving Licence Card">Driving Licence Card</option>
                                        <option value="Passport">Passport</option>
                                    </select>
                                    <br>
                                    <br>
                                    <br>
                                    <h5>Address</h5>
                                    <textarea type="text" rows="10" name="address" required="true"></textarea>
                                    <h5>Checkin Date</h5>
                                    <input  type="date" value="" class="form-control" name="checkindate" required="true">
                                    <h5>Checkout Date</h5>
                                    <input  type="date" value="" class="form-control" name="checkoutdate" required="true">
                                    <br>
                                    <h5 style="color: orange;">Down Payment(500 pesos)</h5>
                                    <br>
                                    <input type="text" value="" class="form-control" name="downPay" required="true">
                                    <br>
                                    <input type="submit" value="send" name="submit">
                                </form>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <?php include_once('includes/getintouch.php');?>
                </div>
                <?php include_once('includes/footer.php');?>
            </body>
            </html>
            <?php   ?>
