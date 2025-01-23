<?php
session_start();
include('includes/dbconnection.php');

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user data including hashed password and block status from the database
    $sql = "SELECT ID, Password, isBlocked, verify_status FROM tbluser WHERE Email = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if($user) {
        // Check if the user is blocked
        if($user['isBlocked'] == 1) {
            echo "<script>alert('Your account is blocked. Please contact the admin for assistance here @ phantomhivehotel7@gmail.com.');</script>";
        } else {
            // Verify the entered password against the stored hashed password
            if(password_verify($password, $user['Password'])) {
                // Check if the email is verified
                if($user['verify_status'] == 0) {
                    echo "<script>alert('Your email is not verified. Please verify your email to log in.');</script>";
                } else {
                    // Set session variables upon successful login
                    $_SESSION['hbmsuid'] = $user['ID']; // User ID
                    $_SESSION['login'] = $user['Email']; // User Email
                    header('Location: index.php'); // Redirect to index.php
                    exit(); // Stop further execution
                }
            } else {
                // Incorrect password
                echo "<script>alert('Incorrect password');</script>";
            }
        }
    } else {
        // User not found
        echo "<script>alert('User not found');</script>";
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Hotel Booking Management System | Login</title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <style>
        .forgot{
            width300px;
            height: 70px;
            /* border: 1px solid black; */
            display: flex;
            justify-content: center;
            gap:5px;
        }
        a{
            text-decoration: none;
        }
        a:hover{
            text-decoration: underline;
            color: blue;
        }
        input[type=email],input[type=password] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 28px;
  box-sizing: border-box;
}
#myInput{
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
h1{
    font-family: 'Rancho-Regular';
    text-align: center;
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
    <!-- Header -->
    <div class="header head-top">
        <div class="container">
            <?php include_once('includes/header.php');?>
        </div>
    </div>
    <!-- Login Form -->
    <div class="content-signin">
        <div class="contact">
            <div class="container">
                <h1>Sign in</h1>
                <!-- <h2>If you have an account with us, please log in.</h2> -->
                <div class="contact-grids-signin">
                    <div class="col-md-4 contact-signin">
                        <form method="post" class="signin">
                            <h5>Email Address</h5>
                            <input type="email" class="form-control" value="" name="email" required="true">
                            <h5>Password</h5>
                            <input type="password" value="" class="form-control" name="password" required="true" id="myInput">
                            <br />
                            <!-- An element to toggle between password visibility -->
                            <input type="checkbox" onclick="showPass()">Show Password
                            <br>
                            <div class="forgot">
                                <p>Forgot your password? click</p>
                                <a href="forgot-password.php" style="color: red;">here</a>
                                <p>to reset your password.</p>
                            </div>
                          
                            <br/>
                            <input type="submit" value="Login" name="login">
                        </form>
                        Don't have account yet? Register <a href="signup.php" style="color: red">here.</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <?php include_once('includes/getintouch.php');?>
    </div>
    <!-- Footer -->
    <?php include_once('includes/footer.php');?>
    <script>
        function showPass() {
  var x = document.getElementById("myInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
    </script>
</body>
</html>
