<?php
session_start();
include('includes/dbconnection.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user data including failed attempts, block status, block time, and last attempt time
    $sql = "SELECT ID, Password, isBlocked, verify_status, failed_attempts, last_attempt_time, block_time FROM tbluser WHERE Email = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $current_time = new DateTime();

        // Automatically unblock the user after 15 minutes
        if ($user['isBlocked'] == 1) {
            $block_time = new DateTime($user['block_time']);
            $interval_seconds = $current_time->getTimestamp() - $block_time->getTimestamp();

            if ($interval_seconds >= 900) { // 15 minutes = 900 seconds
                // Unblock the user
                $sql_unblock = "UPDATE tbluser SET isBlocked = 0, block_time = NULL, failed_attempts = 0 WHERE Email = :email";
                $query_unblock = $dbh->prepare($sql_unblock);
                $query_unblock->bindParam(':email', $email, PDO::PARAM_STR);
                $query_unblock->execute();
                $user['isBlocked'] = 0;
                $user['failed_attempts'] = 0;
            } else {
                echo "<script>alert('Your account is temporarily blocked. Please try again later.');</script>";
                exit();
            }
        }

        // Reset failed attempts after 30 minutes of inactivity
        if ($user['last_attempt_time']) {
            $last_attempt_time = new DateTime($user['last_attempt_time']);
            $interval_seconds = $current_time->getTimestamp() - $last_attempt_time->getTimestamp();

            if ($interval_seconds >= 1800) { // 30 minutes = 1800 seconds
                $sql_reset_attempts = "UPDATE tbluser SET failed_attempts = 0 WHERE Email = :email";
                $query_reset_attempts = $dbh->prepare($sql_reset_attempts);
                $query_reset_attempts->bindParam(':email', $email, PDO::PARAM_STR);
                $query_reset_attempts->execute();
                $user['failed_attempts'] = 0;
            }
        }

        // Verify password
        if (password_verify($password, $user['Password'])) {
            if ($user['verify_status'] == 0) {
                echo "<script>alert('Your email is not verified. Please verify your email to log in.');</script>";
            } else {
                // Successful login
                $sql_reset_attempts = "UPDATE tbluser SET failed_attempts = 0, last_attempt_time = NULL WHERE Email = :email";
                $query_reset_attempts = $dbh->prepare($sql_reset_attempts);
                $query_reset_attempts->bindParam(':email', $email, PDO::PARAM_STR);
                $query_reset_attempts->execute();

                $_SESSION['hbmsuid'] = $user['ID']; // User ID
                $_SESSION['login'] = $email; // User Email
                header('Location: index.php');
                exit();
            }
        } else {
            // Increment failed attempts
            $failed_attempts = $user['failed_attempts'] + 1;

            if ($failed_attempts >= 3) {
                // Block the user
                $sql_block = "UPDATE tbluser SET isBlocked = 1, block_time = NOW() WHERE Email = :email";
                $query_block = $dbh->prepare($sql_block);
                $query_block->bindParam(':email', $email, PDO::PARAM_STR);
                $query_block->execute();

                echo "<script>alert('Too many failed login attempts. Your account is temporarily blocked.');</script>";
              
            } else {
                // Update failed attempts and last attempt time
                $sql_update_attempts = "UPDATE tbluser SET failed_attempts = :failed_attempts, last_attempt_time = NOW() WHERE Email = :email";
                $query_update_attempts = $dbh->prepare($sql_update_attempts);
                $query_update_attempts->bindParam(':failed_attempts', $failed_attempts, PDO::PARAM_INT);
                $query_update_attempts->bindParam(':email', $email, PDO::PARAM_STR);
                $query_update_attempts->execute();

                echo "<script>alert('Incorrect password. You have " . (3 - $failed_attempts) . " attempt(s) remaining.');</script>";
            }
        }
    } else {
        echo "<script>alert('User not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .forgot-pass {
            margin-top: 10px;
        }

        .forgot-pass a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-text {
            color: #333;
            font-size: 14px;
            margin-top: 20px;
        }

        .footer-text a {
            color: #007bff;
            text-decoration: none;
        }

        .show-password {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .show-password input {
            margin-right: 5px;
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
    <h2>Login</h2>
    <form method="post">
        <div class="user-box">
            <input type="email" name="email" required="" placeholder=" ">
            <label>Email</label>
        </div>
        <div class="user-box">
            <input type="password" name="password" id="password" required="" placeholder=" ">
            <label>Password</label>
        </div>
        <div class="show-password">
            <input type="checkbox" id="show_pass">
            <label for="show_pass" style="color: #333;">Show Password</label>
        </div>
        <button type="submit" name="login">Log In</button>
    </form>
    
    <div class="forgot-pass">
        <a href="forgot-password.php">Forgot Password?</a>
    </div>

    <div class="footer-text">
        Don't have an account yet? Register <a href="signup.php">here.</a>
    </div>
</div>

<script>
    const showPasswordToggle = document.getElementById('show_pass');
    const passwordInput = document.getElementById('password');

    showPasswordToggle.addEventListener('change', function() {
        // Toggle the type of the password field between 'password' and 'text'
        passwordInput.type = this.checked ? 'text' : 'password';
    });
</script>

</body>
</html>