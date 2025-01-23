<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['submit'])) {
    // Fetch the concatenated OTP from the submitted form
    $otp = implode('', $_POST['otp']);  // Concatenate the OTP array
    $email = $_SESSION['email'];

    // Debug output: Echo out the session email for verification
    echo "Session Email: " . htmlspecialchars($email) . "<br>"; // Use htmlspecialchars to prevent XSS
    echo "Submitted OTP: " . htmlspecialchars($otp) . "<br>"; // Show the OTP being submitted

    $sql = "SELECT * FROM tbluser WHERE Email=:email AND otp=:otp AND verify_status=0";
    echo "SQL: " . htmlspecialchars($sql) . "<br>"; // San ```php
    // Sanitize SQL output for safety

    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':otp', $otp, PDO::PARAM_STR);
    $query->execute();

    // Debug output: Echo out the number of rows fetched for verification
    echo "Number of Rows Fetched: " . $query->rowCount() . "<br>";

    if ($query->rowCount() > 0) {
        $sql = "UPDATE tbluser SET verify_status=1 WHERE Email=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        
        echo "<script>alert('Congratulations! Your account has been verified successfully. You can now log in.');</script>";
        session_destroy();
        header('location:signin.php');
        exit(); // Always exit after redirecting
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<style>
    html {
        height: 100%;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background-color: #f7f7f7; /* Light background for a clean look */
        display: flex;
        align-items: center; /* Vertically center the login box */
        justify-content: center; /* Horizontally center the login box */
        min-height: 100vh; /* Full viewport height */
    }
    .login-box {
        width: 400px;
        padding: 40px;
        background: #fff; /* White background for the form */
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .login-box h2 {
        margin: 0 0 30px;
        padding: 0;
        color: #333; /* Dark text for high contrast */
        text-align: center;
    }
    .otp-input {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .otp-input input {
        width: 60px;
        height: 60px;
        font-size: 24px;
        text-align: center;
        border: 1px solid #ddd; /* Light border for better aesthetics */
        border-radius: 5px;
        background: #f9f9f9; /* Light background for inputs */
        color: #333; /* Dark text for readability */
        transition: border-color 0.3s; /* Smooth transition for border color */
    }
    .otp-input input:focus {
        border-color: #3f51b5; /* Blue color on focus for better visibility */
        outline: none;
    }
    .but {
        background: #3f51b5; /* Primary button color */
        color: white;
        width: 100%;
        height: 40px;
        border: none; /* Remove default border */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s; /* Smooth transition for button hover */
    }
    .but:hover {
        background: #303f9f; /* Darker shade on hover */
    }
</style>

<div class="login-box">
    <h2>OTP Verification</h2>
    <form method="post">
        <div class="otp-input">
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
            <input type="text" name="otp[]" maxlength="1" required oninput="moveFocus(this, event)" />
        </div>
        <div>
            <input type="submit" value="Enter OTP" name="submit" class="but">
        </div>
    </form>
</div>

<script>
    function moveFocus(currentInput, event) {
        if (event.inputType === 'insertText') {
            const next Input = currentInput.nextElementSibling;
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