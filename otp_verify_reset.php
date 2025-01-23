<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('includes/dbconnection.php');

// Log session data for debugging
error_log("Debug - Session Data: " . json_encode($_SESSION));

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Concatenate OTP values from the input fields
    $otp = implode('', $_POST['otp']);

    // Log the submitted OTP for debugging
    error_log("Debug - Submitted OTP: {$otp}");

    // Check if the email is set in the session
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
    } else {
        error_log("Error - Email session not set.");
        echo "<script>alert('Email not found. Please re-enter your email to get a new OTP.');</script>";
        exit();
    }

    // Log the email being verified
    error_log("Debug - Email: {$email}");

    // Fetch the OTP from the database
    $sql = "SELECT otp FROM tbluser WHERE Email = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $savedOtp = $result['otp'];

        // Log the fetched OTP for debugging
        error_log("Debug - Fetched OTP from DB: {$savedOtp}");

        // Compare the submitted OTP with the saved OTP
        if ($otp === $savedOtp) {
            $_SESSION['reset_email'] = $email; // Store email for password reset
            header('Location: reset_password.php'); // Redirect to the reset password page
            exit();
        } else {
            error_log("Error - Invalid OTP. Submitted: {$otp}, Saved: {$savedOtp}");
            echo "<script>alert('Invalid OTP. Please try again.');</script>";
        }
    } else {
        error_log("Error - No user found with the email: {$email}");
        echo "<script>alert('No user found with the given email. Please check and try again.');</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
        .otp-input {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .otp-input input {
            width: 40px;
            height: 40px;
            font-size: 24px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
        }
        .otp-input input:focus {
            border-color: #007bff;
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
    <h2>OTP Verification</h2>
    <form method="post" id="otpForm">
        <div class="otp-input">
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
        </div>
        <button type="submit" name="submit">VERIFY OTP</button>
    </form>
</div>
<script>
    function moveFocus(currentInput, event) {
        if (event.inputType === 'insertText') {
            const nextInput = currentInput.nextElementSibling;
            if (nextInput) {
                nextInput.focus();
            }
        } else if (event.inputType === 'deleteContentBackward') {
            const previousInput = currentInput.previousElementSibling;
            if (previousInput) {
                previousInput.focus();
            }
        }
    }
</script>
</body>
</html>
