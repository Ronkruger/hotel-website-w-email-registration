<?php
session_start();
include('includes/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/vendor/autoload.php';

// Check if user is logged in and email change OTP is set
if (!isset($_SESSION['hbmsuid']) || !isset($_SESSION['email_change_otp'])) {
    header('location:logout.php');
    exit;
}

$uid = $_SESSION['hbmsuid'];
$expected_otp = $_SESSION['email_change_otp'];
$new_email = $_SESSION['new_email'];

// Handle OTP verification
if (isset($_POST['verify_otp'])) {
    $user_otp = $_POST['otp'];

    if ($user_otp == $expected_otp) {
        // Update email in database
        $sql = "UPDATE tbluser SET Email=:new_email WHERE ID=:uid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':new_email', $new_email, PDO::PARAM_STR);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);

        if ($query->execute()) {
            // Clear session variables
            unset($_SESSION['email_change_otp']);
            unset($_SESSION['new_email']);

            echo '<script>alert("Email successfully changed")</script>';
            echo '<script>window.location.href = "profile.php"</script>';
            exit;
        } else {
            echo '<script>alert("Failed to update email")</script>';
        }
    } else {
        echo '<script>alert("Invalid OTP. Please try again.")</script>';
    }
}

// Resend OTP functionality
if (isset($_POST['resend_otp'])) {
    // Regenerate OTP
    $new_otp = sprintf('%06d', rand(0, 999999));
    $_SESSION['email_change_otp'] = $new_otp;

    // Fetch user details
    $sql = "SELECT FullName FROM tbluser WHERE ID=:uid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Send new OTP
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'romslantano@gmail.com';
    $mail->Password   = 'yupg ivih bvzu fyxx';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    
    $mail->setFrom('romslantano@gmail.com');
    $mail->addAddress($new_email);
    
    $mail->isHTML(true);
    $mail->Subject = 'Email Change Verification - New OTP';
    $mail->Body = "<h2>Email Change Verification</h2>
                   <p>Your new OTP for changing email is: $new_otp</p>";
    
    try {
        $mail->send();
        echo '<script>alert("New OTP sent to your email")</script>';
    } catch (Exception $e) {
        echo '<script>alert("Failed to send OTP")</script>';
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Hotel Booking Management System</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .verification-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 450px;
        }

        .verification-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .verification-header h2 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .verification-header p {
            color: #6c757d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .btn-verify {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-verify:hover {
            background-color: #0056b3;
        }

        .resend-otp {
            text-align: center;
            margin-top: 15px;
        }

        .resend-otp a {
            color: #007bff;
            text-decoration: none;
        }

        .resend-otp a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-header">
            <h2>Email Verification</h2>
            <p>Enter the 6-digit OTP sent to <?php echo htmlspecialchars($new_email); ?></p>
        </div>

        <form method="post">
            <div class="form-group">
                <input 
                    type="text" 
                    class="form-control" 
                    name="otp" 
                    placeholder="Enter 6-digit OTP" 
                    required 
                    maxlength="6" 
                    minlength="6" 
                    pattern="\d{6}"
                >
            </div>

            <button type="submit" name="verify_otp" class="btn-verify">Verify OTP</button>

            <div class="resend-otp">
                <form method="post">
                    <p>Didn't receive the OTP? 
                        <button type="submit" name="resend_otp" style="background:none; border:none; color:#007bff; text-decoration:underline; cursor:pointer;">
                            Resend OTP
                        </button>
                    </p>
                </form>
            </div>
        </form>
    </div>
</body>
</html>