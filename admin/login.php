<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $sql = "SELECT ID FROM tbladmin WHERE UserName=:username and Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['hbmsaid'] = $result->ID;
        }

        if (!empty($_POST["remember"])) {
            setcookie("user_login", $_POST["username"], time() + (10 * 365 * 24 * 60 * 60));
            setcookie("userpassword", $_POST["password"], time() + (10 * 365 * 24 * 60 * 60));
        } else {
            setcookie("user_login", "", time() - 3600);
            setcookie("userpassword", "", time() - 3600);
        }

        $_SESSION['login'] = $_POST['username'];
        echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Hotel Booking Management System | Login Page</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,500,700' rel='stylesheet' type='text/css' />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #2C3E50; /* Solid background color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            color: #fff;
        }
        .login-container {
            background: #ECF0F1; /* Light background for the login box */
            box-shadow: 0 15px 55px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 40px 30px;
            width: 350px;
            position: relative;
        }
        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #2C3E50; /* Dark text for contrast */
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            outline: none;
        }
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #2980B9; /* Primary button color */
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        input[type="submit"]:hover {
            background-color: #1A3E6D; /* Darker variant for hover */
            transform: translateY(-2px);
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #34495E; /* Darker text for checkbox */
        }
        .forget {
            text-align: center;
            margin-top: 15px;
        }
        .forget a {
            color: #2980B9;
            text-decoration: none;
        }
        .forget a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var passwordToggle = document.getElementById("show-password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.innerHTML = "Hide Password";
            } else {
                passwordInput.type = "password";
                passwordToggle.innerHTML = "Show Password";
            }
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h3>Admin Panel</h3>
        <form method="post" class="signin" action="">
            <input type="text" name="username" placeholder="Username"
                   required="true" value="<?php if (isset($_COOKIE["user_login"])) { echo $_COOKIE["user_login"]; } ?>">
            <input type="password" id="password" name="password" placeholder="Password"
                   required="true" value="<?php if (isset($_COOKIE["userpassword"])) { echo $_COOKIE["userpassword"]; } ?>">
				   <div class="checkbox-label">
                <input type="checkbox" id="show-password" onclick="togglePassword()">
                <label for="show-password">Show Password</label>
            </div>
            <div class="checkbox-label">
                <input type="checkbox" id="remember" name="remember" <?php if (isset($_COOKIE["user_login"])) { echo 'checked'; } ?>>
                <label for="remember">Keep me signed in</label>
            </div>
          
            <input type="submit" value="Sign in" name="login">
            <div class="forget">
                <a href="forgot-password.php">Forgot your password?</a>
            </div>
        </form>
    </div>
</body>
</html>