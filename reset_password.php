<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['reset_email'])) {
    // If reset_email session variable is not set, redirect to the forgot password page
    header('location: forgot-password.php');
    exit();
}

if (isset($_POST['submit'])) {
    $email = $_SESSION['reset_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else if (!isValidPassword($new_password)) {
        echo "<script>alert('Password must be at least 8 characters long, start with an uppercase letter, and contain no special characters.');</script>";
    } else {
        // Update the password in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE tbluser SET Password=:password WHERE Email=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            // Password updated successfully, redirect to login page
            unset($_SESSION['reset_email']); // Clear the reset_email session variable
            echo "<script>alert('Password reset successful. You can now login with your new password.');</script>";
            header('location: signin.php');
            exit();
        } else {
            echo "<script>alert('Password reset failed. Please try again later.');</script>";
        }
    }
}

function isValidPassword($password) {
    return preg_match('/^[A-Z][a-zA-Z0-9]{7,}$/', $password); // First letter must be uppercase, no special characters, at least 8 characters.
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        html,
        body {
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

        .show-password {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .show-password input {
            margin-right: 5px;
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
            cursor: default; /* Prevent cursor change */
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
        <h2>Reset Password</h2>
        <form method="post">
            <div class="user-box">
                <input type="password" name="new_password" id="new_password" required="" placeholder=" ">
                <label>New Password</label>
            </div>
            <div class="user-box">
                <input type="password" name="confirm_password" id="confirm_password" required="" placeholder=" ">
                <label>Confirm Password</label>
            </div>
            <div class="show-password">
                <input type="checkbox" id="show_password">
                <label for="show_password" style="color: #333;">Show Passwords</label>
            </div>
            <div class="password-requirements">
                <p><strong>Password Requirements:</strong></p>
                <ul>
                    <li class="requirement"><input type="checkbox" id="length" disabled>At least 8 characters long</li>
                    <li class="requirement"><input type="checkbox" id="uppercase" disabled>Starts with a capital letter</li>
                    <li class="requirement"><input type="checkbox" id="special" disabled>No special characters allowed</li>
                </ul>
            </div>
            <button type="submit" name="submit">Reset</button>
        </form>
    </div>

    <script>
        const showPasswordToggle = document.getElementById('show_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        const lengthCheckbox = document.getElementById('length');
        const uppercaseCheckbox = document.getElementById('uppercase');
        const specialCheckbox = document.getElementById('special');

        newPasswordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validateConfirmPassword);

        showPasswordToggle.addEventListener('change', () => {
            // Toggle the type of the password fields between 'password' and 'text'
            const type = showPasswordToggle.checked ? 'text' : 'password';
            newPasswordInput.type = type;
            confirmPasswordInput.type = type;
        });

        function validatePassword() {
            const value = newPasswordInput.value;

            // Length check
            if (value.length >= 8) {
                lengthCheckbox.checked = true;
                lengthCheckbox.parentElement.classList.add('checked');
                lengthCheckbox.parentElement.classList.remove('unchecked');
                newPasswordInput.classList.remove('label-error');
                newPasswordInput.classList.add('label-success');
            } else {
                lengthCheckbox.checked = false;
                lengthCheckbox.parentElement.classList.add('unchecked');
                lengthCheckbox.parentElement.classList.remove('checked');
                newPasswordInput.classList.add('label-error');
                newPasswordInput.classList.remove('label-success');
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