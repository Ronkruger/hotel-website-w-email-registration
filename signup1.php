<?php
session_start();

include('includes/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/PHPMailer/src/SMTP.php';

require __DIR__ . '/vendor/autoload.php';

$message = [];

function generateOTP()
{
    // Generate a random 6-digit OTP
    return sprintf('%06d', rand(0, 999999));
}

function sendmail_verify($name, $email, $otp)
{
    $mail = new PHPMailer(true);
	$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'thegrandroyal7@gmail.com';
    $mail->Password   = 'zrzkvedqizavowxo';
    $mail->SMTPSecure = 'ssl';  
	$mail->Port       = 465;

    $mail->setFrom('thegrandroyal7@gmail.com');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Account Activation';
    $template  = "
       <h2>You have registered with hotel reservation</h2>
       <h2>Your OTP for verification is: $otp</h5>
   ";
    $mail->Body = $template;

    try {
        $mail->send();
    } catch (Exception $e) {
        // Log any error message if needed
        $message[] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $mobno = $_POST['mobno'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword']; // Confirm Password
    $otp = generateOTP(); // Generate OTP
    $verify_status = 0; // Set verify status to 0 by default

    // Ensure passwords match
    if ($password != $cpassword) {
        echo "<script>alert('Passwords do not match');</script>";
        exit; // Exit the script if passwords don't match
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle profile image upload
$target_dir = "uploads/pfp/";
$target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is an actual image or fake image
if (!getimagesize($_FILES["profile_image"]["tmp_name"])) {
    echo "<script>alert('File is not an image.');</script>";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["profile_image"]["size"] > 1000000) {
    echo "<script>alert('Sorry, your file is too large.');</script>";
    $uploadOk = 0;
}

// Allow certain file formats
$allowed_extensions = ["jpg", "png", "jpeg", "gif"];
if (!in_array($imageFileType, $allowed_extensions)) {
    echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "<script>alert('Sorry, your file was not uploaded.');</script>";
// if everything is ok, try to upload file
} else {
    // Extract only the filename without the directory path
    $target_file = basename($_FILES["profile_image"]["name"]);
    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_dir . $target_file)) {
        // File uploaded successfully
        // Proceed with other operations like database insertion
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
    }
}


    $ret = "SELECT Email FROM tbluser WHERE Email=:email";
    $query = $dbh->prepare($ret);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() == 0) {
        $sql = "INSERT INTO tbluser(FullName, MobileNumber, Email, Password, otp, verify_status, profile_image) VALUES (:fname,:mobno,:email,:password, :otp, :verify_status, :profile_image)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobno', $mobno, PDO::PARAM_INT);
        $query->bindParam(':password', $hashed_password, PDO::PARAM_STR); // Use hashed password
        $query->bindParam(':otp', $otp, PDO::PARAM_INT);
        $query->bindParam(':verify_status', $verify_status, PDO::PARAM_INT);
        $query->bindParam(':profile_image', $target_file, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            sendmail_verify($fname, $email, $otp);
            // Set session email
            $_SESSION['email'] = $email;
            echo "<script>alert('You have successfully registered with us. Please check your email for OTP verification.');</script>";
            echo "<script>window.location.href = 'otp_verify.php';</script>"; // Redirect to otp_verify.php
            exit; // Exit to prevent further execution
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";
        }
    } else {
        echo "<script>alert('Email-id already exists. Please try again');</script>";
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | sign up</title>
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
    <style>
input[type=email],input[type=password],input[type=text],input[type=file] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 28px;
  box-sizing: border-box;
}
        .signin {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}
input[type=submit] {
  width: 100%;
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 28px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #45a049;
}
.signin{
    /* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 28px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);
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
<div class="content-signin">
    <div class="contact">
        <div class="container">
            <h2>Registration</h2>
            <div class="contact-grids-signin">
                <div class="col-md-4 contact-signin">
                    <form method="post" enctype="multipart/form-data" onsubmit="redirectToOtpVerify()" class="signin">
                        <h5>Full Name</h5>
                        <input type="text" value="" name="fname" required="true" class="form-control">
                        <h5>Mobile Number</h5>
                        <input type="text" name="mobno" class="form-control" required="true" maxlength="11" pattern="[0-9]+">
                        <h5>Email Address</h5>
                        <input type="email" class="form-control" value="" name="email" required="true">
                        <h5>Password</h5>
                        <input type="password" value="" class="form-control" name="password" required="true">
                        <h5>Confirm Password</h5>
                        <input type="password" value="" class="form-control" name="cpassword" required="true">
                        <h5>Profile Image</h5>
                        <input type="file" name="profile_image" class="form-control" required="true">
                        <br />
                        <input type="submit" value="Sign Up" name="submit">
                    </form>
                    Already Registered? <a href="signin.php" style="color: red">Sign in</a>
                    <br/>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <?php include_once('includes/getintouch.php');?>
</div>
<?php include_once('includes/footer.php');?>
</body>
</html>
