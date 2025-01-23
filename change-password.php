<?php
session_start();
include('includes/dbconnection.php');

if (empty($_SESSION['hbmsuid'])) {
    header('location: logout.php');
    exit(); // Add an exit after header redirection to terminate further execution
}

if (isset($_POST['submit'])) {
    $uid = $_SESSION['hbmsuid'];
    $currentPassword = $_POST['currentpassword'];
    $newPassword = $_POST['newpassword'];
    $confirmPassword = $_POST['confirmpassword'];

    // Check if new password matches confirm password
    if ($newPassword !== $confirmPassword) {
        echo '<script>alert("New password and confirm password do not match.")</script>';
    } else {
        // Fetch the user's current password from the database
        $sql = "SELECT Password FROM tbluser WHERE ID = :uid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $hashedPassword = $row['Password'];

            // Verify if the entered current password matches the hashed password from the database
            if (password_verify($currentPassword, $hashedPassword)) {
                // Hash the new password before updating it in the database
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $sql = "UPDATE tbluser SET Password = :newPassword WHERE ID = :uid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':newPassword', $hashedNewPassword, PDO::PARAM_STR);
                $query->bindParam(':uid', $uid, PDO::PARAM_STR);
                $query->execute();

                echo '<script>alert("Your password has been successfully changed.")</script>';
                echo "<script>window.location.href = 'index.php';</script>";
            } else {
                echo '<script>alert("Your current password is incorrect.")</script>';
            }
        } else {
            echo '<script>alert("User not found or database error occurred.")</script>';
        }
    }
}
?>

<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "hbmsdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch settings
$sql = "SELECT * FROM tblsettings WHERE id=1";
$result = $conn->query($sql);

// Default settings
$default_logo = "uploads/default_logo.png";
$default_bg_image = "uploads/default_bg.jpg"; 
$default_bg_color = "#ffffff";
$default_logo_text = "Phantom Hive";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $logo = $row['logo'] ? "uploads/" . $row['logo'] : $default_logo;
    $logo_text = $row['logo'] ? "" : $default_logo_text; 
    $background_image = $row['background_image'] ? "uploads/" . $row['background_image'] : $default_bg_image;
    $background_color = $row['background_color'] ? $row['background_color'] : $default_bg_color;
} else {
    $logo = $default_logo;
    $logo_text = $default_logo_text;
    $background_image = $default_bg_image;
    $background_color = $default_bg_color;
}

// Fetch font color
$get_font_color_sql = "SELECT font_color FROM tblsettings WHERE id = 1";
$get_font_color_result = $conn->query($get_font_color_sql);

if ($get_font_color_result->num_rows > 0) {
    $row = $get_font_color_result->fetch_assoc();
    $font_color = $row['font_color'];
} else {
    $font_color = '#000000'; // Use black if no color found
}

// Close the connection
$conn->close();
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Hotel Booking Management System</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all"/>
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
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
        .header-top {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 0;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        #logo {
            max-height: 70px; 
            width: auto; 
            border-radius: 20px;
            transition: transform 0.3s ease;
        }
        #logo:hover {
            transform: scale(1.05); /* Slight hover effect */
        }
        .login-box {
            width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.6);
            border-radius: 10px;
            text-align: center;
        }
        .login-box h2 {
            margin: 0 0 30px;
            text-align: center;
            color: black;
        }
        .user-box {
            position: relative;
            margin-bottom: 30px;
        }
        .user-box input {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
            color: #333;
            border: none;
            border-bottom: 1px solid #007bff;
            outline: none;
            background: transparent;
        }
        .user-box label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 16px;
            color: #333;
            pointer-events: none;
            transition: .5s;
        }
        .user-box input:focus ~ label,
        .user-box input:not(:placeholder-shown) ~ label {
            top: -20px;
            left: 0;
            color: #007bff; /* Color when focused or filled */
            font-size: 12px;
        }
        .but {
            background: #007bff;
            color: white;
            width: 100%;
            height: 40px; 
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }
        .but:hover {
            background: #0056b3; /* Darker blue on hover */
        }
        .password-requirements {
            margin-top: 20px;
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
        .show-password {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .show-password input[type="checkbox"] {
            margin-right: 8px;
        }
        #back{
            position: absolute;
            top: 5rem;
            left: 10rem;
        }
    </style>
</head>
<body>
<a href="index.php" class="btn btn-primary" id="back">Back to Homepage</a>
    <div class="login-box">
        <h2>Change Password</h2>
        <form method="post">
            <div class="user-box">
                <input type="password" name="currentpassword" required="">
                <label>Current Password</label>
            </div>
            <div class="user-box">
                <input type="password" name="newpassword" required="" id="newpassword">
                <label>New Password</label>
            </div>
            <div class="show-password">
                <input type="checkbox" id="showNewPassword"> Show Password
            </div>
            <div class="user-box">
                <input type="password" name="confirmpassword" required="" id="confirmpassword">
                <label>Confirm Password</label>
            </div>
            <div class="show-password">
                <input type="checkbox" id="showConfirmPassword"> Show Password
            </div>

            <div class="password-requirements">
                <p><strong>Password Requirements:</strong></p>
                <ul>
                    <li class="requirement"><input type="checkbox" id="length" disabled>At least 8 characters long</li>
                    <li class="requirement"><input type="checkbox" id="uppercase" disabled>Starts with a capital letter</li>
                    <li class="requirement"><input type="checkbox" id="special" disabled>No special characters allowed</li>
                </ul>
            </div>

            <button type="submit" name="submit" class="but">CHANGE PASSWORD</button>
        </form>
    </div>

    <script>
        const newPasswordInput = document.getElementById('newpassword');
        const confirmPasswordInput = document.getElementById('confirmpassword');
        const showNewPasswordCheckbox = document.getElementById('showNewPassword');
        const showConfirmPasswordCheckbox = document.getElementById('showConfirmPassword');

        const lengthCheckbox = document.getElementById('length');
        const uppercaseCheckbox = document.getElementById('uppercase');
        const specialCheckbox = document.getElementById('special');

        newPasswordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validateConfirmPassword);

        // Show Password functionality
        showNewPasswordCheckbox.addEventListener('change', function() {
            newPasswordInput.type = this.checked ? 'text' : 'password';
        });

        showConfirmPasswordCheckbox.addEventListener('change', function() {
            confirmPasswordInput.type = this.checked ? 'text' : 'password';
        });

        function validatePassword() {
            const value = newPasswordInput.value;

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
            if (newPasswordInput.value === confirmPasswordInput.value) {
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