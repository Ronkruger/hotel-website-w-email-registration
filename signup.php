<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'luxestay4@gmail.com';
    $mail->Password   = 'bzgs zncv snjv srwb'; // Consider using environment variables
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('luxestay4@gmail.com');
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

// Process form submission
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
    // (Omitted for brevity, keep your existing code)

    // Check if email already exists in the database
    $ret = "SELECT Email FROM tbluser WHERE Email=:email";
    $query = $dbh->prepare($ret);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() == 0) {
        // Insert user data into the database
        $sql = "INSERT INTO tbluser(FullName, MobileNumber, Email, Password, otp, verify_status, profile_image) VALUES (:fname,:mobno,:email,:password, :otp, :verify_status, :profile_image)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobno', $mobno, PDO::PARAM_INT);
        $query->bindParam(':password', $hashed_password, PDO::PARAM_STR); // Use hashed password
        $query->bindParam(':otp', $otp, PDO::PARAM_INT);
        $query->bindParam(':verify_status', $verify_status, PDO::PARAM_INT);
        $query->bindParam(':profile_image', $target_file, PDO::PARAM_STR); // Store the filename with directory path
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            // Send verification email
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
        echo "<script>alert('Email id already exists. Please try again');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        /* Same styles as before, plus additional styling for requirements */
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

        .user-box.label-error {
            border-color: red;
        }

        .user-box.label-success {
            border-color: green;
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

        .password-requirements {
            margin-top: 10px;
            text-align: left;
            color: #666;
            font-size: 14px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px;
        }

        .requirement {
            display: flex;
            align-items: center;
        }

        .requirement input {
            margin-right: 10px;
            cursor: default;
        }

        .requirement.checked {
            color: green;
        }

        .requirement.unchecked {
            color: red;
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
    <h2>Signup</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="user-box">
            <input type="text" name="fname" required="true" placeholder=" ">
            <label>Full Name</label>
        </div>
        <div class="user-box">
            <input type="email" name="email" required="true" placeholder=" ">
            <label>Email</label>
        </div>
        <div class="user-box">
            <input type="text" name="mobno" required="true" placeholder=" ">
            <label>Mobile Number</label>
        </div>
        <div class="user-box">
            <input type="password" name="password" required="true" id="password" placeholder=" ">
            <label>Password</label>
        </div>   
        <div class="user-box">
            <input type="password" name="cpassword" required="true" id="cpassword" placeholder=" ">
            <label>Confirm Password</label>
        </div>   
        <div class="user-box">
            <input type="file" name="profile_image" required="true" id="profile_image" style="border: none; padding: 10px;">
            <label for="profile_image" style="color: #777;">Profile Image</label>
        </div>
        
        <div class="password-requirements">
            <p><strong>Password Requirements:</strong></p>
            <ul>
                <li class="requirement"><input type="checkbox" id="length" disabled>At least 8 characters long</li>
                <li class="requirement"><input type="checkbox" id="uppercase" disabled>Starts with a capital letter</li>
                <li class="requirement"><input type="checkbox" id="special" disabled>No special characters allowed</li>
            </ul>
        </div>

        <button type="submit" name="submit">Signup</button>
    </form>

    <div class="footer-text">
        Already have an account? Login <a href="signin.php">here.</a>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('cpassword');

    const lengthCheckbox = document.getElementById('length');
    const uppercaseCheckbox = document.getElementById('uppercase');
    const specialCheckbox = document.getElementById('special');

    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validateConfirmPassword);

    function validatePassword() {
        const value = passwordInput.value;

        // Length check
        if (value.length >= 8) {
            lengthCheckbox.checked = true;
            lengthCheckbox.parentElement.classList.add('checked');
            lengthCheckbox.parentElement.classList.remove('unchecked');
        } else {
            lengthCheckbox.checked = false;
            lengthCheckbox.parentElement.classList.add('unchecked');
            lengthCheckbox.parentElement.classList.remove('checked');
        }

        // Uppercase check
        if (/^[A-Z]/.test(value)) {
            uppercaseCheckbox.checked = true;
            uppercaseCheckbox.parentElement.classList.add('checked');
            uppercaseCheckbox.parentElement.classList.remove('unchecked');
        } else {
            uppercaseCheckbox.checked = false;
            uppercaseCheckbox.parentElement.classList.add('unchecked');
            uppercaseCheckbox.parentElement.classList.remove('checked');
        }

        // Special character check
        if (/^[A-Za-z0-9]*$/.test(value)) {
            specialCheckbox.checked = true;
            specialCheckbox.parentElement.classList.add('checked');
            specialCheckbox.parentElement.classList.remove('unchecked');
        } else {
            specialCheckbox.checked = false;
            specialCheckbox.parentElement.classList.add('unchecked');
            specialCheckbox.parentElement.classList.remove('checked');
        }
    }

    function validateConfirmPassword() {
        if (passwordInput.value === confirmPasswordInput.value) {
            confirmPasswordInput.classList.remove('label-error');
            confirmPasswordInput.classList.add('label-success');
        } else {
            confirmPasswordInput.classList.add('label-error');
            confirmPasswordInput.classList.remove('label-success');
        }
    }
</script>

</body>
</html>