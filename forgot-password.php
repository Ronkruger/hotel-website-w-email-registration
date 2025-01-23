<?php
session_start();
error_reporting(E_ALL); // Enable all error reporting for debugging
include('includes/dbconnection.php');
require 'vendor/autoload.php'; // Include PHPMailer autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

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
    if ($query->rowCount() > 0) {
        $_SESSION['email'] = $email; // Store email in session

        // Send OTP via email using PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'luxestay4@gmail.com';
    $mail->Password   = 'bzgs zncv snjv srwb'; 
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('luxestay4@gmail.com', 'Your App'); // Sender's email and name
        $mail->addAddress($email); // Recipient's email

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body    = 'Your OTP for password reset is: ' . $otp;

        try {
            $mail->send();
            header('Location: otp_verify_reset.php'); // Redirect to OTP verification page
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Email ID or Mobile number is invalid.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 400px;
            padding: 40px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .user-box {
            position: relative;
            margin-bottom: 20px;
        }

        .user-box input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
            box-shadow: none;
        }

        .user-box input:focus {
            border-color: #007bff;
        }

        .user-box label {
            position: absolute;
            top: 10px;
            left: 15px;
            color: #777;
            transition: 0.3s;
            pointer-events: none;
        }

        .user-box input:focus + label,
        .user-box input:not(:placeholder-shown) + label {
            top: -10px;
            left: 12px;
            color: #007bff;
            font-size: 12px;
        }

        .login-box button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-box button:hover {
            background: #0056b3;
        }

        @media (max-width: 450px) {
            .login-box {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Reset Your Password</h2>
    <form method="post">
        <div class="user-box">
            <input type="email" name="email" required="true" placeholder=" ">
            <label>Email</label>
        </div>
        <div class="user-box">
            <input type="text" name="mobile" required="true" placeholder=" ">
            <label>Mobile Number</label>
        </div>
        
        <button type="submit" name="submit">Send OTP</button>
    </form>
</div>

</body>
</html>